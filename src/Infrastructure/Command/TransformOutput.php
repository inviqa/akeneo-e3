<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Command;

use AkeneoE3\Domain\Result\Write\Failed;
use AkeneoE3\Domain\Result\Write\Loaded;
use AkeneoE3\Domain\Result\Write\Skipped;
use AkeneoE3\Domain\Result\Write\WriteResult;
use AkeneoE3\Infrastructure\Comparer\DiffLine;
use AkeneoE3\Infrastructure\Comparer\ResourceComparer;
use AkeneoE3\Infrastructure\Report\ProcessReport;
use LogicException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TransformOutput
{
    private ProgressBar $progressBar;

    private InputInterface $input;
    private OutputInterface $output;

    private ResourceComparer $resourceComparer;
    private ProcessReport $report;

    private bool $outputTransformations;

    private ConsoleSectionOutput $transformErrorSection;
    private ConsoleSectionOutput $transformReportSection;
    private ConsoleSectionOutput $loadErrorSection;

    private SymfonyStyle $style;

    public function __construct(
        InputInterface $input,
        OutputInterface $output
    ) {
        if (!$output instanceof ConsoleOutputInterface) {
            throw new LogicException('Console output must implement ConsoleOutputInterface');
        }

        $this->progressBar = new ProgressBar($output);
        $this->input = $input;
        $this->output = $output;

        $this->resourceComparer = new ResourceComparer();
        $this->report = new ProcessReport();

        $this->outputTransformations = (bool)$input->getOption('output-transform');

        $loadTableSection = $output->section();
        $this->transformReportSection = $output->section();
        $this->transformErrorSection = $output->section();
        $this->loadErrorSection = $output->section();

        $this->style = new SymfonyStyle($this->input, $loadTableSection);
    }


    public function askToConnect(string $host, bool $dryRun): bool
    {
        if ($dryRun === true) {
            return true;
        }

        $phrase = sprintf("You're going to connect to %s to transform data. Continue?", $host);

        return $this->style->confirm($phrase);
    }

    public function render(WriteResult $result, int $estimatedTotal): void
    {
        if ($this->progressBar->getMaxSteps() === 0) {
            $this->progressBar->setMaxSteps($estimatedTotal);
        }

        $this->progressBar->advance();

        $this->report->add($result);

        $this->progressBar->clear();

        $this->outputTransformSummary();
        $this->outputLoadSummary();
        $this->outputTransformation($result);

        $this->progressBar->display();
    }


    /**
     * @param array|DiffLine[] $comparison
     */
    private function outputCompareTable(array $comparison, WriteResult $loadResult): void
    {
        foreach ($comparison as $diff) {
            $this->style->definitionList(
                ['Identifier' => $diff->getCode()],
                ['Field' =>  $diff->getField()],
                ['Before' =>  $diff->getBefore()],
                ['After' =>  $diff->getAfter()]
            );
        }

        if ($loadResult instanceof Failed) {
            $this->style->warning((string)$loadResult);
        }

        if ($loadResult instanceof Loaded) {
            $this->style->success((string)$loadResult);
        }

        if ($loadResult instanceof Skipped) {
            $this->style->text('Skipped: ' . $loadResult->getResource()->getCode());
        }
    }

    private function outputTransformSummary(): void
    {
        $this->transformReportSection->overwrite(
            sprintf('Processed: %d', $this->report->total())
        );

        $errorSummary = $this->report->transformErrorSummary();
        if (count($errorSummary) !== 0) {
            $messages = $this->formatErrorSummary(
                $errorSummary,
                'Transform errors'
            );
            $this->transformErrorSection->overwrite($messages);
        }
    }

    private function outputLoadSummary(): void
    {
        $errorSummary = $this->report->loadErrorSummary();
        if (count($errorSummary) !== 0) {
            $messages = $this->formatErrorSummary($errorSummary, 'Load errors');
            $this->loadErrorSection->overwrite($messages);
        }
    }

    private function outputTransformation(WriteResult $loadResult): void
    {
        if ($this->outputTransformations === false) {
            return;
        }

        $comparison = $this->resourceComparer->compareWithOrigin(
            $loadResult->getResource()
        );

        $this->outputCompareTable($comparison, $loadResult);
    }

    private function formatErrorSummary(array $errorSummary, string $title): array
    {
        if (count($errorSummary) === 0) {
            return [];
        }

        $messages = [$title . ':'];
        foreach ($errorSummary as $message => $total) {
            $messages[] = sprintf(' - %s: %d', $message, $total);
        }

        return $messages;
    }
}

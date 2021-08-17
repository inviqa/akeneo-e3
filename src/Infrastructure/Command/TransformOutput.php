<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Command;

use AkeneoEtl\Domain\Load\Event\AfterLoadEvent;
use AkeneoEtl\Domain\Load\LoadResult\Failed;
use AkeneoEtl\Domain\Load\LoadResult\Loaded;
use AkeneoEtl\Domain\Load\LoadResult\LoadResult;
use AkeneoEtl\Domain\Transform\Event\AfterTransformEvent;
use AkeneoEtl\Domain\Transform\Progress;
use AkeneoEtl\Infrastructure\Comparer\DiffLine;
use AkeneoEtl\Infrastructure\Comparer\ResourceComparer;
use AkeneoEtl\Infrastructure\Report\ProcessReport;
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

    public function outputAfterTransform(AfterTransformEvent $event): void
    {
        $this->report->add($event->getResource());
        $this->report->addTransformResult($event->getTransformResult());

        $this->outputProgress($event->getProgress());

        $this->progressBar->clear();

        $this->outputTransformSummary();
        if ($event->getProgress()->current() === $event->getProgress()->total()) {
            $this->outputLoadSummary();
        }

        $this->progressBar->display();
    }

    public function outputAfterLoad(AfterLoadEvent $event): void
    {
        foreach ($event->getLoadResults() as $loadResult) {
            $this->report->addLoadResult($loadResult);
        }

        $this->progressBar->clear();

        $this->outputLoadSummary();
        $this->outputLoadResults($event->getLoadResults());

        $this->progressBar->display();
    }

    /**
     * @param array|DiffLine[] $comparison
     */
    public function outputCompareTable(array $comparison, LoadResult $loadResult): void
    {
        if (count($comparison) === 0) {
            return;
        }

        foreach ($comparison as $diff) {
            $this->style->definitionList(
                ['Identifier' => $diff->getCode()],
                ['Field' =>  $diff->getField()->getName()],
                ['Before' =>  $diff->getBefore()],
                ['After' =>  $diff->getAfter()]
            );
        }

        if ($loadResult instanceof Failed) {
            $this->style->warning($loadResult->getError());
        }

        if ($loadResult instanceof Loaded) {
            $this->style->success('updated');
        }
    }

    public function outputTransformSummary(): void
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

    public function outputLoadSummary(): void
    {
        $errorSummary = $this->report->loadErrorSummary();
        if (count($errorSummary) !== 0) {
            $messages = $this->formatErrorSummary($errorSummary, 'Load errors');
            $this->loadErrorSection->overwrite($messages);
        }
    }

    public function outputProgress(Progress $progress): void
    {
        if ($this->progressBar->getMaxSteps() === 0) {
            $this->progressBar->setMaxSteps($progress->total());
        }

        $this->progressBar->setProgress($progress->current());
    }

    /**
     * @param array|LoadResult[] $loadResults
     */
    public function outputLoadResults(array $loadResults): void
    {
        if ($this->outputTransformations === false) {
            return;
        }

        foreach ($loadResults as $loadResult) {
            $comparison = $this->resourceComparer->compareWithOrigin(
                $loadResult->getResource()
            );
            $this->outputCompareTable($comparison, $loadResult);
        }
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

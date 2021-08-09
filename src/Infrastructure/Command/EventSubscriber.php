<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Command;

use AkeneoEtl\Domain\Load\Event\LoadErrorEvent;
use AkeneoEtl\Domain\Transform\Event\AfterTransformEvent;
use AkeneoEtl\Domain\Transform\Event\TransformErrorEvent;
use AkeneoEtl\Infrastructure\Command\Compare\ConsoleTableFormatter;
use LogicException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventSubscriber
{
    private EventDispatcherInterface $eventDispatcher;

    private ProgressBar $progressBar;

    private InputInterface $input;

    private OutputInterface $output;

    private ResourceComparer $resourceComparer;

    private ConsoleTableFormatter $tableFormatter;

    private bool $outputTransformations;
    private bool $outputTransformErrors;
    private bool $outputLoadErrors;

    private ConsoleSectionOutput $transformErrorSection;
    private ConsoleSectionOutput $transformProgressSection;
    private ConsoleSectionOutput $transformReportSection;

    private array $transformErrors = [];
    private int $processedCount = 0;

    /**
     * @var array|int[]
     */
    private array $tableColumnWidths;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        InputInterface $input,
        OutputInterface $output
    ) {
        if (!$output instanceof ConsoleOutputInterface) {
            throw new LogicException('Console output must implement ConsoleOutputInterface');
        }

        $this->eventDispatcher = $eventDispatcher;
        $this->progressBar = new ProgressBar($output);
        $this->input = $input;
        $this->output = $output;
        $this->resourceComparer = new ResourceComparer();
        $this->tableColumnWidths = [12, 16, 30, 30];
        $this->tableFormatter = new ConsoleTableFormatter($this->tableColumnWidths);

        $this->outputTransformations = (bool)$input->getOption('output-transform');
        $this->outputTransformErrors = (bool)$input->getOption('output-transform-errors');
        $this->outputLoadErrors = (bool)$input->getOption('output-load-errors');

        $this->transformProgressSection = $output->section();
        $this->transformReportSection = $output->section();
        $this->transformErrorSection = $output->section();

        $this->eventDispatcher->addListener(AfterTransformEvent::class, [$this, 'onProgress']);
        $this->eventDispatcher->addListener(TransformErrorEvent::class, [$this, 'onTransformError']);
    }

    public function onProgress(AfterTransformEvent $event): void
    {
        if ($this->progressBar->getMaxSteps() === 0) {
            $this->progressBar->setMaxSteps($event->getProgress()->total());
        }

        $this->progressBar->setProgress($event->getProgress()->current());

        // If there is no transformed object, skip
        if ($event->getAfter() === null) {
            return;
        }

        $this->progressBar->clear();
        $this->transformReportSection->overwrite(sprintf('Processed: %d', ++$this->processedCount));
        $this->transformProgressSection->writeln(
            $this->processedCount
        );

        if ($this->outputTransformations === true) {
            $comparison = $this->resourceComparer->getCompareTable(
                $event->getBefore(),
                $event->getAfter()
            );

            $this->outputCompareTable($comparison);
        }
        $this->progressBar->display();
    }

    public function onTransformError(TransformErrorEvent $event): void
    {
        if ($this->outputTransformErrors === false) {
            return;
        }

        $this->transformErrors[$event->getMessage()][] = $event->getResource()->getCodeOrIdentifier() ?? '';

        $messages = [];
        foreach ($this->transformErrors as $message => $ids) {
            $messages[] = sprintf('%s: %d', $message, count($ids));
        }

        $this->progressBar->clear();
        $this->transformErrorSection->overwrite($messages);
        $this->progressBar->display();
    }

    public function onLoadError(LoadErrorEvent $event): void
    {
        foreach ($event->getErrors() as $error) {
            $this->output->writeln(
                sprintf(
                    '[%s] %s',
                    $error->getIdentifier(),
                    $error->getMessage()
                )
            );
        }
    }

    private function outputCompareTable(array $comparison): void
    {
        if (count($comparison) === 0) {
            return;
        }

        $formattedTable = $this->tableFormatter->format($comparison);
        $this->transformProgressSection->writeln($formattedTable);

        if (count($formattedTable) > 0) {
            $this->transformProgressSection->writeln(str_pad('', strlen($formattedTable[0]), '-'));
        }
    }
}

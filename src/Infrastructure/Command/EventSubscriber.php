<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Command;

use AkeneoEtl\Domain\Load\Event\AfterLoadEvent;
use AkeneoEtl\Domain\Load\LoadResult\Failed;
use AkeneoEtl\Domain\Resource\Resource;
use AkeneoEtl\Domain\Transform\Event\AfterTransformEvent;
use AkeneoEtl\Domain\Transform\Event\TransformErrorEvent;
use AkeneoEtl\Domain\Transform\TransformResult\Failed as TransformFailed;
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

    private ConsoleSectionOutput $transformErrorSection;
    private ConsoleSectionOutput $transformProgressSection;
    private ConsoleSectionOutput $transformReportSection;

    private array $transformErrors = [];
    private int $processedCount = 0;

    /**
     * @var array|int[]
     */
    private array $tableColumnWidths;

    private function __construct(
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
        $this->tableColumnWidths = [12, 16, 30, 30, 30];
        $this->tableFormatter = new ConsoleTableFormatter($this->tableColumnWidths);

        $this->outputTransformations = (bool)$input->getOption('output-transform');
        $this->outputTransformErrors = (bool)$input->getOption('output-transform-errors');

        $this->transformProgressSection = $output->section();
        $this->transformReportSection = $output->section();
        $this->transformErrorSection = $output->section();

        $this->eventDispatcher->addListener(AfterTransformEvent::class, [$this, 'onAfterTransform']);
        $this->eventDispatcher->addListener(AfterLoadEvent::class, [$this, 'onAfterLoad']);
    }

    public static function init(
        EventDispatcherInterface $eventDispatcher,
        InputInterface $input,
        OutputInterface $output
    ): self {
        return new self($eventDispatcher, $input, $output);
    }

    public function onAfterTransform(AfterTransformEvent $event): void
    {
        if ($this->progressBar->getMaxSteps() === 0) {
            $this->progressBar->setMaxSteps($event->getProgress()->total());
        }

        $this->progressBar->setProgress($event->getProgress()->current());

        if ($event->getResource() === null) {
            return;
        }

        $this->progressBar->clear();

        // output total
        $this->transformReportSection->overwrite(sprintf('Processed: %d', ++$this->processedCount));

        // output transform error stats
        $transformResult = $event->getTransformResult();
        if ($transformResult instanceof TransformFailed) {
            $transformError = $transformResult->getError();
            $this->transformErrors[$transformError][] = $event->getResource()->getCodeOrIdentifier() ?? '';

            $messages = [];
            foreach ($this->transformErrors as $message => $ids) {
                $messages[] = sprintf('%s: %d', $message, count($ids));
            }

            $this->transformErrorSection->overwrite($messages);
        }

        $this->progressBar->display();
    }

    public function onAfterLoad(AfterLoadEvent $event): void
    {
        if ($this->outputTransformations === false) {
            return;
        }

        foreach ($event->getLoadResults() as $loadResult) {
            $comparison = $this->getCompareTable($loadResult->getResource());
            $comparison[0][] = ($loadResult instanceof Failed) ?
                str_replace("\n", '', $loadResult->getError()) :
                'updated';

            $this->outputCompareTable($comparison);
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

    private function getCompareTable(Resource $resource): array
    {
        if ($resource->getOrigin() === null) {
            return $this->resourceComparer->getCompareTable(null, $resource);
        }

        return $this->resourceComparer->getCompareTable(
            $resource->getOrigin()->diff($resource),
            $resource->diff($resource->getOrigin())
        );
    }
}

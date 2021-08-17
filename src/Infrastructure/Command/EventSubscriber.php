<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Command;

use AkeneoEtl\Domain\Load\Event\AfterLoadEvent;
use AkeneoEtl\Domain\Load\LoadResult\Failed;
use AkeneoEtl\Domain\Load\LoadResult\Loaded;
use AkeneoEtl\Domain\Load\LoadResult\LoadResult;
use AkeneoEtl\Domain\Transform\Event\AfterTransformEvent;
use AkeneoEtl\Domain\Transform\TransformResult\Failed as TransformFailed;
use AkeneoEtl\Infrastructure\Comparer\DiffLine;
use AkeneoEtl\Infrastructure\Comparer\ResourceComparer;
use LogicException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventSubscriber
{
    private EventDispatcherInterface $eventDispatcher;

    private ProgressBar $progressBar;

    private InputInterface $input;

    private OutputInterface $output;

    private ResourceComparer $resourceComparer;

    private bool $outputTransformations;

    private ConsoleSectionOutput $transformErrorSection;
    private ConsoleSectionOutput $transformReportSection;

    private array $transformErrors = [];
    private int $processedCount = 0;

    private SymfonyStyle $style;

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
        $this->style = new SymfonyStyle($this->input, $this->output);

        $this->resourceComparer = new ResourceComparer();

        $this->outputTransformations = (bool)$input->getOption('output-transform');

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
            $this->transformErrors[$transformError][] = $event->getResource()->getCode() ?? '';

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
            $comparison = $this->resourceComparer->compareWithOrigin($loadResult->getResource());
            $this->outputCompareTable($comparison, $loadResult);
        }
    }

    /**
     * @param array|DiffLine[] $comparison
     */
    private function outputCompareTable(array $comparison, LoadResult $loadResult): void
    {
        if (count($comparison) === 0) {
            return;
        }

        $this->progressBar->clear();

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

        $this->progressBar->display();
    }
}

<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Command;

use AkeneoEtl\Domain\Load\Event\LoadErrorEvent;
use AkeneoEtl\Domain\Resource;
use AkeneoEtl\Domain\Transform\Event\AfterTransformEvent;
use AkeneoEtl\Domain\Transform\Event\TransformErrorEvent;
use LogicException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventSubscriber
{
    private EventDispatcherInterface $eventDispatcher;

    private ProgressBar $progressBar;

    private OutputInterface $output;

    private Table $table;

    private ResourceComparer $resourceComparer;

    public function __construct(EventDispatcherInterface $eventDispatcher, ProgressBar $progressBar, OutputInterface $output)
    {
        if (!$output instanceof ConsoleOutputInterface) {
            throw new LogicException('Console output must implement ConsoleOutputInterface');
        }

        $this->eventDispatcher = $eventDispatcher;
        $this->progressBar = $progressBar;
        $this->output = $output;
        $section = $output->section();

        $this->table = new Table($section);
        $this->table->setColumnWidth(0, 10);
        $this->table->setColumnWidth(1, 10);
        $this->table->setColumnWidth(2, 10);

        $this->resourceComparer = new ResourceComparer();

        $this->eventDispatcher->addListener(AfterTransformEvent::class, [$this, 'onProgress']);
        $this->eventDispatcher->addListener(TransformErrorEvent::class, [$this, 'onTransformError']);
    }

    public function onProgress(AfterTransformEvent $event): void
    {
        if ($this->progressBar->getMaxSteps() === 0) {
            $this->progressBar->setMaxSteps($event->getProgress()->total());
        }

        $this->progressBar->setProgress($event->getProgress()->current());

        if ($event->getAfter() !== null) {
            $this->progressBar->clear();

            $comparison = $this->resourceComparer->getCompareTable($event->getBefore(), $event->getAfter());
            foreach ($comparison as $item) {
                $this->table->appendRow($item);
            }

            if (count($comparison) > 0) {
                $this->table->appendRow(new TableSeparator());
            }

            $this->progressBar->display();
        }
    }

    public function onTransformError(TransformErrorEvent $event): void
    {
//        $this->progressBar->clear();
//        $this->output->writeln(sprintf('%s', $event->getMessage()));
//        $this->progressBar->display();
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

    private function normaliseResource(?Resource $resource): string
    {
        if ($resource === null) {
            return '';
        }

        return implode(PHP_EOL, $this->normaliser->normalise($resource));
    }
}

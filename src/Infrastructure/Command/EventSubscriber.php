<?php

namespace AkeneoEtl\Infrastructure\Command;

use AkeneoEtl\Domain\Load\Event\LoadErrorEvent;
use AkeneoEtl\Domain\Resource;
use AkeneoEtl\Domain\Transform\Event\AfterTransformEvent;
use AkeneoEtl\Domain\Transform\Event\TransformErrorEvent;
use LogicException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventSubscriber
{
    private EventDispatcherInterface $eventDispatcher;

    private ProgressBar $progressBar;

    private OutputInterface $output;

    private Table $table;

    private ResourceNormaliser $normaliser;

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
        $this->normaliser = new ResourceNormaliser();

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

//            $this->output->writeln(sprintf(
//                '[[ %s ]]',
//                $event->getAfter()->getCodeOrIdentifier()
//            ));

//            if ($this->table === null) {
//                $this->table = new Table($this->output);
//            }
            $this->table->appendRow([
                $this->normaliseResource($event->getBefore()),
                $this->normaliseResource($event->getAfter()),

            ]);
//            $this->table->render();

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

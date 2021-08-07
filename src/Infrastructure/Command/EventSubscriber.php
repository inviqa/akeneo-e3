<?php

namespace AkeneoEtl\Infrastructure\Command;

use AkeneoEtl\Domain\Load\Event\LoadErrorEvent;
use AkeneoEtl\Domain\Transform\Event\AfterTransformEvent;
use AkeneoEtl\Domain\Transform\Event\TransformErrorEvent;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventSubscriber
{
    private EventDispatcherInterface $eventDispatcher;

    private ProgressBar $progressBar;

    private OutputInterface $output;

    public function __construct(EventDispatcherInterface $eventDispatcher, ProgressBar $progressBar, OutputInterface $output)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->progressBar = $progressBar;
        $this->output = $output;

        $this->eventDispatcher->addListener(AfterTransformEvent::class, [$this, 'onProgress']);
        $this->eventDispatcher->addListener(TransformErrorEvent::class, [$this, 'onTransformError']);
    }

    public function onProgress(AfterTransformEvent $event): void
    {
        if ($this->progressBar->getMaxSteps() === 0) {
            $this->progressBar->setMaxSteps($event->getTotal());
        }

        $this->progressBar->setProgress($event->getIndex());

        if ($event->getAfter() !== null) {
            $this->progressBar->clear();
            $this->output->writeln(sprintf(
                '[[ %s ]]',
                $event->getAfter()->getCodeOrIdentifier()
            ));
            $this->progressBar->display();
        }
    }

    public function onTransformError(TransformErrorEvent $event): void
    {
        $this->progressBar->clear();
        $this->output->writeln(sprintf('%s', $event->getMessage()));
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
}

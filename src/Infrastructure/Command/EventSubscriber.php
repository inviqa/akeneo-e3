<?php

namespace AkeneoEtl\Infrastructure\Command;

use AkeneoEtl\Domain\Transform\Event\ProgressEvent;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class EventSubscriber
{
    private EventDispatcherInterface $eventDispatcher;

    private ProgressBar $progressBar;

    private OutputInterface $output;

    public function __construct(EventDispatcherInterface $eventDispatcher, ProgressBar $progressBar, OutputInterface $output)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->progressBar = $progressBar;

        $this->eventDispatcher->addListener(ProgressEvent::class, [$this, 'onProgress']);
        $this->output = $output;
    }

    public function onProgress(ProgressEvent $event): void
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
}

<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Command;

use AkeneoEtl\Domain\Load\Event\AfterLoadEvent;
use AkeneoEtl\Domain\Transform\Event\AfterTransformEvent;
use LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventSubscriber
{
    private EventDispatcherInterface $eventDispatcher;

    private TransformOutput $output;

    private function __construct(
        EventDispatcherInterface $eventDispatcher,
        TransformOutput $output
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->output = $output;

        $this->eventDispatcher->addListener(AfterTransformEvent::class, [$this, 'onAfterTransform']);
        $this->eventDispatcher->addListener(AfterLoadEvent::class, [$this, 'onAfterLoad']);
    }

    public static function init(
        EventDispatcherInterface $eventDispatcher,
        TransformOutput $transformOutput
    ): self {
        return new self($eventDispatcher, $transformOutput);
    }

    public function onAfterTransform(AfterTransformEvent $event): void
    {
        $this->output->outputAfterTransform($event);
    }

    public function onAfterLoad(AfterLoadEvent $event): void
    {
        $this->output->outputAfterLoad($event);
    }
}

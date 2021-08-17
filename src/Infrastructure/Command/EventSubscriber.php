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
use AkeneoEtl\Infrastructure\Report\ProcessReport;
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

    private TransformOutput $output;

    private function __construct(
        EventDispatcherInterface $eventDispatcher,
        InputInterface $input,
        OutputInterface $output
    ) {
        if (!$output instanceof ConsoleOutputInterface) {
            throw new LogicException('Console output must implement ConsoleOutputInterface');
        }

        $this->eventDispatcher = $eventDispatcher;
        $this->output = new TransformOutput($input, $output);

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
        $this->output->outputAfterTransform($event);
    }

    public function onAfterLoad(AfterLoadEvent $event): void
    {
        $this->output->outputAfterLoad($event);
    }
}

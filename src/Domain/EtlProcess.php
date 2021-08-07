<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain;

use AkeneoEtl\Domain\Hook\Hooks;
use AkeneoEtl\Domain\Transform\Event\ProgressEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class EtlProcess
{
    private Extractor $extractor;
    private Transformer $transformer;
    private Loader $loader;
    private Hooks $hooks;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(Extractor $extractor, Transformer $transformer,
        Loader $loader, Hooks $hooks, EventDispatcherInterface $eventDispatcher)
    {
        $this->extractor = $extractor;
        $this->transformer = $transformer;
        $this->loader = $loader;
        $this->hooks = $hooks;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function execute(): void
    {
        $index = 0;
        $count = $this->extractor->count();

        //$this->hooks->onActionProgress(ProgressEvent::create(0, $count));

        $resources = $this->extractor->extract();

        foreach ($resources as $resource) {
            //setCurrentActionResource($resource);
            // @todo: implement as an event and move holder to Application
            CurrentResourceHolder::$current = $resource;

            $transformedResource = $this->transformer->transform($resource);

            $patch = null;
            if ($transformedResource->isChanged() === true) {
                $patch = $transformedResource->diff($resource);

                $this->loader->load($patch);
            }

            $this->eventDispatcher->dispatch(ProgressEvent::create($index++, $count, $patch, $resource));
        }

        $this->loader->finish();
    }
}

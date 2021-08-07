<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain;

use AkeneoEtl\Domain\Exception\LoadException;
use AkeneoEtl\Domain\Exception\TransformException;
use AkeneoEtl\Domain\Load\Event\LoadErrorEvent;
use AkeneoEtl\Domain\Transform\Event\AfterTransformEvent;
use AkeneoEtl\Domain\Transform\Event\BeforeTransformEvent;
use AkeneoEtl\Domain\Transform\Event\TransformErrorEvent;
use Exception;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class EtlProcess
{
    private Extractor $extractor;

    private Transformer $transformer;

    private Loader $loader;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        Extractor $extractor,
        Transformer $transformer,
        Loader $loader,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->extractor = $extractor;
        $this->transformer = $transformer;
        $this->loader = $loader;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function execute(): void
    {
        $index = 0;
        $count = $this->extractor->count();

        $resources = $this->extractor->extract();

        foreach ($resources as $resource) {
            // @todo: implement as an event and move holder to Application
            //CurrentResourceHolder::$current = $resource;
            $this->eventDispatcher->dispatch(BeforeTransformEvent::create($index++, $count, $resource));

            $transformedResource = $this->transform($resource);
            $loadingResource = $this->getResourceForLoad($transformedResource, $resource);
            $this->load($loadingResource);

            $this->eventDispatcher->dispatch(AfterTransformEvent::create($index++, $count, $loadingResource, $resource));
        }

        $this->finishLoading();
    }

    /**
     * @throws \AkeneoEtl\Domain\Exception\TransformException
     */
    private function transform(Resource $resource): ?\AkeneoEtl\Domain\Resource
    {
        $result = null;
        try {
            $result = $this->transformer->transform($resource);
        } catch (TransformException $e) {
            $this->dispatchTransformEvent($e);

            if ($e->canBeSkipped() === false) {
                throw $e;
            }
        }

        return $result;
    }

    private function getResourceForLoad(?Resource $transformedResource, Resource $resource): ?\AkeneoEtl\Domain\Resource
    {
        if ($transformedResource === null || $transformedResource->isChanged() === false) {
            return null;
        }

        return $transformedResource->diff($resource);
    }

    private function load(?Resource $loadingResource): void
    {
        if ($loadingResource === null) {
            return;
        }

        try {
            $this->loader->load($loadingResource);
        } catch (LoadException $e) {
            $this->dispatchLoadEvent($e);
        }
    }

    private function finishLoading(): void
    {
        try {
            $this->loader->finish();
        } catch (LoadException $e) {
            $this->dispatchLoadEvent($e);
        }
    }

    private function dispatchTransformEvent(Exception $exception): void
    {
        $this->eventDispatcher->dispatch(
            TransformErrorEvent::create($exception->getMessage())
        );
    }

    private function dispatchLoadEvent(LoadException $exception): void
    {
        $this->eventDispatcher->dispatch(
            LoadErrorEvent::create($exception->getLoadErrors())
        );
    }
}

<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain;

use AkeneoEtl\Domain\Exception\LoadException;
use AkeneoEtl\Domain\Exception\TransformException;
use AkeneoEtl\Domain\Load\Event\LoadErrorEvent;
use AkeneoEtl\Domain\Transform\Event\AfterTransformEvent;
use AkeneoEtl\Domain\Transform\Event\BeforeTransformEvent;
use AkeneoEtl\Domain\Transform\Event\TransformErrorEvent;
use AkeneoEtl\Domain\Transform\Progress;
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
        $progress = Progress::create($this->extractor->count());
        $resources = $this->extractor->extract();

        foreach ($resources as $resource) {
            $this->onBeforeTransform($progress, $resource);

            $result = $this->transform($resource);
            $patch = $this->getResourceForLoad($result, $resource);
            $this->load($patch);

            $this->onAfterTransform($progress->advance(), $patch, $this->getInitialData($result, $resource));
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
            $this->onTransformError($e, $resource);

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

    private function getInitialData(?Resource $transformedResource, Resource $resource): ?\AkeneoEtl\Domain\Resource
    {
        if ($transformedResource === null || $transformedResource->isChanged() === false) {
            return null;
        }

        return $resource->diff($transformedResource);
    }

    private function load(?Resource $loadingResource): void
    {
        if ($loadingResource === null) {
            return;
        }

        try {
            $this->loader->load($loadingResource);
        } catch (LoadException $e) {
            $this->onLoadError($e);
        }
    }

    private function finishLoading(): void
    {
        try {
            $this->loader->finish();
        } catch (LoadException $e) {
            $this->onLoadError($e);
        }
    }

    private function onTransformError(Exception $exception, Resource $resource): void
    {
        $this->eventDispatcher->dispatch(
            TransformErrorEvent::create($resource, $exception->getMessage())
        );
    }

    private function onLoadError(LoadException $exception): void
    {
        $this->eventDispatcher->dispatch(
            LoadErrorEvent::create($exception->getLoadErrors())
        );
    }

    private function onBeforeTransform(Progress $progress, Resource $resource): void
    {
        $this->eventDispatcher->dispatch(
            BeforeTransformEvent::create($progress, $resource)
        );
    }

    private function onAfterTransform(Progress $progress, ?Resource $loadingResource, ?Resource $initialResource): void
    {
        $this->eventDispatcher->dispatch(
            AfterTransformEvent::create($progress, $loadingResource, $initialResource)
        );
    }
}

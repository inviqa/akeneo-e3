<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain;

use AkeneoEtl\Domain\Exception\TransformException;
use AkeneoEtl\Domain\Load\Event\AfterLoadEvent;
use AkeneoEtl\Domain\Resource\Resource;
use AkeneoEtl\Domain\Transform\Event\AfterTransformEvent;
use AkeneoEtl\Domain\Transform\Event\BeforeTransformEvent;
use AkeneoEtl\Domain\Transform\Progress;
use AkeneoEtl\Domain\Transform\TransformResult\Failed;
use AkeneoEtl\Domain\Transform\TransformResult\Transformed;
use AkeneoEtl\Domain\Transform\TransformResult\TransformResult;
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

        foreach ($resources as $initialResource) {
            $resource = Resource::fromResource($initialResource);
            $isOk = $this->transform($resource, $progress);

            if ($isOk === true) {
                $this->load($resource);
            }
        }

        $this->finishLoading();
    }

    /**
     * @throws \AkeneoEtl\Domain\Exception\TransformException
     */
    private function transform(Resource $resource, Progress $progress): bool
    {
        $this->onBeforeTransform($progress, $resource);

        $result = Transformed::create($resource);

        try {
            $this->transformer->transform($resource);
        } catch (TransformException $e) {
            $result = Failed::create($resource, $e->getMessage());

            if ($e->canBeSkipped() === false) {
                throw $e;
            }
        } finally {
            $this->onAfterTransform($progress->advance(), $resource, $result);
        }

        return (!$result instanceof Failed);
    }

    private function load(Resource $resource): void
    {
        $results = $this->loader->load($resource);

        $this->onAfterLoad($results);
    }

    private function finishLoading(): void
    {
        $results = $this->loader->finish();

        $this->onAfterLoad($results);
    }

    private function onBeforeTransform(Progress $progress, Resource $resource): void
    {
        $this->eventDispatcher->dispatch(
            BeforeTransformEvent::create($progress, $resource)
        );
    }

    private function onAfterTransform(Progress $progress, Resource $resource, TransformResult $result): void
    {
        $this->eventDispatcher->dispatch(
            AfterTransformEvent::create($progress, $resource, $result)
        );
    }

    private function onAfterLoad(array $results): void
    {
        $this->eventDispatcher->dispatch(
            AfterLoadEvent::create($results)
        );
    }
}

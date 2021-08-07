<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain;

use AkeneoEtl\Domain\Hook\ActionProgress;
use AkeneoEtl\Domain\Hook\Hooks;

class EtlProcess
{
    private Extractor $extractor;
    private Transformer $transformer;
    private Loader $loader;
    private Hooks $hooks;

    public function __construct(Extractor $extractor, Transformer $transformer, Loader $loader, Hooks $hooks)
    {
        $this->extractor = $extractor;
        $this->transformer = $transformer;
        $this->loader = $loader;
        $this->hooks = $hooks;
    }

    public function execute(): void
    {
        $index = 0;
        $count = $this->extractor->count();

        $this->hooks->onActionProgress(ActionProgress::create(0, $count));

        $resources = $this->extractor->extract();

        foreach ($resources as $resource) {
            //setCurrentActionResource($resource);
            CurrentResourceHolder::$current = $resource;

            $transformedResource = $this->transformer->transform($resource);

            if ($transformedResource->isChanged() === true) {
                $patch = $transformedResource->diff($resource);

                $this->loader->queue($patch);
            }

            $this->hooks->onActionProgress(ActionProgress::create($index++, $count));
        }

        $this->loader->load();
    }
}

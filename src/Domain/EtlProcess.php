<?php

namespace AkeneoEtl\Domain;

use AkeneoEtl\Domain\Hook\ActionProgress;
use AkeneoEtl\Domain\Hook\Hooks;
use function AkeneoEtl\Application\Expression\Functions\setCurrentActionResource;

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
            setCurrentActionResource($resource);

            $patch = $this->transformer->transform($resource);
            // @todo: in no patch method, then merge to $resource

            if ($patch->isChanged() === true) {
                $this->loader->queue($patch->changes());
            }

            $this->hooks->onActionProgress(ActionProgress::create($index++, $count));
        }

        $this->loader->load();
    }
}

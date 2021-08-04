<?php

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

        foreach ($resources as $product) {
            $patch = $this->transformer->transform($product);
            // @todo: in no patch method, then merge to $product

            if ($patch !== null) {
                $this->loader->queue($patch);
            }

            $this->hooks->onActionProgress(ActionProgress::create($index++, $count));
        }

        $this->loader->load();
    }
}

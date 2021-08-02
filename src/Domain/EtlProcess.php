<?php

namespace AkeneoEtl\Domain;

use Closure;

class EtlProcess
{
    private Extractor $extractor;

    private Transformer $transformer;

    private Loader $loader;

    public function __construct(Extractor $extractor, Transformer $transformer, Loader $loader)
    {
        $this->extractor = $extractor;
        $this->transformer = $transformer;
        $this->loader = $loader;
    }

    public function execute(Closure $progressCallback, Closure $traceCallBack)
    {
        $index = 0;
        $count = $this->extractor->count();

        $progressCallback(0, $count);

        $products = $this->extractor->extract();

        foreach ($products as $product) {
            $patch = $this->transformer->transform($product, $traceCallBack);
            // @todo: in no patch method, then merge to $product

            if ($patch !== null) {
                $this->loader->addToBatch($patch);
            }

            $progressCallback($index++, $count);
        }

        $this->loader->flushBatch();
    }
}

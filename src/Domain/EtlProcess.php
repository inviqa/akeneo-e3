<?php

declare(strict_types=1);

namespace AkeneoE3\Domain;

use AkeneoE3\Domain\Load\LoadResult\LoadResult;

final class EtlProcess
{
    private Extractor $extractor;

    private IterableTransformer $transformer;

    private IterableLoader $loader;

    public function __construct(
        Extractor $extractor,
        IterableTransformer $transformer,
        IterableLoader $loader
    ) {
        $this->extractor = $extractor;
        $this->transformer = $transformer;
        $this->loader = $loader;
    }

    /**
     * @return LoadResult[]
     *
     * @throws \AkeneoE3\Domain\Exception\TransformException
     */
    public function execute(): iterable
    {
        $resources = $this->extractor->extract();

        $transformResults = $this->transformer->transform($resources);

        $loadResults = $this->loader->load($transformResults);

        yield from $loadResults;
    }

    public function total(): int
    {
        return $this->extractor->count();
    }
}

<?php

declare(strict_types=1);

namespace AkeneoE3\Domain;

use AkeneoE3\Domain\Load\LoadResult\LoadResult;
use AkeneoE3\Domain\Repository\ReadRepository;
use AkeneoE3\Domain\Resource\ResourceType;

final class EtlProcess
{
    private IterableExtractor $extractor;

    private IterableTransformer $transformer;

    private IterableLoader $loader;

    public function __construct(
        IterableExtractor $extractor,
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

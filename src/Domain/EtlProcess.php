<?php

declare(strict_types=1);

namespace AkeneoE3\Domain;

use AkeneoE3\Domain\Result\Write\WriteResult;

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
     * @return WriteResult[]
     *
     * @throws \AkeneoE3\Domain\Exception\TransformException
     */
    public function execute(): iterable
    {
        $resources = $this->extractor->extract();

        $transformResults = $this->transformer->transform($resources);

        yield from $this->loader->load($transformResults);
    }

    public function total(): int
    {
        return $this->extractor->count();
    }
}

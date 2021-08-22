<?php

namespace AkeneoE3\Infrastructure\Extractor;

use AkeneoE3\Domain\Extractor;
use AkeneoE3\Domain\Resource\Resource;

class ApiExtractor implements Extractor
{
    private ExtractConnector $connector;

    public function __construct(ExtractConnector $connector)
    {
        $this->connector = $connector;
    }

    public function count(): int
    {
        return $this->connector->count();
    }

    /**
     * @return iterable<Resource>
     */
    public function extract(): iterable
    {
        return $this->connector->extract();
    }
}

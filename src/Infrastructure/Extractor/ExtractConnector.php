<?php

namespace AkeneoE3\Infrastructure\Extractor;

use AkeneoE3\Domain\Resource\Resource;

interface ExtractConnector
{
    public function count(): int;

    /**
     * @return iterable<Resource>
     */
    public function extract(): iterable;
}

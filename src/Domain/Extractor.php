<?php

declare(strict_types=1);

namespace AkeneoE3\Domain;

use AkeneoE3\Domain\Resource\Resource;

/**
 * @deprecated
 */
interface Extractor
{
    public function count(): int;

    /**
     * @return iterable<Resource>
     */
    public function extract(): iterable;
}

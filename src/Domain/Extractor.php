<?php

declare(strict_types=1);

namespace AkeneoE3\Domain;

use Generator;

interface Extractor
{
    public function count(): int;

    /**
     * @return \Generator|\AkeneoE3\Domain\Resource\Resource[]
     */
    public function extract(): Generator;
}

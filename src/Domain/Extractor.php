<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain;

use Generator;

interface Extractor
{
    public function count(): int;

    /**
     * @return \Generator|\AkeneoEtl\Domain\Resource\Resource[]
     */
    public function extract(): Generator;
}

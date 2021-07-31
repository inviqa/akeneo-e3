<?php

namespace AkeneoEtl\Domain;

use Generator;

interface Extractor
{
    public function count(): int;

    public function extract(): Generator;
}

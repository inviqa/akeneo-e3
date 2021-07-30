<?php

namespace App\Domain;

use Generator;

interface Extractor
{
    public function count(): int;

    public function extract(): Generator;
}

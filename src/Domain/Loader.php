<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain;

interface Loader
{
    public function queue(Resource $resource): void;

    public function load(): void;
}

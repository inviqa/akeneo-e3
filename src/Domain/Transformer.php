<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain;

interface Transformer
{
    public function transform(Resource $resource): \AkeneoEtl\Domain\Resource;
}

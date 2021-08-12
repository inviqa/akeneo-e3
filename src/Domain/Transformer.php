<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain;

use AkeneoEtl\Domain\Resource\Resource as EtlResource;

interface Transformer
{
    public function transform(EtlResource $resource): void;
}

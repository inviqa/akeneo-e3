<?php

declare(strict_types=1);

namespace AkeneoE3\Domain;

use AkeneoE3\Domain\Resource\Resource as EtlResource;

interface Transformer
{
    public function transform(EtlResource $resource): void;
}

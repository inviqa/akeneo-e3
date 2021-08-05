<?php

namespace AkeneoEtl\Domain;

interface Transformer
{
    public function transform(Resource $resource): \AkeneoEtl\Domain\Resource;
}

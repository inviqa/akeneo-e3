<?php

namespace AkeneoEtl\Domain\Transform\TransformResult;

use AkeneoEtl\Domain\Resource\Resource;

interface TransformResult
{
    public function getResource(): Resource;
}

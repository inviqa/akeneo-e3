<?php

namespace AkeneoEtl\Domain\Load\LoadResult;

use AkeneoEtl\Domain\Resource\Resource;

interface LoadResult
{
    public function getResource(): Resource;
}

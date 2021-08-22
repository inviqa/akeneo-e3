<?php

namespace AkeneoE3\Domain\Load\LoadResult;

use AkeneoE3\Domain\Resource\Resource;

interface LoadResult
{
    public function getResource(): Resource;

    public function __toString(): string;
}

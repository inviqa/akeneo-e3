<?php

namespace AkeneoE3\Domain\Transform\TransformResult;

use AkeneoE3\Domain\Resource\Resource;

interface TransformResult
{
    public function getResource(): Resource;
}

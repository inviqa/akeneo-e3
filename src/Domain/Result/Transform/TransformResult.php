<?php

namespace AkeneoE3\Domain\Result\Transform;

use AkeneoE3\Domain\Resource\Resource;
use Stringable;

interface TransformResult extends Stringable
{
    public function getResource(): Resource;
}

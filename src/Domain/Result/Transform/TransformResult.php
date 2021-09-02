<?php

namespace AkeneoE3\Domain\Result\Transform;

use AkeneoE3\Domain\Resource\BaseResource;
use Stringable;

interface TransformResult extends Stringable
{
    public function getResource(): BaseResource;
}

<?php

namespace AkeneoE3\Domain\Result\Write;

use AkeneoE3\Domain\Resource\Resource;
use Stringable;

interface WriteResult extends Stringable
{
    public function getResource(): Resource;
}

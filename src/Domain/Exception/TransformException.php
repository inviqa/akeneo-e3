<?php

namespace AkeneoEtl\Domain\Exception;

use Exception;

class TransformException extends Exception
{
    private bool $canBeSkipped;

    public function __construct(string $message, bool $canBeSkipped)
    {
        $this->canBeSkipped = $canBeSkipped;
        parent::__construct($message);
    }

    public function canBeSkipped(): bool
    {
        return $this->canBeSkipped;
    }
}

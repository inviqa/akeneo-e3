<?php

namespace AkeneoEtl\Domain\Exception;

use AkeneoEtl\Domain\Load\LoadError;
use Exception;

class LoadException extends Exception
{
    /**
     * @var array|LoadError[]
     */
    private array $loadErrors;

    public function __construct(string $message, array $loadErrors)
    {
        $this->loadErrors = $loadErrors;

        parent::__construct($message);
    }

    /**
     * @return LoadError[]|array
     */
    public function getLoadErrors(): array
    {
        return $this->loadErrors;
    }
}

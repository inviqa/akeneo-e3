<?php

namespace AkeneoEtl\Domain\Load\Event;

use AkeneoEtl\Domain\Load\LoadError;
use Symfony\Contracts\EventDispatcher\Event;

class LoadErrorEvent extends Event
{
    /**
     * @var array|LoadError[]
     */
    private array $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    public static function create(array $errors): self
    {
        return new self($errors);
    }

    /**
     * @return array|LoadError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}

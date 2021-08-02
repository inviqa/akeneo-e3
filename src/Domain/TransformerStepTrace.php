<?php

namespace AkeneoEtl\Domain;

use DateTimeImmutable;
use DateTimeInterface;

class TransformerStepTrace
{
    private DateTimeInterface $dateTime;

    private string $identifier;

    /**
     * @var mixed
     */
    private $before;

    /**
     * @var mixed
     */
    private $after;

    public function __construct(string $identifier, $before, $after)
    {
        $this->dateTime = new DateTimeImmutable();
        $this->identifier = $identifier;
        $this->before = $before;
        $this->after = $after;
    }

    public static function create(string $identifier, $before, $after): self
    {
        return new self($identifier, $before, $after);
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getBefore()
    {
        return $this->before;
    }

    public function getAfter()
    {
        return $this->after;
    }

    public function getDateTime()
    {
        return $this->dateTime;
    }
}

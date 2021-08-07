<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Hook;

use DateTimeImmutable;
use DateTimeInterface;

class ActionTrace
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

    /**
     * @param mixed $before
     * @param mixed $after
     */
    private function __construct(string $identifier, $before, $after)
    {
        $this->dateTime = new DateTimeImmutable();
        $this->identifier = $identifier;
        $this->before = $before;
        $this->after = $after;
    }

    /**
     * @param mixed $before
     * @param mixed $after
     */
    public static function create(string $identifier, $before, $after): self
    {
        return new self($identifier, $before, $after);
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return mixed
     */
    public function getBefore()
    {
        return $this->before;
    }

    /**
     * @return mixed
     */
    public function getAfter()
    {
        return $this->after;
    }

    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }
}

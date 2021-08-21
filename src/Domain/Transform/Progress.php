<?php

namespace AkeneoE3\Domain\Transform;

class Progress
{
    private int $current;
    private int $total;

    private function __construct(int $total)
    {
        $this->current = 0;
        $this->total = $total;
    }

    public static function create(int $total): self
    {
        return new self($total);
    }

    public function current(): int
    {
        return $this->current;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function advance(): self
    {
        $this->current++;

        return $this;
    }
}

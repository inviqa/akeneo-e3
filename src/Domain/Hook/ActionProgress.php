<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Hook;

class ActionProgress
{
    private int $index;
    private int $total;

    private function __construct(int $index, int $total)
    {
        $this->index = $index;
        $this->total = $total;
    }

    public static function create(int $index, int $total): self
    {
        return new self($index, $total);
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getTotal(): int
    {
        return $this->total;
    }
}

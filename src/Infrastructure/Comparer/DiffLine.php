<?php

namespace AkeneoE3\Infrastructure\Comparer;

use AkeneoE3\Domain\Resource\Field;

class DiffLine
{
    private string $code;

    private Field $field;

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
    private function __construct(string $code, Field $field, $before, $after)
    {
        $this->code = $code;
        $this->field = $field;
        $this->before = $before;
        $this->after = $after;
    }

    /**
     * @param mixed $before
     * @param mixed $after
     */
    public static function create(string $code, Field $field, $before, $after): self
    {
        return new self($code, $field, $before, $after);
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getField(): Field
    {
        return $this->field;
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
}

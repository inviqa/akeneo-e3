<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Resource;

use Generator;
use LogicException;

final class PropertyValues
{
    private array $values;

    private UpdateBehavior $updateBehavior;

    private function __construct(array $data)
    {
        unset($data['values']);
        $this->values = $data;

        $this->updateBehavior = new UpdateBehavior();
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function count(): int
    {
        return count($this->values);
    }

    /**
     * @return mixed|null
     */
    public function get(Property $property)
    {
        $name = $property->getName();

        if (array_key_exists($name, $this->values) === false) {
            throw new LogicException(sprintf('Property %s is not present in data', $name));
        }

        return $this->values[$name];
    }

    public function has(Property $property): bool
    {
        $name = $property->getName();

        return array_key_exists($name, $this->values);
    }

    /**
     * @param mixed $value
     */
    public function set(Property $property, $value): void
    {
        $name = $property->getName();

        $this->updateBehavior->patch($this->values, $name, $value);
    }

    public function addTo(Property $property, array $value): void
    {
        $name = $property->getName();

        $this->updateBehavior->addTo($this->values, $name, $value);
    }

    /**
     * @return Generator|Property[]
     */
    public function fields(): Generator
    {
        foreach (array_keys($this->values) as $name) {
            yield Property::create($name);
        }
    }

    public function toArray(): array
    {
        return $this->values;
    }
}

<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Resource;

use AkeneoEtl\Domain\ArrayUtils;
use Generator;
use LogicException;

final class PropertyValues
{
    private array $values;

    private function __construct(array $data)
    {
        unset($data['values']);
        $this->values = $data;
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
    public function set(Property $property, $value): self
    {
        $name = $property->getName();
        $this->recursivelySet($this->values, $name, $value);

        return $this;
    }

    public function addTo(Property $property, array $value): self
    {
        $name = $property->getName();
        $this->recursivelyAddTo($this->values, $name, $value);

        return $this;
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

    /**
     * @param mixed $currentValue
     */
    private function recursivelySet(array &$currentElement, string $currentName, $currentValue): void
    {
        if ($currentValue === null || ArrayUtils::isScalarOrSimpleArray($currentValue) === true) {
            $currentElement[$currentName] = $currentValue;

            return;
        }

        foreach ($currentValue as $key => $value) {
            if (array_key_exists($currentName, $currentElement) === false) {
                $currentElement[$currentName] = [];
            }

            $this->recursivelySet($currentElement[$currentName], $key, $value);
        }
    }

    private function recursivelyAddTo(array &$currentElement, string $currentName, array $currentItems): void
    {
        if (ArrayUtils::isSimpleArray($currentItems) === true) {
            if (isset($currentElement[$currentName]) && ArrayUtils::isSimpleArray($currentElement[$currentName]) === false) {
                throw new LogicException(sprintf('%s must be an array for using with `add`', $currentName));
            }

            $before = $currentElement[$currentName] ?? [];
            $currentElement[$currentName] = array_unique(array_merge($before, $currentItems));

            return;
        }

        foreach ($currentItems as $key => $value) {
            if (array_key_exists($currentName, $currentElement) === false) {
                $currentElement[$currentName] = [];
            }

            $this->recursivelyAddTo($currentElement[$currentName], $key, $value);
        }
    }
}

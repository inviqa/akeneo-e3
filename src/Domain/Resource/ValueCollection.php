<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Resource;

use Generator;
use LogicException;

final class ValueCollection
{
    /**
     * @var array<string, mixed>
     */
    private array $values = [];

    private function __construct(array $data)
    {
        foreach ($data as $name => $localisedValues) {
            foreach ($localisedValues as $localisedValue) {
                $attribute = Attribute::create(
                    $name,
                    $localisedValue['scope'],
                    $localisedValue['locale']
                );
                $this->values[$this->attributeHash($attribute)] = $localisedValue['data'];
            }
        }
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * @return mixed|null
     */
    public function get(Attribute $attribute)
    {
        $hash = $this->attributeHash($attribute);

        if (array_key_exists($hash, $this->values) === false) {
            throw new LogicException(sprintf(
                'Attribute %s (scope=%s, locale=%s) is not present in data',
                $attribute->getName(),
                $attribute->getScope() ?? 'null',
                $attribute->getLocale() ?? 'null'
            ));
        }

        return $this->values[$hash];
    }

    /**
     * @param mixed $value
     */
    public function set(Attribute $attribute, $value): self
    {
        $hash = $this->attributeHash($attribute);
        $this->values[$hash] = $value;

        return $this;
    }

    public function has(Attribute $attribute): bool
    {
        $hash = $this->attributeHash($attribute);

        return array_key_exists($hash, $this->values);
    }

    public function count(): int
    {
        return count($this->values);
    }

    public function diff(ValueCollection $collection): self
    {
        $diff = [];
        foreach ($this->values as $hash => $value) {
            if (array_key_exists($hash, $collection->values) === false ||
                $value !== $collection->values[$hash]) {
                $diff[$hash] = $value;
            }
        }

        $diffCollection = new self([]);
        $diffCollection->values = $diff;

        return $diffCollection;
    }

    public function merge(ValueCollection $collection): self
    {
        $merge = $this->values;
        foreach ($collection->values as $hash => $value) {
            $merge[$hash] = $value;
        }

        $mergeCollection = new self([]);
        $mergeCollection->values = $merge;

        return $mergeCollection;
    }

    /**
     * @return Generator|Attribute[]
     */
    public function attributes(): Generator
    {
        foreach (array_keys($this->values) as $hash) {
            yield $this->hashToAttribute($hash);
        }
    }

    public function toArray(): array
    {
        $result = [];

        foreach ($this->values as $hash => $value) {
            $attribute = $this->hashToAttribute($hash);

            $result[$attribute->getName()][] = [
                'scope' => $attribute->getScope(),
                'locale' => $attribute->getLocale(),
                'data' => $value,
            ];
        }

        return $result;
    }

    private function attributeHash(Attribute $attribute): string
    {
        return implode('.', [$attribute->getName(), $attribute->getScope(), $attribute->getLocale()]);
    }

    private function hashToAttribute(string $hash): Attribute
    {
        $pieces = explode('.', $hash);

        return Attribute::create(
            $pieces[0],
            $pieces[1] !== '' ? $pieces[1] : null,
            $pieces[2] !== '' ? $pieces[2] : null
        );
    }
}

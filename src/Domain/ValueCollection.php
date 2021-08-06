<?php

namespace AkeneoEtl\Domain;

class ValueCollection
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
     * @param mixed $default
     *
     * @return mixed|null
     */
    public function get(Attribute $attribute, $default = null)
    {
        $hash = $this->attributeHash($attribute);

        return $this->values[$hash] ?? $default;
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

    public function count(): int
    {
        return count($this->values);
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

    public function diff(ValueCollection $collection): ValueCollection
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
}

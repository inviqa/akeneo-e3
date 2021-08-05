<?php

namespace AkeneoEtl\Domain;

class ValueCollection
{
    /**
     * @var array<\AkeneoEtl\Domain\Attribute, mixed>
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
                $this->values[spl_object_hash($attribute)] = $localisedValue['data'];
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
        return $this->values[$this->attributeHash($attribute)];
    }

    public function count(): int
    {
        return count($this->values);
    }

    private function attributeHash(Attribute $attribute): string
    {
        return spl_object_hash($attribute);
    }
}

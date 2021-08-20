<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Resource;

use AkeneoEtl\Domain\AkeneoSpecifics;
use AkeneoEtl\Domain\ArrayHelper;
use Generator;
use LogicException;

final class AttributeValues
{
    private array $values = [];

    private string $channelFieldName;

    private ArrayHelper $arrayHelper;

    private function __construct(array $data, string $resourceType)
    {
        $this->channelFieldName = AkeneoSpecifics::getChannelFieldName($resourceType);

        $this->arrayHelper = new ArrayHelper();

        foreach ($data as $name => $localisedValues) {
            foreach ($localisedValues as $localisedValue) {
                $hash = $this->hash(
                    $name,
                    $localisedValue[$this->channelFieldName] ?? '',
                    $localisedValue['locale'] ?? ''
                );
                $this->values[$hash] = $localisedValue['data'] ?? null;
            }
        }
    }

    public static function fromArray(array $data, string $resourceType = 'product'): self
    {
        return new self($data, $resourceType);
    }

    /**
     * @return mixed|null
     */
    public function get(Attribute $attribute)
    {
        $hash = $this->attributeHash($attribute);

        if (array_key_exists($hash, $this->values) === false) {
            $this->checkAttributeExists($attribute);
        }

        return $this->values[$hash];
    }

    /**
     * @param mixed $value
     */
    public function set(Attribute $attribute, $value): void
    {
        $hash = $this->attributeHash($attribute);

        $this->values[$hash] = $value;
    }

    public function addTo(Attribute $attribute, array $value): void
    {
        $existingValue =
            $this->has($attribute) ?
            $this->get($attribute) : [];

        $hash = $this->attributeHash($attribute);
        $this->values[$hash] = $this->arrayHelper->merge($existingValue, $value);
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
                $this->channelFieldName => $attribute->getScope(),
                'locale' => $attribute->getLocale(),
                'data' => $value,
            ];
        }

        return $result;
    }

    private function hash(string $name, ?string $scope, ?string $locale): string
    {
        return implode('.', [$name, $scope ?? '', $locale ?? '']);
    }

    private function attributeHash(Attribute $attribute): string
    {
        return $this->hash($attribute->getName(), $attribute->getScope(), $attribute->getLocale());
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

    private function checkAttributeExists(Attribute $attribute): void
    {
        throw new LogicException(
            sprintf(
                'Attribute %s (%s=%s, locale=%s) is not present in data',
                $attribute->getName(),
                $this->channelFieldName,
                $attribute->getScope() ?? 'null',
                $attribute->getLocale() ?? 'null'
            )
        );
    }
}

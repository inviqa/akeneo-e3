<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Command;

class ResourceDataNormaliser
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function normalise($value)
    {
        if (is_scalar($value) === true) {
            return $value;
        }

        if (is_array($value) === true && $this->isObjectLikeArray($value) === true) {
            return $this->normaliseObject($value);
        }

        if (is_array($value) === true) {
            return $this->normaliseArray($value);
        }

        return '~not supported~';
    }

    private function isObjectLikeArray(array $data): bool
    {
        return is_string(array_key_first($data));
    }

    /**
     * @param mixed $data
     */
    private function isScalarOrSimpleArray($data): bool
    {
        if (is_scalar($data) === true) {
            return true;
        }

        if (is_array($data) === true) {
            return $this->isObjectLikeArray($data) === false;
        }

        return false;
    }

    private function isArrayOfScalarValues(array $data): bool
    {
        if (count($data) === 0) {
            return true;
        }

        return (is_scalar($data[0]) === true);
    }

    private function normaliseObject(array $object): string
    {
        $data = [];

        $this->normaliseObjectRecursively($object, $data, '');

        $lines = array_map(
            function ($key, $arrayItem) {
                return sprintf(
                    '%s: %s',
                    trim((string)$key, '.'),
                    $arrayItem
                );
            },
            array_keys($data),
            array_values($data)
        );

        return implode(PHP_EOL, $lines);
    }

    private function normaliseObjectRecursively(array $object, array &$data, string $masterKey): void
    {
        foreach ($object as $key => $item) {
            if ($item === null) {
                $data[$masterKey.'.'.$key] = '';

                continue;
            }

            if (is_scalar($item) || $this->isScalarOrSimpleArray($item)) {
                $data[$masterKey.'.'.$key] = $this->normalise($item);

                continue;
            }

            $this->normaliseObjectRecursively($item, $data, $masterKey.'.'.$key);
        }
    }

    private function normaliseArray(array $value): string
    {
        $data = [];
        foreach ($value as $arrayItem) {
            $data[] = $this->normalise($arrayItem);
        }

        $separator = $this->isArrayOfScalarValues($value) ? ', ' : PHP_EOL;

        return implode($separator, $data);
    }
}

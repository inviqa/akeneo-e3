<?php

namespace AkeneoEtl\Domain\Resource;

use AkeneoEtl\Domain\ArrayHelper;
use LogicException;

/**
 * Update Behavior rules
 *
 * @see https://api.akeneo.com/documentation/update.html#update-behavior
 *
 * Rules:
 *      Rule 1: If the value is an object, it will be merged with the old value.
 *      Rule 2: If the value is not an object, it will replace the old value.
 *      Rule 3: For non-scalar values (objects and arrays) data types must match.
 *      Rule 4: Any data in non specified properties will be left untouched.
 *
 * Implementation:
 *      Rule 1: implemented
 *      Rule 2: implemented
 *      Rule 3: not implemented - type mismatches should be controlled by Akeneo.
 *              Users can send any data and E3 should not restrict them.
 *      Rule 3: not implemented - not possible by design
 */
class UpdateBehavior
{
    private ArrayHelper $arrayHelper;

    public function __construct()
    {
        $this->arrayHelper = new ArrayHelper();
    }

    /**
     * @param mixed $patch
     */
    public function patch(array &$original, string $fieldName, $patch): void
    {
        if ($patch === null || $this->arrayHelper->isScalarOrSimpleArray($patch) === true) {
            $original[$fieldName] = $patch;

            return;
        }

        foreach ($patch as $key => $value) {
            if (array_key_exists($fieldName, $original) === false) {
                $original[$fieldName] = [];
            }

            $this->patch($original[$fieldName], $key, $value);
        }
    }

    public function addTo(array &$original, string $fieldName, array $itemsToAdd): void
    {
        if ($this->arrayHelper->isSimpleArray($itemsToAdd) === true) {
            if (isset($original[$fieldName]) && $this->arrayHelper->isSimpleArray($original[$fieldName]) === false) {
                throw new LogicException(sprintf('%s must be an array for using with `add`', $fieldName));
            }

            $before = $original[$fieldName] ?? [];
            $original[$fieldName] = array_unique(array_merge($before, $itemsToAdd));

            return;
        }

        foreach ($itemsToAdd as $key => $value) {
            if (array_key_exists($fieldName, $original) === false) {
                $original[$fieldName] = [];
            }

            $this->addTo($original[$fieldName], $key, $value);
        }
    }
}

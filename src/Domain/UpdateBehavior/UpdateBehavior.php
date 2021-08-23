<?php

namespace AkeneoE3\Domain\UpdateBehavior;

use AkeneoE3\Domain\Exception\TransformException;

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
 */
class UpdateBehavior
{
    private ArrayHelper $arrayHelper;

    private array $original;

    public function __construct()
    {
        $this->arrayHelper = new ArrayHelper();
    }

    public static function fromArray(array &$original): self
    {
        $self = new self();
        $self->original = &$original;

        return $self;
    }

    /**
     * @param mixed $patch
     */
    public function patch(string $fieldName, $patch): void
    {
        $this->patchRecursive(
            $this->original,
            $fieldName,
            $patch,
            function ($oldValue, $newValue) {
                return $newValue;
            }
        );
    }

    public function addTo(string $fieldName, array $itemsToAdd): void
    {
        $this->patchRecursive(
            $this->original,
            $fieldName,
            $itemsToAdd,
            [$this->arrayHelper, 'merge']
        );
    }


    public function removeFrom(string $fieldName, array $itemsToAdd): void
    {
        $this->patchRecursive(
            $this->original,
            $fieldName,
            $itemsToAdd,
            [$this->arrayHelper, 'subtract']
        );
    }

    /**
     * @param mixed $oldValue
     *
     * @return mixed
     */
    public function merge($oldValue, array $newValue)
    {
        return array_unique(array_merge($oldValue ?? [], $newValue));
    }

    /**
     * @param mixed $oldValue
     */
    public function subtract($oldValue, array $newValue): array
    {
        return array_values(array_diff($oldValue ?? [], $newValue));
    }


    /**
     * @param mixed $patch
     */
    private function patchRecursive(
        array &$original,
        string $fieldName,
        $patch,
        callable $valuePatcher
    ): void {
        // Update Behavior: Rule 3 (validation on data types)
        // For non-scalar values (objects and arrays) data types must match.
        // Throw an exception that can be processes by caller.
        if (array_key_exists($fieldName, $original) === true &&
            $this->arrayHelper->haveMatchingTypes($patch, $original[$fieldName] ?? null) === false) {
            throw new TransformException(sprintf('New value for the field %s does not match its type', $fieldName), true);
        }

        // Update Behavior: Rule 2 (non object update)
        // If the value is not an object, it will replace the old value.
        if ($patch === null || $this->arrayHelper->isScalarOrSimpleArray($patch) === true) {
            $original[$fieldName] = $valuePatcher($original[$fieldName] ?? null, $patch);

            return;
        }

        // Update Behavior: Rule 1 (object update)
        // If the value is an object, it will be merged with the old value.
        foreach ($patch as $key => $value) {
            if (array_key_exists($fieldName, $original) === false) {
                $original[$fieldName] = [];
            }

            $this->patchRecursive($original[$fieldName], $key, $value, $valuePatcher);
        }
    }
}

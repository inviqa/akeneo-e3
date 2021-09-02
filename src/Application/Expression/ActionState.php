<?php

declare(strict_types=1);

namespace AkeneoE3\Application\Expression;

use AkeneoE3\Domain\Resource\Field;
use AkeneoE3\Domain\Resource\TransformableResource;

/**
 * @internal
 */
final class ActionState
{
    public static TransformableResource $resource;

    public static Field $field;

    public static function setResourceAndField(TransformableResource $resource, Field $field): void
    {
        self::$resource = $resource;
        self::$field = $field;
    }
}

<?php

declare(strict_types=1);

namespace AkeneoE3\Application\Expression;

use AkeneoE3\Domain\Resource\Field;
use AkeneoE3\Domain\Resource\Resource;

/**
 * @internal
 */
final class ActionState
{
    public static Resource $resource;

    public static Field $field;

    public static function setResourceAndField(Resource $resource, Field $field): void
    {
        self::$resource = $resource;
        self::$field = $field;
    }
}

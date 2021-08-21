<?php

declare(strict_types=1);

namespace AkeneoE3\Application\Expression;

use AkeneoE3\Domain\Resource\Field;

/**
 * @internal
 */
final class StateHolder
{
    public static \AkeneoE3\Domain\Resource\Resource $resource;

    public static Field $field;
}

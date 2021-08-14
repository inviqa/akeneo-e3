<?php

declare(strict_types=1);

namespace AkeneoEtl\Application\Expression;

use AkeneoEtl\Domain\Resource\Field;

/**
 * @internal
 */
final class StateHolder
{
    public static \AkeneoEtl\Domain\Resource\Resource $resource;

    public static Field $field;
}

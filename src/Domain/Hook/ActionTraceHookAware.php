<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Hook;

interface ActionTraceHookAware
{
    public function setHook(ActionTraceHook $hook): void;
}

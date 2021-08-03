<?php

namespace AkeneoEtl\Domain\Hook;

interface ActionTraceHookAware
{
    public function setHook(ActionTraceHook $hook): void;
}

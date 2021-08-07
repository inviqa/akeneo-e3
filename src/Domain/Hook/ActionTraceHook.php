<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Hook;

interface ActionTraceHook
{
    public function onAction(ActionTrace $trace): void;
}

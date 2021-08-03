<?php

namespace AkeneoEtl\Domain\Hook;

interface ActionTraceHook
{
    public function onAction(ActionTrace $trace): void;
}

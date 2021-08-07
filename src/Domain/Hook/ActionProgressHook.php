<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Hook;

interface ActionProgressHook
{
    public function onActionProgress(ActionProgress $actionProgress): void;
}

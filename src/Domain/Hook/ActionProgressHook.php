<?php

namespace AkeneoEtl\Domain\Hook;

interface ActionProgressHook
{
    public function onActionProgress(ActionProgress $actionProgress): void;
}

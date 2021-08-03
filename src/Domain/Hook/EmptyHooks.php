<?php

namespace AkeneoEtl\Domain\Hook;

use AkeneoEtl\Infrastructure\Loader\LoaderError;

class EmptyHooks implements Hooks
{
    public function onActionProgress(ActionProgress $actionProgress): void {}

    public function onAction(ActionTrace $trace): void {}

    public function onLoaderError(array $item, LoaderError $error): void {}
}

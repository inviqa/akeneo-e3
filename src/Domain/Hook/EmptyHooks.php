<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Hook;

class EmptyHooks implements Hooks
{
    public function onActionProgress(ActionProgress $actionProgress): void
    {
    }

    public function onAction(ActionTrace $trace): void
    {
    }

    public function onLoaderError(array $item, LoaderError $error): void
    {
    }
}

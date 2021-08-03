<?php

namespace AkeneoEtl\Domain\Hook;

use AkeneoEtl\Infrastructure\Loader\LoaderError;

interface LoaderErrorHook
{
    // @todo: urgent: move loader error to domain
    public function onLoaderError(array $item, LoaderError $error): void;
}

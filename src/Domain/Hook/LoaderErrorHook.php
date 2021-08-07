<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Hook;

interface LoaderErrorHook
{
    public function onLoaderError(array $item, LoaderError $error): void;
}

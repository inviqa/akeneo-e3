<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Hook;

interface Hooks extends ActionTraceHook, ActionProgressHook, LoaderErrorHook
{
}

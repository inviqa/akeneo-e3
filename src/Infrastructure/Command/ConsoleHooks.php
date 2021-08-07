<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Command;

use AkeneoEtl\Domain\Hook\ActionTrace;
use AkeneoEtl\Domain\Hook\ProgressEvent;
use AkeneoEtl\Domain\Hook\Hooks;
use AkeneoEtl\Domain\Hook\LoaderError;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

final class ConsoleHooks implements Hooks
{
    private OutputInterface $output;
    private ProgressBar $progress;

    public function __construct(OutputInterface $output, ProgressBar $progress)
    {
        $this->output = $output;
        $this->progress = $progress;
    }

    public function onAction(ActionTrace $trace): void
    {

    }

    public function onLoaderError(array $item, LoaderError $error): void
    {
        $this->output->writeln(
            sprintf(
                '[%s] %s',
                $error->getIdentifier(),
                $error->getErrorMessage()
            )
        );
    }

    public function onActionProgress(ProgressEvent $actionProgress): void
    {
        if ($actionProgress->getIndex() === 0) {
            $this->progress->start($actionProgress->getTotal());
            return;
        }

        $this->progress->advance();
    }
}

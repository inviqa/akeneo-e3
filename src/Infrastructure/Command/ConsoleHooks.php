<?php

namespace AkeneoEtl\Infrastructure\Command;

use AkeneoEtl\Domain\Hook\ActionTrace;
use AkeneoEtl\Domain\Hook\ActionProgress;
use AkeneoEtl\Domain\Hook\Hooks;
use AkeneoEtl\Infrastructure\Loader\LoaderError;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleHooks implements Hooks
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
        $this->progress->clear();
        $this->output->writeln(sprintf('[[ %s ]] %s -> %s',
            $trace->getIdentifier(),
            $trace->getBefore(),
            $trace->getAfter()));
        $this->progress->display();
    }

    public function onLoaderError(array $item, LoaderError $error): void
    {
        $this->output->writeln(
            sprintf(
                '[%s] %s',
                $error->getIdentifier(),
                $error->getErrorMessage()
            )
        );    }

    public function onActionProgress(ActionProgress $actionProgress): void
    {
        if ($actionProgress->getIndex() === 0) {
            $this->progress->start($actionProgress->getTotal());

            return;
        }

        $this->progress->advance();
    }
}

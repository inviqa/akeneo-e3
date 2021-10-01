<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Command;

use AkeneoE3\Infrastructure\EtlFactory;
use AkeneoE3\Infrastructure\TransformRequest\TransformRequest;
use AkeneoE3\Infrastructure\TransformRequest\TransformRequestFromInputFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class TransformCommand extends Command
{
    private EtlFactory $factory;

    private TransformRequestFromInputFactory $requestFactory;

    private TransformOutput $output;

    private TransformRequest $request;

    public function __construct(
        EtlFactory $factory,
        TransformRequestFromInputFactory $requestFactory
    ) {
        $this->factory = $factory;
        $this->requestFactory = $requestFactory;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('transform')
            ->addOption('resource-type', 't', InputOption::VALUE_REQUIRED)
            ->addOption('connection', 'c', InputOption::VALUE_REQUIRED)
            ->addOption(
                'destination-connection',
                'd',
                InputOption::VALUE_REQUIRED
            )
            ->addOption('rules', 'r', InputOption::VALUE_REQUIRED)
            ->addOption('output-transform', 'o', InputOption::VALUE_NONE, 'Output transformation results on-the-fly')
            ->addOption('dry-run', null, InputOption::VALUE_OPTIONAL, 'Enables dry run - no data will be modified. Apply only for specific resources by their codes --dry-run=1234,5678', '')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->request = $this->requestFactory->createFromInput($input);

        $this->output = new TransformOutput($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $askToConnect = $this->output->askToConnect(
            $this->request->destinationConnection->getHost(),
            $this->request->ruleProfile->isDryRun()
        );

        if ($askToConnect === false) {
            return Command::SUCCESS;
        }

        $etl = $this->factory->createEtlProcess(
            $this->request->resourceType,
            $this->request->sourceConnection,
            $this->request->destinationConnection,
            $this->request->ruleProfile
        );

        $total = $etl->total();
        $results = $etl->execute();

        foreach ($results as $result) {
            $this->output->render($result, $total);
        }

        return Command::SUCCESS;
    }
}

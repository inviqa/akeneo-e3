<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Command;

use AkeneoE3\Domain\Profile\EtlProfile;
use AkeneoE3\Infrastructure\EtlFactory;
use AkeneoE3\Infrastructure\Request\RuleSetTransformRequest;
use AkeneoE3\Infrastructure\Request\RuleSetTransformRequestFactory;
use AkeneoE3\Infrastructure\Request\TransformRequest;
use AkeneoE3\Infrastructure\Request\TransformRequestFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class MigrateCommand extends Command
{
    private EtlFactory $factory;

    private RuleSetTransformRequestFactory $requestFactory;

    public function __construct(
        EtlFactory $factory,
        RuleSetTransformRequestFactory $requestFactory
    ) {
        $this->factory = $factory;
        $this->requestFactory = $requestFactory;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('migrate')
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
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $request = $this->requestFactory->createFromInput($input);

        $askToConnect = (new TransformOutput($input, $output))->askToConnect(
            $request->destinationConnection->getHost(),
            $request->isDryRun
        );

        if ($askToConnect === false) {
            return Command::SUCCESS;
        }

        foreach ($request->ruleSet as $ruleProfile) {
            $consoleOutput = new TransformOutput($input, $output);

            $etl = $this->factory->createEtlProcess(
                $ruleProfile->getResourceType(),
                $request->sourceConnection,
                $request->destinationConnection,
                $ruleProfile
            );

            $total = $etl->total();
            $results = $etl->execute();

            foreach ($results as $result) {
                $consoleOutput->render($result, $total);
            }
        }

        return Command::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Command;

use AkeneoE3\Domain\Profile\ConnectionProfile;
use AkeneoE3\Domain\Profile\EtlProfile;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\EtlFactory;
use AkeneoE3\Infrastructure\Profile\ConnectionProfileFactory;
use AkeneoE3\Infrastructure\Profile\EtlProfileFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class TransformCommand extends Command
{
    private EtlFactory $factory;

    private ConnectionProfileFactory $connectionProfileFactory;

    private EtlProfileFactory $etlProfileFactory;

    private ConnectionProfile $sourceConnection;

    private ConnectionProfile $destinationConnection;

    private EtlProfile $ruleProfile;

    private ResourceType $resourceType;

    private TransformOutput $output;

    public function __construct(
        EtlFactory $factory,
        ConnectionProfileFactory $connectionProfileFactory,
        EtlProfileFactory $etlProfileFactory
    ) {
        $this->factory = $factory;
        $this->connectionProfileFactory = $connectionProfileFactory;
        $this->etlProfileFactory = $etlProfileFactory;

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
        $this->sourceConnection = $this->getConnectionProfile($input);
        $this->destinationConnection = $this->getDestinationConnectionProfile($input) ?? $this->sourceConnection;

        $this->ruleProfile = $this->getEtlProfile($input);

        $this->ruleProfile->setDryRun($this->isDryRun($input));
        $this->ruleProfile->setDryRunCodes($this->getDryRunCodes($input));

        $this->resourceType = ResourceType::create((string)$input->getOption('resource-type'));

        $this->output = new TransformOutput($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $askToConnect = $this->output->askToConnect(
            $this->destinationConnection->getHost(),
            $this->ruleProfile->isDryRun()
        );

        if ($askToConnect === false) {
            return Command::SUCCESS;
        }

        $etl = $this->factory->createEtlProcess(
            $this->resourceType,
            $this->sourceConnection,
            $this->destinationConnection,
            $this->ruleProfile
        );

        $total = $etl->total();
        $results = $etl->execute();

        foreach ($results as $result) {
            $this->output->render($result, $total);
        }

        return Command::SUCCESS;
    }

    private function getConnectionProfile(InputInterface $input): ConnectionProfile
    {
        $profileFileName = (string)$input->getOption('connection');

        if ($profileFileName === '') {
            throw new LogicException(
                '--connection option is required.'
            );
        }

        return $this->connectionProfileFactory->fromFile($profileFileName);
    }

    private function getDestinationConnectionProfile(InputInterface $input): ?ConnectionProfile
    {
        $profileFileName = (string)$input->getOption('destination-connection');

        if ($profileFileName === '') {
            return null;
        }

        return $this->connectionProfileFactory->fromFile($profileFileName);
    }

    private function getEtlProfile(InputInterface $input): EtlProfile
    {
        $profileFileName = (string)$input->getOption('rules');

        if ($profileFileName === '') {
            throw new LogicException('--rules option is required.');
        }

        return $this->etlProfileFactory->fromFile($profileFileName);
    }

    private function isDryRun(InputInterface $input): bool
    {
        return $input->getOption('dry-run') !== '';
    }

    private function getDryRunCodes(InputInterface $input): array
    {
        $dryRunOption = (string)$input->getOption('dry-run');

        if ($dryRunOption === '') {
            return [];
        }

        return explode(',', $dryRunOption);
    }
}

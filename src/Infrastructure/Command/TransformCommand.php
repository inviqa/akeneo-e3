<?php

namespace AkeneoEtl\Infrastructure\Command;

use AkeneoEtl\Domain\Profile\ConnectionProfile;
use AkeneoEtl\Domain\Profile\EtlProfile;
use AkeneoEtl\Infrastructure\ConnectionProfile\ProfileFactory as ConnectionProfileFactory;
use AkeneoEtl\Infrastructure\EtlProfile\ProfileFactory as EtlProfileFactory;
use AkeneoEtl\Infrastructure\EtlFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TransformCommand extends Command
{
    private EtlFactory $factory;
    private ConnectionProfileFactory $connectionProfileFactory;
    private EtlProfileFactory $etlProfileFactory;

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
            ->addOption('resource-type', 'r ', InputOption::VALUE_REQUIRED)
            ->addOption('connection-profile', 'c', InputOption::VALUE_REQUIRED)
            ->addOption(
                'destination-connection-profile',
                'd',
                InputOption::VALUE_REQUIRED
            )
            ->addOption('etl-profile', 'p', InputOption::VALUE_REQUIRED);
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $sourceConnectionProfile = $this->getConnectionProfile($input);
        $destinationConnectionProfile = $this->getDestinationConnectionProfile(
            $input
        ) ?? $sourceConnectionProfile;

        $etlProfile = $this->getEtlProfile($input);

        $resourceType = $input->getOption('resource-type');

        if ($resourceType === null) {
            // @todo: read from etl profile
            // if null, throw an exception
        }

        $progress = new ProgressBar($output);
        $consoleHooks = new ConsoleHooks($output, $progress);

        $etl = $this->factory->createEtlProcess(
            $resourceType,
            $sourceConnectionProfile,
            $destinationConnectionProfile,
            $etlProfile,
            $consoleHooks
        );

        $etl->execute();

        $progress->finish();

        return Command::SUCCESS;
    }

    private function getConnectionProfile(InputInterface $input): ConnectionProfile
    {
        $profileFileName = $input->getOption('connection-profile');

        if ($profileFileName === null) {
            throw new LogicException(
                '--connection-profile option is required.'
            );
        }

        return $this->connectionProfileFactory->read($profileFileName);
    }

    private function getDestinationConnectionProfile(InputInterface $input): ?ConnectionProfile
    {
        $profileFileName = $input->getOption('destination-connection-profile');

        if ($profileFileName === null) {
            return null;
        }

        return $this->connectionProfileFactory->read($profileFileName);
    }

    private function getEtlProfile(InputInterface $input): EtlProfile
    {
        $profileFileName = $input->getOption('etl-profile');

        if ($profileFileName === null) {
            throw new LogicException('--etl-profile option is required.');
        }

        return $this->etlProfileFactory->fromFile($profileFileName);
    }
}

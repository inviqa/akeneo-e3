<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Command;

use AkeneoEtl\Domain\Profile\ConnectionProfile;
use AkeneoEtl\Domain\Profile\EtlProfile;
use AkeneoEtl\Infrastructure\EtlFactory;
use AkeneoEtl\Infrastructure\Profile\ConnectionProfileFactory;
use AkeneoEtl\Infrastructure\Profile\EtlProfileFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class TransformCommand extends Command
{
    private EtlFactory $factory;

    private ConnectionProfileFactory $connectionProfileFactory;

    private EtlProfileFactory $etlProfileFactory;

    private EventDispatcherInterface $eventDispatcher;

    private ConnectionProfile $sourceConnection;

    private ConnectionProfile $destinationConnection;

    private EtlProfile $ruleProfile;

    private string $resourceType;

    private SymfonyStyle $style;

    public function __construct(
        EtlFactory $factory,
        ConnectionProfileFactory $connectionProfileFactory,
        EtlProfileFactory $etlProfileFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->factory = $factory;
        $this->connectionProfileFactory = $connectionProfileFactory;
        $this->etlProfileFactory = $etlProfileFactory;
        $this->eventDispatcher = $eventDispatcher;

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
            ->addOption('output-transform', 'o', InputOption::VALUE_NONE, 'Output transformation results on-the-fly');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->sourceConnection = $this->getConnectionProfile($input);
        $this->destinationConnection = $this->getDestinationConnectionProfile($input) ?? $this->sourceConnection;
        $this->ruleProfile = $this->getEtlProfile($input);

        $this->resourceType = (string)$input->getOption('resource-type');

        $this->style = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->askToConnect() === false) {
            return Command::SUCCESS;
        }

        EventSubscriber::init($this->eventDispatcher, $input, $output);

        $etl = $this->factory->createEtlProcess(
            $this->resourceType,
            $this->sourceConnection,
            $this->destinationConnection,
            $this->ruleProfile
        );

        $etl->execute();

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

    private function askToConnect(): bool
    {
        if ($this->ruleProfile->isDryRun() === true) {
            return false;
        }

        $phrase = sprintf(
            "You're going to connect to %s to transform data. Continue?",
            $this->destinationConnection->getHost()
        );

        return $this->style->confirm($phrase);
    }
}

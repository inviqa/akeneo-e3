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
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class TransformCommand extends Command
{
    private EtlFactory $factory;

    private ConnectionProfileFactory $connectionProfileFactory;

    private EtlProfileFactory $etlProfileFactory;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        EtlFactory $factory,
        ConnectionProfileFactory $connectionProfileFactory,
        EtlProfileFactory $etlProfileFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->factory = $factory;
        $this->connectionProfileFactory = $connectionProfileFactory;
        $this->etlProfileFactory = $etlProfileFactory;

        parent::__construct();
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function configure(): void
    {
        $this
            ->setName('transform')
            ->addOption('resource-type', 'r', InputOption::VALUE_REQUIRED)
            ->addOption('connection-profile', 'c', InputOption::VALUE_REQUIRED)
            ->addOption(
                'destination-connection-profile',
                'd',
                InputOption::VALUE_REQUIRED
            )
            ->addOption('etl-profile', 'p', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sourceConnectionProfile = $this->getConnectionProfile($input);
        $destinationConnectionProfile = $this->getDestinationConnectionProfile($input) ?? $sourceConnectionProfile;

        $etlProfile = $this->getEtlProfile($input);

        $resourceType = (string)$input->getOption('resource-type');

        if ($resourceType === '') {
            // @todo: read from etl profile
            // if null, throw an exception
        }

        $progress = new ProgressBar($output);
        $consoleHooks = new ConsoleHooks($output, $progress);

        new EventSubscriber($this->eventDispatcher, $progress, $output);

        $etl = $this->factory->createEtlProcess(
            $resourceType,
            $sourceConnectionProfile,
            $destinationConnectionProfile,
            $etlProfile,
            $consoleHooks,
            $this->eventDispatcher
        );

        $etl->execute();

        $progress->finish();

        return Command::SUCCESS;
    }

    private function getConnectionProfile(InputInterface $input): ConnectionProfile
    {
        $profileFileName = (string)$input->getOption('connection-profile');

        if ($profileFileName === '') {
            throw new LogicException(
                '--connection-profile option is required.'
            );
        }

        return $this->connectionProfileFactory->fromFile($profileFileName);
    }

    private function getDestinationConnectionProfile(InputInterface $input): ?ConnectionProfile
    {
        $profileFileName = (string)$input->getOption('destination-connection-profile');

        if ($profileFileName === '') {
            return null;
        }

        return $this->connectionProfileFactory->fromFile($profileFileName);
    }

    private function getEtlProfile(InputInterface $input): EtlProfile
    {
        $profileFileName = (string)$input->getOption('etl-profile');

        if ($profileFileName === '') {
            throw new LogicException('--etl-profile option is required.');
        }

        return $this->etlProfileFactory->fromFile($profileFileName);
    }
}

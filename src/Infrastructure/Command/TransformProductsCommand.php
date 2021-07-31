<?php

namespace AkeneoEtl\Infrastructure\Command;

use AkeneoEtl\Domain\ConnectionProfile;
use AkeneoEtl\Domain\EtlProfile;
use AkeneoEtl\Infrastructure\ConnectionProfile\YamlReader as ConnectionProfileReader;
use AkeneoEtl\Infrastructure\EtlProfile\YamlReader as EtlProfileReader;
use AkeneoEtl\Infrastructure\EtlFactory;
use AkeneoEtl\Infrastructure\Loader\LoaderError;
use Closure;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TransformProductsCommand extends Command
{

    protected static $defaultName = 'etl:product';

    private EtlFactory $factory;

    private ConnectionProfileReader $connectionProfileReader;

    private EtlProfileReader $etlProfileReader;

    public function __construct(
        EtlFactory $factory,
        ConnectionProfileReader $connectionProfileReader,
        EtlProfileReader $etlProfileReader
    ) {
        $this->factory = $factory;
        $this->connectionProfileReader = $connectionProfileReader;
        $this->etlProfileReader = $etlProfileReader;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
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

        $etl = $this->factory->createEtlProcess(
            $sourceConnectionProfile,
            $destinationConnectionProfile,
            $etlProfile,
            function (array $item, LoaderError $error) use ($output) {
                $output->writeln(
                    sprintf(
                        '[%s] %s',
                        $error->getIdentifier(),
                        $error->getErrorMessage()
                    )
                );
            }
        );

        $progress = new ProgressBar($output);

        $etl->execute(
            function (int $stepIndex, int $stepCount) use ($progress) {

                if ($stepIndex === 0) {
                    $progress->start($stepCount);

                    return;
                }

                $progress->advance();
            }
        );

        $progress->finish();

        return Command::SUCCESS;
    }

    private function getConnectionProfile(InputInterface $input
    ): ConnectionProfile {
        $profileFileName = $input->getOption('connection-profile');

        if ($profileFileName === null) {
            throw new LogicException(
                '--connection-profile option is required.'
            );
        }

        return $this->connectionProfileReader->read($profileFileName);
    }

    private function getDestinationConnectionProfile(InputInterface $input
    ): ?ConnectionProfile {
        $profileFileName = $input->getOption('destination-connection-profile');

        if ($profileFileName === null) {
            return null;
        }

        return $this->connectionProfileReader->read($profileFileName);
    }

    private function getEtlProfile(InputInterface $input): EtlProfile
    {
        $profileFileName = $input->getOption('etl-profile');

        if ($profileFileName === null) {
            throw new LogicException('--etl-profile option is required.');
        }

        return $this->etlProfileReader->read($profileFileName);
    }
}

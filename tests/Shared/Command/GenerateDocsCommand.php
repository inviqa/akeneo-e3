<?php

declare(strict_types=1);

namespace AkeneoEtl\Tests\Shared\Command;

use AkeneoEtl\Domain\EtlProcess;
use AkeneoEtl\Domain\Profile\EtlProfile;
use AkeneoEtl\Domain\Resource\Resource;
use AkeneoEtl\Infrastructure\Comparer\ResourceComparer;
use AkeneoEtl\Infrastructure\EtlFactory;
use AkeneoEtl\Tests\Acceptance\bootstrap\InMemoryExtractor;
use AkeneoEtl\Tests\Acceptance\bootstrap\InMemoryLoader;
use LogicException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;

final class GenerateDocsCommand extends Command
{
    private EtlFactory $factory;

    private Environment $twig;

    private EventDispatcherInterface $eventDispatcher;

    private ResourceComparer $resourceComparer;

    public function __construct(
        EtlFactory $factory,
        Environment $twig,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
        $this->twig = $twig;

        $this->resourceComparer = new ResourceComparer();

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('generate-docs');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $examples = Yaml::parseFile('docs/example-provider.yaml');

        $categories = $examples['@categories'];
        unset($examples['@categories']);

        $exampleList = [];

        foreach ($examples as &$example) {
            foreach ($example['tasks'] as $taskCode => &$task) {
                $profileData = $task['profile'];

                $profile = EtlProfile::fromArray($profileData);
                $resource = Resource::fromArray($task['resource'], 'product');

                $task['results'] = $this->getTransformationResults($resource, $profile, $task['description']);
                $task['profile'] = Yaml::dump($task['profile'], 4);
            }

            $this->renderExampleFile($example);

            $exampleList[$example['category']][$example['file_name']] = $example['header'];
        }

        $this->renderExampleListFile($exampleList, $categories);

        return Command::SUCCESS;
    }

    private function getTransformationResults(
        Resource $resource,
        EtlProfile $profile,
        string $taskCode
    ): array {
        $extractor = new InMemoryExtractor($resource);
        $loader = new InMemoryLoader($resource, false);
        $transformer = $this->factory->createTransformer($profile);

        $etl = new EtlProcess(
            $extractor,
            $transformer,
            $loader,
            $this->eventDispatcher
        );

        $etl->execute();

        if ($loader->getResult() === null) {
            throw new LogicException(
                'Task %s: invalid rules',
                $taskCode
            );
        }

        $compareTable = $this->resourceComparer->compareWithOrigin($loader->getResult());

        $results = [];

        foreach ($compareTable as $change) {
            $fieldName = $change->getField()->getName();
            $results[$fieldName] = [
                'field' => $fieldName,
                'before' => $change->getBefore(),
                'after' => $change->getAfter(),
            ];
        }

        return $results;
    }

    private function renderExampleFile(array $example): void
    {
        $content = $this->twig->render('example.md.twig', $example);

        $resultFileName = sprintf('./docs/examples/%s.md', $example['file_name']);
        file_put_contents($resultFileName, $content);
    }

    private function renderExampleListFile(array $exampleList, array $categories): void
    {
        $content = $this->twig->render('example-list.md.twig', [
            'examples' => $exampleList,
            'categories' => $categories,
        ]);

        $resultFileName = './docs/examples/example-list.md';
        file_put_contents($resultFileName, $content);
    }
}

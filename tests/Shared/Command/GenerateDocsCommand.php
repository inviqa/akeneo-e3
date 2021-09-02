<?php

declare(strict_types=1);

namespace AkeneoE3\Tests\Shared\Command;

use AkeneoE3\Application\Expression\FunctionProvider;
use AkeneoE3\Domain\EtlProcess;
use AkeneoE3\Domain\Extractor;
use AkeneoE3\Domain\Loader;
use AkeneoE3\Domain\Profile\EtlProfile;
use AkeneoE3\Domain\Resource\TransformableResource;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Comparer\ResourceComparer;
use AkeneoE3\Infrastructure\EtlFactory;
use AkeneoE3\Tests\Acceptance\bootstrap\EmptyQuery;
use AkeneoE3\Tests\Acceptance\bootstrap\InMemoryExtractor;
use AkeneoE3\Tests\Acceptance\bootstrap\InMemoryLoader;
use AkeneoE3\Tests\Shared\FunctionDocumentor;
use LogicException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;

final class GenerateDocsCommand extends Command
{
    private EtlFactory $factory;

    private Environment $twig;

    private ResourceComparer $resourceComparer;

    private FunctionDocumentor $functionDocumentor;

    public function __construct(
        EtlFactory $factory,
        Environment $twig,
        FunctionProvider $functionProvider
    ) {
        $this->factory = $factory;
        $this->twig = $twig;

        $this->resourceComparer = new ResourceComparer();
        $this->functionDocumentor = new FunctionDocumentor($functionProvider);

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('generate-docs');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->renderFunctions();
        $this->renderExamples();

        return Command::SUCCESS;
    }

    protected function renderFunctions(): void
    {
        $functions = $this->functionDocumentor->getFunctions();
        $content = $this->twig->render('function-list.md.twig', [
            'functions' => $functions,
        ]);

        $resultFileName = './docs/function-list.md';

        file_put_contents($resultFileName, $content);
    }

    private function getTransformationResults(
        TransformableResource $resource,
        EtlProfile $profile,
        string $taskCode
    ): array {
        $extractor = new InMemoryExtractor($resource);
        $loader = new InMemoryLoader();
        $transformer = $this->factory->createTransformer($profile);

        $etl = new EtlProcess(
            new Extractor($extractor, new EmptyQuery()),
            $transformer,
            new Loader($loader, $profile)
        );

        iterator_count($etl->execute());

        $compareTable = $this->resourceComparer->compareWithOrigin($loader->getResult());

        if (count($compareTable) === 0) {
            throw new LogicException(sprintf('Empty results of the task `%s`', $taskCode));
        }

        $results = [];

        foreach ($compareTable as $change) {
            $fieldName = $change->getField();
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

        $resultFileName = './docs/example-list.md';
        file_put_contents($resultFileName, $content);
    }

    private function renderExamples(): void
    {
        $examples = Yaml::parseFile('docs/example-provider.yaml');

        $categories = $examples['@categories'];
        unset($examples['@categories']);

        $exampleList = [];

        foreach ($examples as &$example) {
            foreach ($example['tasks'] as $taskCode => &$task) {
                $profileData = $task['profile'];

                $profile = EtlProfile::fromArray($profileData);
                $resourceType = ResourceType::create($task['resource-type'] ?? 'product');
                $resource = Resource::fromArray($task['resource'], $resourceType);

                $task['results'] = $this->getTransformationResults(
                    $resource,
                    $profile,
                    $task['description']
                );
                $task['profile'] = Yaml::dump($task['profile'], 4);
            }

            $this->renderExampleFile($example);

            $exampleList[$example['category']][$example['file_name']] = $example['header'];
        }

        $this->renderExampleListFile($exampleList, $categories);
    }
}

<?php

namespace AkeneoE3\Tests\Acceptance\bootstrap;

use AkeneoE3\Domain\EtlProcess;
use AkeneoE3\Domain\Extractor;
use AkeneoE3\Domain\Loader;
use AkeneoE3\Domain\Transformer;
use AkeneoE3\Domain\Profile\EtlProfile;
use AkeneoE3\Domain\Resource\Attribute;
use AkeneoE3\Domain\Resource\AuditableResource;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\EtlFactory;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\Yaml\Yaml;
use Webmozart\Assert\Assert;

class TransformContext implements Context
{
    private InMemoryExtractor $extractor;

    private Transformer $transformer;

    private InMemoryLoader $loader;

    private array $resourceData = [];

    private ResourceType $resourceType;

    private EtlProfile $profile;

    /**
     * @Given /^(a|an) (?P<resourceType>[^"]+) in the PIM( with properties)?:$/
     */
    public function readResourceProperties(string $resourceType, TableNode $table): void
    {
        $this->resourceType = ResourceType::create($resourceType);
        $this->resourceData = $this->readPropertiesFromTable($table);
    }

    /**
     * @Given /^attributes:$/
     */
    public function readResourceValues(TableNode $table): void
    {
        $this->resourceData['values'] = $this->readValuesFromTable($table);
    }

    /**
     * @Given the list of :listCode:
     */
    public function readResourceLocalisedList(string $listCode, TableNode $table): void
    {
        $this->resourceData[$listCode] = $this->readLocalisedListFromTable($table);
    }

    /**
     * @Given /^associations:$/
     */
    public function readResourceAssociations(TableNode $table): void
    {
        $this->resourceData['associations'] = $this->readAssociationsFromTable($table);
    }

    /**
     * @Given with a text attribute :attributeName:
     */
    public function setTextAttributeValue(string $attributeName, PyStringNode $value)
    {
        $this->resourceData['values'][$attributeName][] = [
            'locale' => null,
            'scope' => null,
            'data' => $value->getRaw(),
        ];
    }

    /**
     * @Given I apply transformations using the profile:
     */
    public function initialiseEtlProfile(PyStringNode $string): void
    {
        $config = Yaml::parse($string);

        $factory = new EtlFactory();
        $this->profile = EtlProfile::fromArray($config);

        $this->transformer = $factory->createTransformer($this->profile);
    }

    /**
     * @When transformation is executed
     */
    public function transformationIsExecuted(): void
    {
        $resource = AuditableResource::fromArray($this->resourceData, $this->resourceType);

        $this->extractor = new InMemoryExtractor($resource);
        $this->loader = new InMemoryLoader($resource, $this->profile->getUploadMode());

        $etl = new EtlProcess(
            new Extractor($this->extractor, new EmptyQuery()),
            $this->transformer,
            new Loader($this->loader, $this->profile)
        );

        iterator_count($etl->execute());
    }

    /**
     * @Then the upload result should have properties:
     */
    public function checkResultProperties(TableNode $table): void
    {
        $expected = $this->readPropertiesFromTable($table);

        $loaderData = $this->loader->getResult()->toArray();
        unset($loaderData['values'], $loaderData['labels'], $loaderData['associations']);

        Assert::eq($expected, $loaderData);
    }

    /**
     * @Then the upload result should have attributes:
     */
    public function checkValues(TableNode $table): void
    {
        $expected = $this->readValuesFromTable($table);

        $loaderData = $this->loader->getResult()->toArray();
        Assert::eq($expected, $loaderData['values']);
    }


    /**
     * @Then the upload result should have the list of :listCode:
     */
    public function checkLocalisedList(string $listCode, TableNode $table): void
    {
        $expected = $this->readLocalisedListFromTable($table);

        $loaderData = $this->loader->getResult()->toArray();

        Assert::eq($expected, $loaderData[$listCode]);
    }

    /**
     * @Then the upload result should have associations:
     */
    public function checkAssociations(TableNode $table): void
    {
        $expected = $this->readAssociationsFromTable($table);

        $loaderData = $this->loader->getResult()->toArray();

        Assert::eq($expected, $loaderData['associations']);
    }


    /**
     * @Then should have the text attribute :attributeName:
     */
    public function checkTextAttributeValue(string $attributeName, PyStringNode $attributeValue)
    {
        $actualValue = $this->loader->getResult()->get(Attribute::create($attributeName, null, null));

        Assert::eq($attributeValue->getRaw(), $actualValue);
    }

    /**
     * @Then the upload result is empty
     */
    public function resourceIsNotModified()
    {
        Assert::true($this->loader->isResultEmpty());
    }

    private function readPropertiesFromTable(TableNode $table): array
    {
        $data = [];
        foreach ($table as $row) {
            $field = $row['field'];
            $value = $this->convertTableValue($row['value']);

            $data[$field] = $value;
        }

        return $data;
    }

    private function readValuesFromTable(TableNode $table): array
    {
        $data = [];
        foreach ($table as $row) {
            $field = $row['attribute'];
            $value = $this->convertTableValue($row['value']);

            $data[$field][] = [
                'scope' => $row['scope'],
                'locale' => $row['locale'],
                'data' => $value,
            ];
        }

        return $data;
    }

    private function readAssociationsFromTable(TableNode $table): array
    {
        $data = [];
        foreach ($table as $row) {
            $type = $row['type'];

            foreach (['products', 'product_models', 'groups'] as $codeGroup) {
                $cellContent = $this->convertTableValue($row[$codeGroup]);

                if ($cellContent !== '' && $cellContent !== []) {
                    $data[$type][$codeGroup] = $cellContent;
                }
            }
        }

        return $data;
    }

    private function readLocalisedListFromTable(TableNode $table): array
    {
        $data = [];
        foreach ($table as $row) {
            $locale = $row['locale'];
            $value = $this->convertTableValue($row['value']);

            $data[$locale] = $value;
        }

        return $data;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function convertTableValue($value)
    {
        $matches = [];
        if (preg_match('/^\[(.*)\]$/', $value, $matches) !== 1) {
            return $value;
        }

        if ($matches[1] === '') {
            return [];
        }

        return explode(',', $matches[1]);
    }
}

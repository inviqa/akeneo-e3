<?php

namespace AkeneoE3\Tests\Acceptance\bootstrap;

use AkeneoE3\Domain\Exception\TransformException;
use AkeneoE3\Domain\Resource\Attribute;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\Property;
use AkeneoE3\Domain\Resource\ResourceType;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Webmozart\Assert\Assert;

class UpdateBehaviorContext implements Context
{
    private Resource $resource;

    private ResourceType $resourceType;

    /**
     * @Given an original resource:
     */
    public function readOriginalResource(PyStringNode $json): void
    {
        $data = json_decode($json, true);
        $this->resourceType = ResourceType::create(isset($data['identifier']) ? 'product' : 'object');

        $this->resource = Resource::fromArray($data, $this->resourceType);
    }

    /**
     * @When I apply the patch request body
     */
    public function applyPatchRequestBody(PyStringNode $json): void
    {
        $patch = json_decode($json, true);

        try {
            $attributeValues = $patch['values'] ?? [];
            $this->setAttributeValues($attributeValues);
            unset($patch['values']);
            $this->applyPropertyValues($patch);
        } catch (TransformException $e) {
            if ($e->canBeSkipped() === true) {
                return;
            }
        }
    }

    /**
     * @Then the resulting resource should be:
     */
    public function theResultingResourceShouldBe(PyStringNode $json): void
    {
        $expected = json_decode($json, true);

        Assert::eq($expected, $this->resource->toArray());
    }

    private function setAttributeValues(array $attributeValues): void
    {
        foreach ($attributeValues as $fieldName => $values) {
            foreach ($values as $value) {
                $this->resource->set(
                    Attribute::create(
                        $fieldName,
                        $value['scope'],
                        $value['locale']
                    ),
                    $value['data']
                );
            }
        }
    }

    private function applyPropertyValues(array $properties): void
    {
        foreach ($properties as $fieldName => $value) {
            $this->resource->set(Property::create($fieldName), $value);
        }
    }
}

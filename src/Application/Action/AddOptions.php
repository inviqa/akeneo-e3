<?php

declare(strict_types=1);

namespace AkeneoEtl\Application\Action;

use LogicException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddOptions
{
    private string $field;

    private ?string $locale;

    private ?string $scope;

    /**
     * @var mixed|null
     */
    private $items;

    private ?string $expression;

    /**
     * @param mixed $items
     */
    private function __construct(
        string $field,
        ?string $locale,
        ?string $scope,
        ?array $items,
        ?string $expression
    ) {
        $this->field = $field;
        $this->locale = $locale;
        $this->scope = $scope;
        $this->items = $items;
        $this->expression = $expression;
    }

    public static function fromArray(array $array): self
    {
        $resolver = new OptionsResolver();

        $resolver
            ->setRequired('field')
            ->setDefined('scope')
            ->setDefined('locale')
            ->setDefined('items')
            ->setDefined('expression')
            ->setAllowedTypes('items', ['array'])
            ->setAllowedTypes('locale', ['string', 'null'])
            ->setAllowedTypes('scope', ['string', 'null'])
            ->setAllowedTypes('expression', ['string', 'null'])
            ->resolve($array);

        if (array_key_exists('items', $array) === false &&
            array_key_exists('expression', $array) === false) {
            throw new LogicException('One of the options `items` or `expression` are required for the set action.');
        }

        if (isset($array['items']) === true &&
            isset($array['expression']) === true) {
            throw new LogicException('Only one of the options `items` or `expression` is required for the set action.');
        }

        return new self(
            $array['field'],
            $array['locale'] ?? null,
            $array['scope'] ?? null,
            $array['items'] ?? null,
            $array['expression'] ?? null,
        );
    }

    public function getFieldName(): string
    {
        return $this->field;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getExpression(): ?string
    {
        return $this->expression;
    }
}

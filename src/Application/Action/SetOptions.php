<?php

namespace AkeneoEtl\Application\Action;

use LogicException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SetOptions
{
    private string $field;

    private ?string $locale;

    private ?string $scope;

    /**
     * @var mixed|null
     */
    private $value;

    private ?string $expression;

    /**
     * @param mixed $value
     */
    private function __construct(
        string $field,
        ?string $locale,
        ?string $scope,
        $value,
        ?string $expression
    ) {
        if ($value === null && $expression === null) {
            throw new LogicException('One of the options `field` or `expression` are required for the set action.');
        }

        $this->field = $field;
        $this->locale = $locale;
        $this->scope = $scope;
        $this->value = $value;
        $this->expression = $expression;
    }

    public static function fromArray(array $array): self
    {
        $resolver = new OptionsResolver();

        $resolver
            ->setRequired('field')
            ->setDefaults([
                'locale' => null,
                'scope' => null,
            ])
            ->setDefined('value')
            ->setDefined('expression')
            ->setAllowedTypes('locale', ['string', 'null'])
            ->setAllowedTypes('scope', ['string', 'null'])
            ->setAllowedTypes('expression', ['string', 'null'])
            ->resolve($array);

        return new self(
            $array['field'],
            $array['locale'] ?? null,
            $array['scope'] ?? null,
            $array['value'] ?? null,
            $array['expression'] ?? null,
        );
    }

    public function getField(): string
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

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getExpression(): ?string
    {
        return $this->expression;
    }
}

<?php

namespace AkeneoEtl\Application\TransformerStep;

use AkeneoEtl\Domain\TransformerStep;
use Closure;
use Symfony\Component\String\Slugger\AsciiSlugger;

class Slugger implements TransformerStep
{
    private AsciiSlugger $slugger;

    private string $source;

    private string $destination;

    public function __construct(array $options)
    {
        $this->source = $options['source'];
        $this->destination = $options['destination'];

        $this->slugger = new AsciiSlugger();
    }

    public function getType(): string
    {
        return 'slug';
    }

    public function transform(array $item, Closure $traceCallBack = null): ?array
    {
        //$locale = 'pt_PT';
        $title = TransformerUtils::getAttributeValue($item, $this->source);  // $this->getAttributeValue($item['values']['name'], null, $locale);

        $slug = $this->slugger->slug(trim($title['data'] ?? ''), '-');
        $slug = $slug->lower()->toString();

        // if provided scope and locale then array
        // otherwise: simple value
        return TransformerUtils::createAttributeValues($this->destination, $slug);
    }
}

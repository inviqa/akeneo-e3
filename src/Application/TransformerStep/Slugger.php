<?php

namespace App\Application\TransformerStep;

use App\Domain\TransformerStep;
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

    public function getName(): string
    {
        return 'slug';
    }

    public function transform(array $item): array
    {
        //$locale = 'pt_PT';
        $title = TransformerUtils::getAttributeValue($item, $this->source);  // $this->getAttributeValue($item['values']['name'], null, $locale);

        $slug = $this->slugger->slug(trim($title['data'] ?? ''), '-');
        $slug = $slug->lower()->toString();

        return TransformerUtils::createAttributeValues($this->destination, $slug);
    }
}

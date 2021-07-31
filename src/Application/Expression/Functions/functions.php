<?php

namespace App\Application\Expression\Functions {

    use Symfony\Component\String\Slugger\AsciiSlugger;

    function slug(string $string): string
    {
        $slugger = new AsciiSlugger();

        return $slugger->slug($string, '-');
    }
}

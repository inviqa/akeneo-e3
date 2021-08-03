<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->in(__DIR__);

$config = new PhpCsFixer\Config();

return $config->setRules([
            '@PSR12' => true,
            'strict_param' => false,
            'array_syntax' => ['syntax' => 'short'],
        ]
    )
//    ->setRiskyAllowed(false)
    ->setFinder($finder);;

#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use AkeneoEtl\Infrastructure\Command;
use Symfony\Component\Console\Application;

$stepFactory = new \AkeneoEtl\Application\TransformerStepFactory();

$factory = new \AkeneoEtl\Infrastructure\EtlFactory();
$connectionProfileReader = new \AkeneoEtl\Infrastructure\ConnectionProfile\YamlReader();
$etlProfileReader = new \AkeneoEtl\Infrastructure\EtlProfile\YamlReader($stepFactory);

$application = new Application('AkeneoEtl', 'git_version');
$application->add(new Command\TransformProductsCommand($factory, $connectionProfileReader, $etlProfileReader));
$application->run();

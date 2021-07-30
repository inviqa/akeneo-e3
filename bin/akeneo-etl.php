#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use App\Infrastructure\Command;
use Symfony\Component\Console\Application;

$stepFactory = new \App\Application\TransformerStepFactory();

$factory = new \App\Infrastructure\EtlFactory();
$connectionProfileReader = new \App\Infrastructure\ConnectionProfile\YamlReader();
$etlProfileReader = new \App\Infrastructure\EtlProfile\YamlReader($stepFactory);

$application = new Application('Cliph', '0.1-dev');
$application->add(new Command\TransformProductsCommand($factory, $connectionProfileReader, $etlProfileReader, '.'));
$application->run();

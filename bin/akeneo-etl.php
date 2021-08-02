#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use AkeneoEtl\Application\TransformerStepFactory;
use AkeneoEtl\Infrastructure\Command;
use AkeneoEtl\Infrastructure\ConnectionProfile\YamlReader;
use AkeneoEtl\Infrastructure\EtlFactory;
use AkeneoEtl\Infrastructure\EtlProfile\ProfileFactory as EtlProfileFactory;
use Symfony\Component\Console\Application;
use Symfony\Component\Validator\ValidatorBuilder;

$stepFactory = new TransformerStepFactory();

$validatorBuilder = new ValidatorBuilder();


$factory = new EtlFactory();
$connectionProfileReader = new YamlReader();
$etlProfileFactory = new EtlProfileFactory($stepFactory, $validatorBuilder->getValidator());

$application = new Application('AkeneoEtl', 'git_version');
$application->add(new Command\TransformProductsCommand($factory, $connectionProfileReader, $etlProfileFactory));
$application->run();

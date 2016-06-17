<?php

namespace Osl\SonosCron;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

require __DIR__ . '/../vendor/autoload.php';

$container = new ContainerBuilder();

$loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../config'));
$loader->load('parameters.yml');
$loader->load('services.yml');

$container
    ->addCompilerPass(new HandlerCompilerPass())
    ->compile();

$container->get('sonos_cron.processor')->process();
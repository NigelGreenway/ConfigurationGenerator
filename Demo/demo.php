<?php

$s = microtime(true);

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/configuration.php';

$container = new \League\Container\Container($config['services']);

$mailer = $container->get('mailer.service');

echo sprintf('<p>%s</p>', $mailer->demo());
echo sprintf('<p>%.4f</p>', microtime(true) - $s);
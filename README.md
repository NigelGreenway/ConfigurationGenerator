# Colonel Configuration Generator

This is a generator in its basic form. Using the PHP reflection classes, it allows to use annotations to generate your config files. This is useful if you want to either save them keystrokes or have a large application and are using:

- [League\Route](https://github.com/thephpleague/container)
- ~~[Colonel\Demander](https://github.com/colonel/demander)~~ **Not available as of yet**

> The reflection class is **only** used for the building of the configuration file.

# Usage

## Application Console

The package runs off [Symfony/Console](https://github.com/symfony/Console). If you do not have this setup already and runnng current console commands, you can use the basic config:

```php
#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new \Colonel\ConfigurationGenerator\ConfigurationGeneratorCommand);
$application->run();
```

## Container

```php
<?php

namespace MyApp\Service;

/**
 * @Service(your.conanical.service.name)
 */
class EmailService
{
    private $swiftMailerAdaptor;

    public function __constuct(
        SwiftMailerAdaptorInterface $swiftMailerAdaptor
    ) {
        $this->swiftMailerAdaptor = $swiftMailerAdaptor;
    }
}
```

Run the generator:

```shell
$ php ./path/to/app configuration:generate \
    config /path/to/configuration.[php|json] \
    dir ./path/to/root/namespace [dir ./path/to/another/root/namespace]
```

*There is also a help argument:*

```shell
$ php ./path/to/app configuration:generate --help
Usage:
 configuration:generate [--indent="..."] config dir1 ... [dirN]

Arguments:
 config                Configuration file of project
 dir                   Directory array of project files with configs

Options:
 --indent              Amount of spaces per indentation (default: 4)
 --help (-h)           Display this help message
 --quiet (-q)          Do not output any message
 --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 --version (-V)        Display this application version
 --ansi                Force ANSI output
 --no-ansi             Disable ANSI output
 --no-interaction (-n) Do not ask any interactive question
 
```

The generated configuration is:

```php
<?php

return [
    'service' => [
        'di' => [
            'swiftmailer.adaptor' => [
                'class' => 'MyApp\Service\SwiftmailAdaptor',
            ],
            'your.conanical.service.name' => [
                'class' => 'MyApp\Service\EmailService',
                'arguments' => [
                    'swiftmailer.adaptor',
                ],
            ],
        ],
    ],
];
```


# Colonel Configuration Generator

This is a generator in its basic form. Using the PHP reflection classes, it allows to use annotations to generate your config files. This is useful if you want to either save them keystrokes or have a large application and are using:

- [League\Route](https://github.com/thephpleague/container)
- [Colonel\Demander](https://github.com/colonel/demander)

> The reflection class is **only** used for the building of the configuration file.

# Usage

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

The generate configuration is:

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


<?php

return [
    'services' => [
        'di' => [
            'a.dependency.service' => [
                'class' => 'Colonel\\ConfigurationGenerator\\Demo\\Services\\ADependencyService',
            ],
            'this.is.my.service' => [
                'class' => 'Colonel\\ConfigurationGenerator\\Demo\\Services\\FirstDemoService',
            ],
            'mailer.service' => [
                'class' => 'Colonel\\ConfigurationGenerator\\Demo\\Services\\MailerService',
                'arguments' => [
                    'Colonel\\ConfigurationGenerator\\Demo\\Services\\ADependencyService',
                ],
            ],
        ],
    ],
    'debug' => true,
];
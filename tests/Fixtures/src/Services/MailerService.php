<?php

namespace Colonel\ConfigurationGenerator\Test\Fixtures\src\Services;

/**
 * @Service('App.Mailer.Service')
 */
final class MailerService
{
    /** @var ADependencyService  */
    private $dependencyService;

    /**
     * @param ADependencyService $dependencyService
     */
    public function __construct(
        ADependencyService $dependencyService
    ) {
        $this->dependencyService = $dependencyService;
    }
}
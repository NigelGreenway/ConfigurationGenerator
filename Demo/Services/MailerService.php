<?php

namespace Colonel\ConfigurationGenerator\Demo\Services;

/**
 * @service('Mailer.Service')
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

    public function demo()
    {
        echo $this->dependencyService->getParam();
    }
}
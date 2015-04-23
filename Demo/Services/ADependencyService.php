<?php

namespace Colonel\ConfigurationGenerator\Demo\Services;

/**
 * @Service('a.dependency.service')
 * @Tag
 */
final class ADependencyService
{
    private $param = 'Hello';

    public function __construct()
    {}

    public function getParam()
    {
        return $this->param;
    }
}
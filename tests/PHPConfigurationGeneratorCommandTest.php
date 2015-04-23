<?php
/**
 * ...
 *
 * @author Nigel Greenway <nigel_greenway@me.com>
 * @license ...
 */

namespace Colonel\ConfigurationGenerator\Test;

use Colonel\ConfigurationGenerator\ConfigurationGeneratorCommand;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class PHPConfigurationGeneratorCommandTest extends PHPUnit_Framework_TestCase
{
    /** @var  Application */
    private $application;

    /** @var  ConfigurationGeneratorCommand */
    private $command;

    /** @var  CommandTester */
    private $commandTester;

    public function setUp()
    {
        $this->application = new Application;
        $this->application->add(new ConfigurationGeneratorCommand);

        $this->command = $this->application->find('configuration:generate');

        $this->commandTester = new CommandTester($this->command);
    }

    public function test_generates_with_default_indentation_of_4_spaces()
    {
        $this->commandTester->execute([
            'command'  => $this->command->getName(),
            'config'   => realpath(__DIR__ . '/fixtures/configuration.php'),
            'dir'      => [
                realpath(__DIR__ . '/fixtures/src/Services'),
            ]
        ]);

        $file = file_get_contents(__DIR__ . '/fixtures/configuration.php', 'r');

        $file = explode("\n", $file);

        $this->assertRegExp('/^[\ ]{4,4}/', $file[3]);
        $this->assertRegExp('/^[\ ]{8,8}/', $file[4]);
        $this->assertRegExp('/^[\ ]{12,12}/', $file[5]);
        $this->assertRegExp('/^[\ ]{16,16}/', $file[6]);
    }

    public function test_generates_with_default_indentation_of_8_spaces()
    {
        $this->commandTester->execute([
            'command'  => $this->command->getName(),
            '--indent' => 8,
            'config'   => realpath(__DIR__ . '/fixtures/configuration.php'),
            'dir'      => [
                realpath(__DIR__ . '/fixtures/src'),
            ]
        ]);

        $file = file_get_contents(__DIR__ . '/fixtures/configuration.php', 'r');

        $file = explode("\n", $file);

        $this->assertRegExp('/^[\ ]{8,8}/', $file[3]);
        $this->assertRegExp('/^[\ ]{16,16}/', $file[4]);
        $this->assertRegExp('/^[\ ]{16,24}/', $file[5]);
        $this->assertRegExp('/^[\ ]{32,32}/', $file[6]);
    }

    public function test_generates_with_correct_classes_added()
    {
        $this->commandTester->execute([
            'command'  => $this->command->getName(),
            '--indent' => 8,
            'config'   => realpath(__DIR__ . '/fixtures/configuration.php'),
            'dir'      => [
                realpath(__DIR__ . '/fixtures/src'),
            ]
        ]);

        $config = require __DIR__ . '/fixtures/configuration.php';

        $this->assertArrayHasKey('app.mailer.service', $config['services']['di']);
        $this->assertArrayHasKey('app.dependency.service', $config['services']['di']);
    }

    public function test_generated_config_only_adds_classes_with_service_annotation()
    {
        $this->commandTester->execute([
            'command'  => $this->command->getName(),
            'config'   => realpath(__DIR__ . '/fixtures/configuration.php'),
            'dir'      => [
                realpath(__DIR__ . '/fixtures/src'),
            ]
        ]);

        $config = require __DIR__ . '/fixtures/configuration.php';

        foreach($config['services']['di'] as $key => $value) {
            $this->assertNotEquals('Colonel\ConfigurationGenerator\Test\Fixtures\src\Model\User', $value['class']);
        }
    }

    /**
     * @expectedException \Colonel\ConfigurationGenerator\DuplicateServiceException
     */
    public function test_generator_throws_exception_on_duplicate_keys()
    {
        $this->commandTester->execute([
            'command'  => $this->command->getName(),
            'config'   => realpath(__DIR__ . '/fixtures/configuration.php'),
            'dir'      => [
                realpath(__DIR__ . '/fixtures/src'),
                realpath(__DIR__ . '/fixtures/src'),
            ]
        ]);

        $config = require __DIR__ . '/fixtures/configuration.php';
    }

    /**
     * Rebuild the config for the next session of testing and
     * destroy the built params
     */
    public function tearDown()
    {
        $content = <<<DATA
<?php

return [
    'services' => [
        'di' => [
            'this.is.a.test' => [
                'class' => 'My\\Class',
            ]
        ],
    ],
    'debug' => true,
];
DATA;

        file_put_contents(
            realpath(__DIR__ . '/fixtures/configuration.php'),
            $content
        );

        $this->application
        = $this->command
        = $this->commandTester
        = null;
    }
}
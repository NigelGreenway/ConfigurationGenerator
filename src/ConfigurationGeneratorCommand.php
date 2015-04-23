<?php
/**
 * The Colonel Framework
 *
 * @author Nigel Greenway <nigel_greenway@me.com>
 * @license MIT
 */

namespace Colonel\ConfigurationGenerator;

use DirectoryIterator;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A command to build a configuration file
 *
 * @package Colonel\ConfigurationGenerator
 * @author  Nigel Greenway <nigel_greenway@me.com>
 */
final class ConfigurationGeneratorCommand extends Command
{
    /** @const JSON_CONFIG_EXTENSION The JSON extension type */
    const JSON_CONFIG_EXTENSION = 'json';

    /** @const PHP_CONFIG_EXTENSION  The PHP extension type */
    const PHP_CONFIG_EXTENSION  = 'php';

    /** @var string */
    private $configuration;

    /** @var  string */
    private $configurationType;

    /** @var array  */
    private $map = [];

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('configuration:generate')
            ->setDescription('Setup Service Container config')
            ->addArgument(
                'config',
                InputArgument::REQUIRED,
                'Configuration file of project'
            )
            ->addArgument(
                'dir',
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                'Directory array of project files with config`s'
            )
            ->addOption(
                'indent',
                null,
                InputArgument::OPTIONAL,
                'Amount of spaces per indentation',
                4
            )
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configurationPath = $input->getArgument('config');
        $directories       = $input->getArgument('dir');

        $progress = new ProgressBar($output, count($directories));

        switch ($extension = pathinfo($configurationPath)['extension']) {
            case "json":
                $this->configuration     = json_decode(file_get_contents($configurationPath), true);
                $this->configurationType = self::JSON_CONFIG_EXTENSION;
                break;
            case "php":
                $this->configuration     = require $configurationPath;
                $this->configurationType = self::PHP_CONFIG_EXTENSION;
                break;
            default:
                throw new \Exception(sprintf('The file type [%s] is not currently supported.', $extension));
                break;
        }

        $progress->start();

        foreach($directories as $directory) {
            array_map(function($class) {
                $this->findConfigurationAnnotations($class);
            }, $this->recursiveDirectoryIterator($directory));
            $progress->advance();
        }

        $this->mergeContent();

        $this->generateConfigurationFile($input, $configurationPath, $progress);
    }

    /**
     * Recursively look through the given directory
     * to find all php files
     *
     * @param string $directory
     * @param array  $namespaces
     *
     * @return array
     */
    private function recursiveDirectoryIterator($directory, array $namespaces = [])
    {
        $iterator = new DirectoryIterator($directory);

        foreach ($iterator as $info) {
            // Ignore `.` and `..` directories
            if ($info->isDot() == true) {
                continue;
            }
            // Recursive iteration
            if ($info->isDir()) {
                $namespaces = $this->recursiveDirectoryIterator($info->getPathname(), $namespaces);
            }
            // Check for php only files
            if (
                $info->isFile()
                && $info->getExtension() == 'php'
            ) {
                if ($this->buildFQCN($info->getPathname()) !== null) {
                    $namespaces[] = $this->buildFQCN($info->getPathname());
                }
            }
        }

        return $namespaces;
    }

    /**
     * Build a full qualified class name from the file contents
     *
     * @param  string $file
     *
     * @throws InvalidArgumentException If no namespace or class have been found in the file
     *
     * @return string
     */
    private function buildFQCN($file)
    {
        $fileContents = file_get_contents($file, 'r');

        preg_match('(namespace\s+(.+?);)', $fileContents, $namespaceMatches);
        preg_match('(.*class\s+(\w+))',    $fileContents, $classMatches);

        if (
            !isset($namespaceMatches[1])
            && !isset($classMatches[1])
        ) {
            return null;
        };

        $namespace = $namespaceMatches[1];
        $class     = $classMatches[1];

        return sprintf('%s\\%s', $namespace, $class);
    }

    /**
     * Find if a service class has been found by
     * the annotation. eg: @service("example.service")
     *
     * @param string $fqcn The Fully qualified name class name
     *
     * @throws
     *
     * @return void
     */
    private function findConfigurationAnnotations($fqcn)
    {
        $reflection = new ReflectionClass($fqcn);

        $docComment = $reflection->getDocComment();

        if ($docComment === false) {
            return null;
        }

        preg_match('/@(service|Service)\(\'([a-zA-Z.]*)\'\)/', strtolower($docComment), $annotation);

        $type          = $annotation[1];
        $canonicalName = $annotation[2];

        switch ($type) {
            case ServiceAnnotationParser::TYPE:

                if (array_key_exists(ServiceAnnotationParser::TYPE, $this->map) === false) {
                    $this->map[ServiceAnnotationParser::TYPE]['di'] = [];
                }

                if (array_key_exists($canonicalName, $this->map[ServiceAnnotationParser::TYPE]['di']) === true) {
                    throw new DuplicateServiceException(
                        $canonicalName,
                        $fqcn,
                        $this->map[ServiceAnnotationParser::TYPE]['di'][$canonicalName]['class']
                    );
                }

                $this->map[ServiceAnnotationParser::TYPE]['di'][$canonicalName] = ServiceAnnotationParser::parse($fqcn);
                break;
            default:
                break;
        }
    }

    /**
     * Merge the newly built content with the already
     * existing content by its key
     *
     * @return void
     */
    private function mergeContent()
    {
        foreach ($this->map as $key => $value) {
            switch ($key) {
                case ServiceAnnotationParser::TYPE:
                    $mergedServices = array_merge(
                        $this->configuration['services'],
                        $this->map[ServiceAnnotationParser::TYPE]
                    );
                    $this->configuration['services'] = $mergedServices;
                    break;
            }
        }
    }

    /**
     * Generate the configuration file with the new
     * data
     *
     * @param InputInterface $input
     * @param $configurationPath
     * @param $progress
     *
     * @return void
     */
    private function generateConfigurationFile(InputInterface $input, $configurationPath, $progress)
    {
        switch ($this->configurationType) {
            case self::JSON_CONFIG_EXTENSION:
                JSONConfigurationWriter::write(
                    $configurationPath,
                    $this->configuration,
                    $input->getOption('indent')
                );
                $progress->finish();
                break;
            case self::PHP_CONFIG_EXTENSION:
                PHPConfigurationWriter::write(
                    $configurationPath,
                    $this->configuration,
                    $input->getOption('indent')
                );
                $progress->finish();
                break;
        }
    }
}
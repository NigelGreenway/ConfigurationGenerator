<?php
/**
 * The Colonel Framework
 *
 * @author Nigel Greenway <nigel_greenway@me.com>
 * @license MIT
 */

namespace Colonel\ConfigurationGenerator;

use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * Used to write the new PHP array data to
 * a json file
 *
 * @package Colonel\ConfigurationGenerator
 * @author  Nigel Greenway <nigel_greenway@me.com>
 */
final class JSONConfigurationWriter implements ConfigurationWriterInterface
{
    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * Class constructor
     */
    private function __construct()
    {
        $this->twig = new Twig_Environment(
            new Twig_Loader_Filesystem(__DIR__ . '/Template')
        );
    }

    /**
     * {@inheritDoc}
     */
    public static function write(
        $configurationPath,
        array $configuration = [],
        $spaces = 4
    ) {
        $output = (new self)->twig->render('config.json.twig', ['configuration' => $configuration]);

        $indentation = str_pad('', $spaces, ' ', STR_PAD_LEFT);

        $output = preg_replace('/    /', $indentation, $output);

        file_put_contents(
            $configurationPath,
            $output
        );
    }
}
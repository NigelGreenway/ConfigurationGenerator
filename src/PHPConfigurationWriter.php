<?php
/**
 * The Colonel Framework
 *
 * @author Nigel Greenway <nigel_greenway@me.com>
 * @license MIT
 */

namespace Colonel\ConfigurationGenerator;

use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;
use Twig_SimpleFunction;

/**
 * Used to write the new PHP array data to
 * a php file
 *
 * @package Colonel\ConfigurationGenerator
 * @author  Nigel Greenway <nigel_greenway@me.com>
 */
final class PHPConfigurationWriter implements ConfigurationWriterInterface
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
            new Twig_Loader_Filesystem(__DIR__),
            [
                'debug' => true,
            ]
        );

        $varExportFunction = new Twig_SimpleFunction('var_export', 'var_export');

        $this->twig->addExtension(new Twig_Extension_Debug());
        $this->twig->addFunction($varExportFunction);
    }

    /**
     * {@inheritDoc}
     */
    public static function write(
        $configurationPath,
        array $configuration = [],
        $spaces = 4
    ) {
        $output = (new self)->twig->render('config.php.twig', ['configuration' => $configuration]);

        $indentation = str_pad('', $spaces, ' ', STR_PAD_LEFT);

        $output = str_replace("array (", "[", $output); // Replace old style array with new
        $output = str_replace(")",']', $output); // Replace old style array with new
        $output = preg_replace('/(=>\s\n\s+)/', '=> ', $output); // Bring array declaration `[` on the same line as `=>`
        $output = preg_replace('(\d+ => )', '', $output); // Remove key if it is an integer
        $output = preg_replace('/  /', $indentation, $output);

        file_put_contents(
            $configurationPath,
            $output
        );
    }
}
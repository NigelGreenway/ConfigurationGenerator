<?php
/**
 * The Colonel Framework
 *
 * @author Nigel Greenway <nigel_greenway@me.com>
 * @license MIT
 */

namespace Colonel\ConfigurationGenerator;

/**
 * Interface for Configuration Writers
 *
 * @package Colonel\ConfigurationGenerator
 * @author  Nigel Greenway <nigel_greenway@me.com>
 */
interface ConfigurationWriterInterface
{
    /**
     * Write the configuration array to a
     * file
     *
     * @param string  $configurationPath
     * @param array   $configuration
     * @param integer $spaces
     *
     * @return void
     */
    public static function write(
        $configurationPath,
        array $configuration = [],
        $spaces
    );
}
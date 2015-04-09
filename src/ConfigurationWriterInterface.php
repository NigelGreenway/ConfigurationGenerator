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
     * @param array $configuration
     *
     * @return void
     */
    public function write(array $configuration = []);
}
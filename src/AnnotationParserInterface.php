<?php
/**
 * The Colonel Framework
 *
 * @author Nigel Greenway <nigel_greenway@me.com>
 * @license MIT
 */

namespace Colonel\ConfigurationGenerator;

/**
 * Interface for the Annotation Parsers
 *
 * @package Colonel\ConfigurationGenerator
 * @author  Nigel Greenway <nigel_greenway@me.com>
 */
interface AnnotationParserInterface
{
    /**
     * Parse the annotation for the required
     * information and return an array
     *
     * @param strong $fqcn The Fully Qualified Class Name of the service
     *
     * @return array
     */
    public static function parse($fqcn);
}
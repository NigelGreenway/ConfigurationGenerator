<?php
/**
 * The Colonel Framework
 *
 * @author Nigel Greenway <nigel_greenway@me.com>
 * @license MIT
 */

namespace Colonel\ConfigurationGenerator;

use ReflectionMethod;
use ReflectionParameter;

/**
 * Class ServiceAnnotationParser
 * @package Colonel\ConfigurationGenerator
 */
class ServiceAnnotationParser implements AnnotationParserInterface
{
    /** @const TYPE The type of dependency the parser handles */
    const TYPE = 'service';

    /** {@inheritDoc} */
    public static function parse($fqcn)
    {
        $mapping = [];

        $mapping['class'] = $fqcn;

        $constructor = new ReflectionMethod($fqcn, '__construct');

        foreach($constructor->getParameters() as $key => $parameter) {
            $parameter = new ReflectionParameter([$fqcn, '__construct'], ($key));
            $mapping['arguments'][] = $parameter->getClass()->getName();
        }

        return $mapping;
    }
}
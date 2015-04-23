<?php
/**
 * The Colonel Framework
 *
 * @author Nigel Greenway <nigel_greenway@me.com>
 * @license MIT
 */

namespace Colonel\ConfigurationGenerator;

use Exception;

/**
 * A duplicate service canonical name exception
 *
 * @package Colonel\ConfigurationGenerator
 * @author  Nigel Greenway <nigel_greeway@me.com>
 */
final class DuplicateServiceException extends Exception
{
    /**
     * @param string $canonicalName
     * @param string $duplicateFqcn
     * @param string $firstFqcn
     *
     * {@inheritDoc}
     */
    public function __construct($canonicalName, $duplicateFqcn, $firstFqcn)
    {
        parent::__construct(
            sprintf(
                "A duplicate Service name of [%s] has been found in [%s].\n\nThe original can be found in [%s]",
                $canonicalName,
                $duplicateFqcn,
                $firstFqcn
            )
        );
    }
}
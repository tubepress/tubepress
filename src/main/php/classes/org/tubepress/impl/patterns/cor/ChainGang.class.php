<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_spi_patterns_cor_Command',
    'org_tubepress_spi_patterns_cor_Chain',
    'org_tubepress_impl_log_Log',
));

/**
 * Implementation of the "chain" in the chain-of-responsbility pattern.
 */
class org_tubepress_impl_patterns_cor_ChainGang implements org_tubepress_spi_patterns_cor_Chain
{
    const LOG_PREFIX = 'Chain Gang';

    /**
     * Executes the given commands with the given context.
     *
     * @param array $context  An array of context elements (may be empty).
     * @param array $commands An array of org_tubepress_spi_patterns_cor_Command class names to execute.
     *
     * @return unknown The result of the command execution.
     */
    public function execute($context, $commands)
    {
        /* sanity checkin' */
        if (!is_array($commands)) {
            throw new Exception('execute() requires an array of commands');
        }

        if (!is_object($context)) {
            throw new Exception('execute() requires an object to be passed as the context');
        }

        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        /* run the first command that wants to handle this */
        foreach ($commands as $commandName) {

            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Seeing if "%s" wants to handle execution', $commandName);

            $command = $ioc->get($commandName);

            if (!is_a($command, 'org_tubepress_spi_patterns_cor_Command')) {
                throw new Exception("$commandName does not implement org_tubepress_spi_patterns_cor_Command");
            }

            $ableToHandle = call_user_func_array(array($command, 'execute'), array($context));

            if ($ableToHandle === true) {
                org_tubepress_impl_log_Log::log(self::LOG_PREFIX, '%s handled execution', $commandName);
                return true;
            }
        }

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'None of the supplied commands were able to handle the execution: ' . implode("', '", $commands));

        return false;
    }

    /**
     * Create a context object for the chain to work with.
     *
     * @return object An instance of stdClass for the commands to work with.
     */
    function createContextInstance()
    {
        return new stdClass;
    }
}

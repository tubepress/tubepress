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

/**
 * Chain in the chain-of-responsibility pattern.
 */
interface org_tubepress_spi_patterns_cor_Chain
{
    const _ = 'org_tubepress_spi_patterns_cor_Chain';

    /**
     * Executes the given commands with the given context.
     *
     * @param array $context          An array of context elements (may be empty).
     * @param array $commandInstances An array of org_tubepress_spi_patterns_cor_Command class names to execute.
     *
     * @throws Exception If none of the commands can handle execution.
     *
     * @return void
     */
    function execute($context, $commandInstances);

    /**
     * Create a context object for the chain to work with.
     *
     * @return object An instance of stdClass for the commands to work with.
     */
    function createContextInstance();
}

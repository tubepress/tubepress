<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_impl_ioc_Definition extends ehough_iconic_Definition implements tubepress_api_ioc_DefinitionInterface
{
    /**
     * Sets a specific argument
     *
     * @param integer $index
     * @param mixed   $argument
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @throws OutOfBoundsException When the replaced argument does not exist
     *
     * @api
     * @since 3.1.0
     */
    public function replaceArgument($index, $argument)
    {
        try {

            return parent::replaceArgument($index, $argument);

        } catch (ehough_iconic_exception_OutOfBoundsException $e) {

            throw new OutOfBoundsException($e);
        }
    }


    /**
     * Gets an argument to pass to the service constructor/factory method.
     *
     * @param integer $index
     *
     * @return mixed The argument value
     *
     * @throws OutOfBoundsException When the argument does not exist
     *
     * @api
     * @since 3.1.0
     */
    public function getArgument($index)
    {
        try {

            return parent::getArgument($index);

        } catch (ehough_iconic_exception_OutOfBoundsException $e) {

            throw new OutOfBoundsException($e);
        }
    }

    /**
     * Adds a method to call after service initialization.
     *
     * @param string $method    The method name to call
     * @param array  $arguments An array of arguments to pass to the method call
     *
     * @return tubepress_api_ioc_DefinitionInterface The current instance
     *
     * @throws InvalidArgumentException on empty $method param
     *
     * @api
     * @since 3.1.0
     */
    public function addMethodCall($method, array $arguments = array())
    {
        try {

            return parent::addMethodCall($method, $arguments);

        } catch (ehough_iconic_exception_InvalidArgumentException $e) {

            throw new InvalidArgumentException($e);
        }
    }
}
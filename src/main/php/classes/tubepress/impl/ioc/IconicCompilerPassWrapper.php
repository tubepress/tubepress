<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_impl_ioc_IconicCompilerPassWrapper implements ehough_iconic_compiler_CompilerPassInterface
{
    /**
     * @var tubepress_api_ioc_CompilerPassInterface
     */
    private $_delegate;

    public function __construct(tubepress_api_ioc_CompilerPassInterface $tubePressCompilerPass)
    {
        $this->_delegate = $tubePressCompilerPass;
    }

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ehough_iconic_ContainerBuilder $container
     *
     * @throws RuntimeException If the
     *
     * @api
     */
    public function process(ehough_iconic_ContainerBuilder $container)
    {
        if (!($container instanceof tubepress_api_ioc_ContainerInterface)) {

            throw new RuntimeException('Compiler pass wrapper expected instance of tubepress_api_ioc_ContainerInterface');
        }

        /**
         * @var $container tubepress_api_ioc_ContainerInterface
         */
        $this->_delegate->process($container);
    }
}
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
class FakeCompilerPass implements tubepress_api_ioc_CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param tubepress_api_ioc_ContainerInterface $container
     *
     * @return void
     */
    public final function process(tubepress_api_ioc_ContainerInterface $container)
    {
        //do nothing
    }
}
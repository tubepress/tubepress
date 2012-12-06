<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class FakeCompilerPass implements ehough_iconic_api_compiler_ICompilerPass
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ehough_iconic_impl_ContainerBuilder $container
     *
     * @return void
     */
    public final function process(ehough_iconic_impl_ContainerBuilder $container)
    {
        //do nothing
    }
}
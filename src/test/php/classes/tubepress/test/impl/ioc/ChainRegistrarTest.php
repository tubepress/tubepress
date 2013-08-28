<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_impl_ioc_ChainRegistrar
 */
class tubepress_test_impl_ioc_ChainRegistrarTest extends tubepress_test_TubePressUnitTest implements ehough_chaingang_api_Command
{
    private $_bell;

    public function onSetup()
    {
        $this->_bell = false;
    }

    public function testBuildChain()
    {
        $result = tubepress_impl_ioc_ChainRegistrar::buildChain($this);

        $this->assertTrue($result instanceof ehough_chaingang_api_Chain);

        $result->execute(new ehough_chaingang_impl_StandardContext());

        $this->assertTrue($this->_bell);
    }

    public function execute(ehough_chaingang_api_Context $context)
    {
        $this->_bell = true;
    }
}
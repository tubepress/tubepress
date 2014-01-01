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

class tubepress_test_addons_wordpress_impl_message_WordPressMessageServiceTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_wordpress_impl_message_WordPressMessageService
     */
    private $_sut;

    public function onSetup()
    {
        $wrapper = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);
        $wrapper->shouldReceive('__')->andReturnUsing(function ($key) {

            return "[[$key]]";
        });

        $this->_sut = new tubepress_addons_wordpress_impl_message_WordPressMessageService($wrapper);
    }

    public function testGetKeyNoExists()
    {
        $this->assertEquals('', $this->_sut->_(''));
        $this->assertEquals('', $this->_sut->_(null));
    }

    public function testGetKey()
    {
        $result = $this->_sut->_('foo') === "[[foo]]";

        if (!$result) {

            print "foo did not resolve to [[foo]]";
        }

        $this->assertTrue($result);
    }
}
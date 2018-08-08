<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_wordpress_impl_translation_WpTranslator
 */
class tubepress_test_wordpress_impl_message_WpTranslatorTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_translation_WpTranslator
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_wpFunctions;

    public function onSetup()
    {
        $this->_wpFunctions = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);
        $this->_wpFunctions->shouldReceive('__')->andReturnUsing(function ($key) {

            return "[[$key]]";
        });

        $this->_sut = new tubepress_wordpress_impl_translation_WpTranslator($this->_wpFunctions);
    }

    public function testSetLocale()
    {
        $this->setExpectedException('LogicException', 'Use WPLANG to set WordPress locale');
        $this->_sut->setLocale('abc');
    }

    public function testGetLocale()
    {
        $this->_wpFunctions->shouldReceive('get_locale')->once()->andReturn('abc');

        $this->assertEquals('abc', $this->_sut->getLocale());
    }

    public function testGetKeyNoExists()
    {
        $this->assertEquals('', $this->_sut->trans(''));
        $this->assertEquals('', $this->_sut->trans(null));
    }

    public function testGetKey()
    {
        $result = $this->_sut->trans('foo') === '[[foo]]';

        if (!$result) {

            echo 'foo did not resolve to [[foo]]';
        }

        $this->assertTrue($result);
    }
}

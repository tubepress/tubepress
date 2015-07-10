<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_cache_html_impl_options_ui_FieldProvider<extended>
 */
class tubepress_test_htmlcache_impl_options_ui_FieldProviderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_jwplayer5_impl_options_ui_JwPlayerFieldProvider
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface[]
     */
    private $_mockFields;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockField;

    public function onSetup()
    {
        $this->_mockField = $this->mock('tubepress_app_api_options_ui_FieldInterface');
        $this->_mockFields = array($this->_mockField);

        $this->_sut = new tubepress_cache_html_impl_options_ui_FieldProvider(

            $this->_mockFields,
            array('foo' => 'bar')
        );
    }

    public function testDefaults()
    {
        $map = array(

            'foo' => 'bar',
        );

        $this->assertEquals(array(), $this->_sut->getCategories());
        $this->assertTrue($this->_sut->fieldsShouldBeInSeparateBoxes());
        $this->assertFalse($this->_sut->isAbleToBeFilteredFromGui());
        $this->assertEquals('HTML Cache', $this->_sut->getUntranslatedDisplayName());
        $this->assertEquals($map, $this->_sut->getCategoryIdsToFieldIdsMap());
        $this->assertEquals('field-provider-htmlcache', $this->_sut->getId());
        $this->assertSame($this->_mockFields, $this->_sut->getFields());
    }
}
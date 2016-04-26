<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_dailymotion_impl_embedded_DailymotionEmbeddedProvider
 */
class tubepress_test_dailymotion_impl_embedded_DailymotionEmbeddedProviderTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_dailymotion_impl_embedded_DailymotionEmbeddedProvider
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockContext;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockLangUtils;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockUrlFactory;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockMediaItem;

    public function onSetup() {

        $this->_mockUrlFactory = $this->mock(tubepress_api_url_UrlFactoryInterface::_);
        $this->_mockContext    = $this->mock(tubepress_api_options_ContextInterface::_);
        $this->_mockLangUtils  = $this->mock(tubepress_api_util_LangUtilsInterface::_);
        $this->_mockUrlFactory = $this->mock(tubepress_api_url_UrlFactoryInterface::_);
        $this->_mockMediaItem  = $this->mock('tubepress_api_media_MediaItem');

        $this->_sut = new tubepress_dailymotion_impl_embedded_DailymotionEmbeddedProvider(

            $this->_mockContext,
            $this->_mockLangUtils,
            $this->_mockUrlFactory
        );
    }

    public function testBasics()
    {
        $this->assertEquals('dailymotion', $this->_sut->getName());
        $this->assertEquals('Dailymotion', $this->_sut->getUntranslatedDisplayName());
        $this->assertEquals(array('dailymotion'), $this->_sut->getCompatibleMediaProviderNames());
        $this->assertEquals('single/embedded/dailymotion_iframe', $this->_sut->getTemplateName());
        $this->assertEquals(array(TUBEPRESS_ROOT . '/src/add-ons/provider-dailymotion/templates'), $this->_sut->getTemplateDirectories());
    }

    /**
     * @dataProvider getDataGetDataUrl
     */
    public function testGetDataUrl(array $context, array $query)
    {
        $mockUrl   = $this->mock('tubepress_api_url_UrlInterface');
        $mockQuery = $this->mock('tubepress_api_url_QueryInterface');

        foreach ($context as $key => $value) {

            $this->_mockContext->shouldReceive('get')->once()->with($key)->andReturn($value);
        }

        $mockUrl->shouldReceive('getQuery')->once()->andReturn($mockQuery);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('https://www.dailymotion.com/embed/video/xx')->andReturn($mockUrl);

        $this->_mockLangUtils->shouldReceive('booleanToStringOneOrZero')->atLeast(1)->andReturnUsing(function ($incoming) {

            $langutils = new tubepress_util_impl_LangUtils();

            return $langutils->booleanToStringOneOrZero($incoming);
        });

        $this->_mockMediaItem->shouldReceive('getId')->once()->andReturn('xx');

        $expected = array(
            tubepress_api_template_VariableNames::EMBEDDED_DATA_URL => $mockUrl,
            'player_id'                                             => 'player-id',
        );

        foreach ($query as $key => $value) {

            $mockQuery->shouldReceive('set')->once()->with($key, $value);
        }

        $actual = $this->_sut->getTemplateVariables($this->_mockMediaItem);

        $this->assertEquals($expected, $actual);
    }

    public function getDataGetDataUrl()
    {
        return array(
            array(
                array(
                    tubepress_dailymotion_api_Constants::OPTION_PLAYER_ID => 'player-id',
                    tubepress_api_options_Names::EMBEDDED_AUTOPLAY        => true,
                    tubepress_api_options_Names::EMBEDDED_SHOW_INFO       => true,
                ),
                array(
                    'autoplay'             => '1',
                    'ui-start_screen_info' => '1',
                    'id'                   => 'player-id',
                ),
            ),
        );
    }
}

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
class tubepress_plugins_youtube_impl_patterns_ioc_YouTubeIocContainerExtensionTest extends TubePressUnitTest
{
    /**
     * @var tubepress_plugins_core_impl_patterns_ioc_IocContainerExtension
     */
    private $_sut;

    /**
     * @var ehough_iconic_impl_ContainerBuilder
     */
    private $_mockParentContainer;

    public function onSetup()
    {
        $this->_sut = new tubepress_plugins_youtube_impl_patterns_ioc_YouTubeIocContainerExtension();

        $this->_mockParentContainer = new ehough_iconic_impl_ContainerBuilder();
    }

    public function testGetAlias()
    {
        $this->assertEquals('youtube', $this->_sut->getAlias());
    }

    public function testLoad()
    {
        $this->_sut->load($this->_mockParentContainer);

        foreach ($this->_getExpectedServices() as $service) {

            $definition = $this->_mockParentContainer->getDefinition($service->id);

            $this->assertNotNull($definition);

            $this->assertTrue($definition->getClass() === $service->type);

            if (isset($service->tag)) {

                $this->assertTrue($definition->hasTag($service->tag));
            }
        }
    }

    private function _getExpectedServices()
    {
        $map = array(

            array('tubepress_plugins_youtube_impl_provider_YouTubeUrlBuilder', 'tubepress_plugins_youtube_impl_provider_YouTubeUrlBuilder'),
            array('tubepress_plugins_youtube_impl_embedded_YouTubePluggableEmbeddedPlayerService', 'tubepress_plugins_youtube_impl_embedded_YouTubePluggableEmbeddedPlayerService',
                tubepress_spi_embedded_PluggableEmbeddedPlayerService::_),
            array('tubepress_plugins_youtube_impl_provider_YouTubePluggableVideoProviderService', 'tubepress_plugins_youtube_impl_provider_YouTubePluggableVideoProviderService',
                tubepress_spi_provider_PluggableVideoProviderService::_),
            array('tubepress_plugins_youtube_impl_options_ui_YouTubeOptionsPageParticipant', 'tubepress_plugins_youtube_impl_options_ui_YouTubeOptionsPageParticipant',
                tubepress_spi_options_ui_PluggableOptionsPageParticipant::_),
        );

        $toReturn = array();

        foreach ($map as $s) {

            $service = new stdClass();
            $service->id = $s[0];
            $service->type = $s[1];

            if (isset($s[2])) {

                $service->tag = $s[2];
            }

            $toReturn[] = $service;
        }

        return $toReturn;
    }
}
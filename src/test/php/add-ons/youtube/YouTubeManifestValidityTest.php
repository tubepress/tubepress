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
class_exists('tubepress_test_impl_addon_AbstractManifestValidityTest') ||
    require dirname(__FILE__) . '/../../classes/tubepress/test/impl/addon/AbstractManifestValidityTest.php';

class tubepress_test_addons_core_YouTubeManifestValidityTest extends tubepress_test_impl_addon_AbstractManifestValidityTest
{
    public function testManifest()
    {
        /**
         * @var $addon tubepress_spi_addon_Addon
         */
        $addon = $this->getAddonFromManifest(dirname(__FILE__) . '/../../../../main/php/add-ons/youtube/youtube.json');

        $this->assertEquals('tubepress-youtube-addon', $addon->getName());
        $this->assertEquals('1.0.0', $addon->getVersion());
        $this->assertEquals('YouTube', $addon->getTitle());
        $this->assertEquals(array('name' => 'TubePress LLC', 'url' => 'http://tubepress.com'), $addon->getAuthor());
        $this->assertEquals(array(array('type' => 'MPL-2.0', 'url' => 'http://www.mozilla.org/MPL/2.0/')), $addon->getLicenses());
        $this->assertEquals('Allows TubePress work with YouTube', $addon->getDescription());
        $this->assertEquals(array('tubepress_addons_youtube' => TUBEPRESS_ROOT . '/src/main/php/add-ons/youtube/classes'), $addon->getPsr0ClassPathRoots());
        $this->assertEquals(array('tubepress_addons_youtube_impl_ioc_YouTubeIocContainerExtension'), $addon->getIocContainerExtensions());
        $this->validateClassMap($this->_getExpectedClassMap(), $addon->getClassMap());
    }

    private function _getExpectedClassMap()
    {
        return array(
            'tubepress_addons_youtube_api_const_YouTubeEventNames' => 'classes/tubepress/addons/youtube/api/const/YouTubeEventNames.php',
            'tubepress_addons_youtube_api_const_options_names_Embedded' => 'classes/tubepress/addons/youtube/api/const/options/names/Embedded.php',
            'tubepress_addons_youtube_api_const_options_names_Feed' => 'classes/tubepress/addons/youtube/api/const/options/names/Feed.php',
            'tubepress_addons_youtube_api_const_options_names_GallerySource' => 'classes/tubepress/addons/youtube/api/const/options/names/GallerySource.php',
            'tubepress_addons_youtube_api_const_options_names_Meta' => 'classes/tubepress/addons/youtube/api/const/options/names/Meta.php',
            'tubepress_addons_youtube_api_const_options_values_GallerySourceValue' => 'classes/tubepress/addons/youtube/api/const/options/values/GallerySourceValue.php',
            'tubepress_addons_youtube_api_const_options_values_YouTube' => 'classes/tubepress/addons/youtube/api/const/options/values/YouTube.php',
            'tubepress_addons_youtube_impl_embedded_YouTubePluggableEmbeddedPlayerService' => 'classes/tubepress/addons/youtube/impl/embedded/YouTubePluggableEmbeddedPlayerService.php',
            'tubepress_addons_youtube_impl_listeners_http_YouTubeHttpErrorResponseListener' => 'classes/tubepress/addons/youtube/impl/listeners/http/YouTubeHttpErrorResponseListener.php',
            'tubepress_addons_youtube_impl_listeners_options_YouTubePlaylistHandler' => 'classes/tubepress/addons/youtube/impl/listeners/options/YouTubePlaylistHandler.php',
            'tubepress_addons_youtube_impl_listeners_video_YouTubeVideoConstructionListener' => 'classes/tubepress/addons/youtube/impl/listeners/video/YouTubeVideoConstructionListener.php',
            'tubepress_addons_youtube_impl_options_YouTubeOptionsProvider' => 'classes/tubepress/addons/youtube/impl/options/YouTubeOptionsProvider.php',
            'tubepress_addons_youtube_impl_ioc_YouTubeIocContainerExtension' => 'classes/tubepress/addons/youtube/impl/ioc/YouTubeIocContainerExtension.php',
            'tubepress_addons_youtube_impl_provider_YouTubePluggableVideoProviderService' => 'classes/tubepress/addons/youtube/impl/provider/YouTubePluggableVideoProviderService.php',
            'tubepress_addons_youtube_impl_provider_YouTubeUrlBuilder' => 'classes/tubepress/addons/youtube/impl/provider/YouTubeUrlBuilder.php'
        );
    }
}
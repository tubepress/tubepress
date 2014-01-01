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

class tubepress_test_addons_core_VimeoManifestValidityTest extends tubepress_test_impl_addon_AbstractManifestValidityTest
{
    public function testManifest()
    {
        /**
         * @var $addon tubepress_spi_addon_Addon
         */
        $addon = $this->getAddonFromManifest(dirname(__FILE__) . '/../../../../main/php/add-ons/vimeo/vimeo.json');

        $this->assertEquals('tubepress-vimeo-addon', $addon->getName());
        $this->assertEquals('1.0.0', $addon->getVersion());
        $this->assertEquals('Vimeo', $addon->getTitle());
        $this->assertEquals(array('name' => 'TubePress LLC', 'url' => 'http://tubepress.com'), $addon->getAuthor());
        $this->assertEquals(array(array('type' => 'MPL-2.0', 'url' => 'http://www.mozilla.org/MPL/2.0/')), $addon->getLicenses());
        $this->assertEquals('Allows TubePress work with Vimeo', $addon->getDescription());
        $this->assertEquals(array('tubepress_addons_vimeo' => TUBEPRESS_ROOT . '/src/main/php/add-ons/vimeo/classes'), $addon->getPsr0ClassPathRoots());
        $this->assertEquals(array('tubepress_addons_vimeo_impl_ioc_VimeoIocContainerExtension'), $addon->getIocContainerExtensions());
        $this->validateClassMap($this->_getExpectedClassMap(), $addon->getClassMap());
    }

    private function _getExpectedClassMap()
    {
        return array(
            'tubepress_addons_vimeo_api_const_options_names_Embedded'                     => 'classes/tubepress/addons/vimeo/api/const/options/names/Embedded.php',
            'tubepress_addons_vimeo_api_const_options_names_Feed'                         => 'classes/tubepress/addons/vimeo/api/const/options/names/Feed.php',
            'tubepress_addons_vimeo_api_const_options_names_GallerySource'                => 'classes/tubepress/addons/vimeo/api/const/options/names/GallerySource.php',
            'tubepress_addons_vimeo_api_const_options_names_Meta'                         => 'classes/tubepress/addons/vimeo/api/const/options/names/Meta.php',
            'tubepress_addons_vimeo_api_const_options_values_GallerySourceValue'          => 'classes/tubepress/addons/vimeo/api/const/options/values/GallerySourceValue.php',
            'tubepress_addons_vimeo_api_const_VimeoEventNames'                            => 'classes/tubepress/addons/vimeo/api/const/VimeoEventNames.php',
            'tubepress_addons_vimeo_impl_embedded_VimeoPluggableEmbeddedPlayerService'    => 'classes/tubepress/addons/vimeo/impl/embedded/VimeoPluggableEmbeddedPlayerService.php',
            'tubepress_addons_vimeo_impl_ioc_VimeoIocContainerExtension'                  => 'classes/tubepress/addons/vimeo/impl/ioc/VimeoIocContainerExtension.php',
            'tubepress_addons_vimeo_impl_listeners_http_VimeoHttpErrorResponseListener'   => 'classes/tubepress/addons/vimeo/impl/listeners/http/VimeoHttpErrorResponseListener.php',
            'tubepress_addons_vimeo_impl_listeners_http_VimeoOauthRequestListener'        => 'classes/tubepress/addons/vimeo/impl/listeners/http/VimeoOauthRequestListener.php',
            'tubepress_addons_vimeo_impl_listeners_video_VimeoVideoConstructionListener'  => 'classes/tubepress/addons/vimeo/impl/listeners/video/VimeoVideoConstructionListener.php',
            'tubepress_addons_vimeo_impl_options_VimeoOptionsProvider'                    => 'classes/tubepress/addons/vimeo/impl/options/VimeoOptionsProvider.php',
            'tubepress_addons_vimeo_impl_provider_VimeoPluggableVideoProviderService'     => 'classes/tubepress/addons/vimeo/impl/provider/VimeoPluggableVideoProviderService.php',
            'tubepress_addons_vimeo_impl_provider_VimeoUrlBuilder'                        => 'classes/tubepress/addons/vimeo/impl/provider/VimeoUrlBuilder.php'
        );
    }
}
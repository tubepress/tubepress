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
class_exists('tubepress_test_impl_addon_AbstractManifestValidityTest') ||
    require dirname(__FILE__) . '/../../classes/tubepress/test/impl/addon/AbstractManifestValidityTest.php';

class tubepress_addons_core_JwPlayerManifestValidityTest extends tubepress_test_impl_addon_AbstractManifestValidityTest
{
    public function testManifest()
    {
        /**
         * @var $addon tubepress_spi_addon_Addon
         */
        $addon = $this->getAddonFromManifest(dirname(__FILE__) . '/../../../../main/php/add-ons/jwplayer/jwplayer.json');

        $this->assertEquals('tubepress-jwplayer-addon', $addon->getName());
        $this->assertEquals('1.0.0', $addon->getVersion());
        $this->assertEquals('JW Player', $addon->getTitle());
        $this->assertEquals(array('name' => 'TubePress LLC', 'url' => 'http://tubepress.com'), $addon->getAuthor());
        $this->assertEquals(array(array('type' => 'MPL-2.0', 'url' => 'http://www.mozilla.org/MPL/2.0/')), $addon->getLicenses());
        $this->assertEquals('Allows TubePress to play YouTube videos with JW Player', $addon->getDescription());
        $this->assertEquals(array('tubepress_addons_jwplayer' => TUBEPRESS_ROOT . '/src/main/php/add-ons/jwplayer/classes'), $addon->getPsr0ClassPathRoots());
        $this->assertEquals(array('tubepress_addons_jwplayer_impl_ioc_JwPlayerIocContainerExtension'), $addon->getIocContainerExtensions());
        $this->validateClassMap($this->_getExpectedClassMap(), $addon->getClassMap());
    }

    private function _getExpectedClassMap()
    {
        return array(
            'tubepress_addons_jwplayer_api_const_options_names_Embedded' => 'classes/tubepress/addons/jwplayer/api/const/options/names/Embedded.php',
            'tubepress_addons_jwplayer_api_const_template_Variable' => 'classes/tubepress/addons/jwplayer/api/const/template/Variable.php',
            'tubepress_addons_jwplayer_impl_embedded_JwPlayerPluggableEmbeddedPlayerService' => 'classes/tubepress/addons/jwplayer/impl/embedded/JwPlayerPluggableEmbeddedPlayerService.php',
            'tubepress_addons_jwplayer_impl_listeners_template_JwPlayerTemplateVars' => 'classes/tubepress/addons/jwplayer/impl/listeners/template/JwPlayerTemplateVars.php',
            'tubepress_addons_jwplayer_impl_options_JwPlayerOptionsProvider' => 'classes/tubepress/addons/jwplayer/impl/options/JwPlayerOptionsProvider.php',
            'tubepress_addons_jwplayer_impl_options_ui_JwPlayerOptionsPageParticipant' => 'classes/tubepress/addons/jwplayer/impl/options/ui/JwPlayerOptionsPageParticipant.php',
            'tubepress_addons_jwplayer_impl_ioc_JwPlayerIocContainerExtension' => 'classes/tubepress/addons/jwplayer/impl/ioc/JwPlayerIocContainerExtension.php'
        );
    }
}
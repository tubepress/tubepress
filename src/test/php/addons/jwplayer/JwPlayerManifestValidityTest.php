<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class_exists('tubepress_impl_addon_AbstractManifestValidityTest') ||
    require dirname(__FILE__) . '/../../classes/tubepress/impl/addon/AbstractManifestValidityTest.php';

class tubepress_addons_core_JwPlayerManifestValidityTest extends tubepress_impl_addon_AbstractManifestValidityTest
{
    public function testManifest()
    {
        /**
         * @var $addon tubepress_spi_addon_Addon
         */
        $addon = $this->getAddonFromManifest(dirname(__FILE__) . '/../../../../main/php/addons/jwplayer/jwplayer.json');

        $this->assertEquals('tubepress-jwplayer-addon', $addon->getName());
        $this->assertEquals('1.0.0', $addon->getVersion());
        $this->assertEquals('JW Player', $addon->getTitle());
        $this->assertEquals(array('name' => 'TubePress LLC', 'url' => 'http://tubepress.org'), $addon->getAuthor());
        $this->assertEquals(array(array('type' => 'MPL-2.0', 'url' => 'http://www.mozilla.org/MPL/2.0/')), $addon->getLicenses());
        $this->assertEquals('Allows TubePress to play YouTube videos with JW Player', $addon->getDescription());
        $this->assertEquals(TUBEPRESS_ROOT . '/src/main/php/addons/jwplayer/scripts/bootstrap.php', $addon->getBootstrap());
        $this->assertEquals(array('tubepress_addons_jwplayer' => TUBEPRESS_ROOT . '/src/main/php/addons/jwplayer/classes'), $addon->getPsr0ClassPathRoots());
        $this->assertEquals(array('tubepress_addons_jwplayer_impl_patterns_ioc_JwPlayerIocContainerExtension'), $addon->getIocContainerExtensions());
    }
}
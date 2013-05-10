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
class_exists('tubepress_test_impl_addon_AbstractManifestValidityTest') ||
    require dirname(__FILE__) . '/../../classes/tubepress/test/impl/addon/AbstractManifestValidityTest.php';

class tubepress_addons_core_EmbedPlusManifestValidityTest extends tubepress_test_impl_addon_AbstractManifestValidityTest
{
    public function testManifest()
    {
        /**
         * @var $addon tubepress_spi_addon_Addon
         */
        $addon = $this->getAddonFromManifest(dirname(__FILE__) . '/../../../../main/php/addons/embedplus/embedplus.json');

        $this->assertEquals('tubepress-embedplus-addon', $addon->getName());
        $this->assertEquals('1.0.0', $addon->getVersion());
        $this->assertEquals('EmbedPlus', $addon->getTitle());
        $this->assertEquals(array('name' => 'TubePress LLC', 'url' => 'http://tubepress.org'), $addon->getAuthor());
        $this->assertEquals(array(array('type' => 'MPL-2.0', 'url' => 'http://www.mozilla.org/MPL/2.0/')), $addon->getLicenses());
        $this->assertEquals('Allows TubePress to play YouTube videos with EmbedPlus', $addon->getDescription());
        $this->assertEmpty($addon->getBootstrapClasses());
        $this->assertEmpty($addon->getBootstrapServices());
        $this->assertEmpty($addon->getBootstrapFiles());
        $this->assertEquals(array('tubepress_addons_embedplus' => TUBEPRESS_ROOT . '/src/main/php/addons/embedplus/classes'), $addon->getPsr0ClassPathRoots());
        $this->assertEquals(array('tubepress_addons_embedplus_impl_ioc_EmbedPlusIocContainerExtension'), $addon->getIocContainerExtensions());
        $this->validateClassMap($this->_getExpectedClassMap(), $addon->getClassMap());
    }
    
    private function _getExpectedClassMap()
    {
        return array(
            'tubepress_addons_embedplus_impl_embedded_EmbedPlusPluggableEmbeddedPlayerService' => 'classes/tubepress/addons/embedplus/impl/embedded/EmbedPlusPluggableEmbeddedPlayerService.php',
            'tubepress_addons_embedplus_impl_ioc_EmbedPlusIocContainerExtension' => 'classes/tubepress/addons/embedplus/impl/ioc/EmbedPlusIocContainerExtension.php'
        );
    }
}
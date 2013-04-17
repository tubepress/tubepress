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

class tubepress_addons_core_WordPressManifestValidityTest extends tubepress_impl_addon_AbstractManifestValidityTest
{
    public function testManifest()
    {
        /**
         * @var $addon tubepress_spi_addon_Addon
         */
        $addon = $this->getAddonFromManifest(dirname(__FILE__) . '/../../../../main/php/addons/wordpress/wordpress.json');

        $this->assertEquals('tubepress-wordpress-addon', $addon->getName());
        $this->assertEquals('1.0.0', $addon->getVersion());
        $this->assertEquals('WordPress', $addon->getTitle());
        $this->assertEquals(array('name' => 'TubePress LLC', 'url' => 'http://tubepress.org'), $addon->getAuthor());
        $this->assertEquals(array(array('type' => 'MPL-2.0', 'url' => 'http://www.mozilla.org/MPL/2.0/')), $addon->getLicenses());
        $this->assertEquals('Allows TubePress to integrate with WordPress', $addon->getDescription());
        $this->assertEquals(TUBEPRESS_ROOT . '/src/main/php/addons/wordpress/scripts/bootstrap.php', $addon->getBootstrap());
        $this->assertEquals(array('tubepress_addons_wordpress' => TUBEPRESS_ROOT . '/src/main/php/addons/wordpress/classes'), $addon->getPsr0ClassPathRoots());
        $this->assertEquals(array('tubepress_addons_wordpress_impl_patterns_ioc_WordPressIocContainerExtension'), $addon->getIocContainerExtensions());
    }
}
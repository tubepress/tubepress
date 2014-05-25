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

class tubepress_test_core_EmbedPlusManifestValidityTest extends tubepress_test_impl_addon_AbstractManifestValidityTest
{
    public function testManifest()
    {
        /**
         * @var $addon tubepress_api_addon_AddonInterface
         */
        $addon = $this->getAddonFromManifest($this->getPathToManifest());

        $this->assertEquals('tubepress/embedplus', $addon->getName());
        $this->assertEquals('1.0.0', $addon->getVersion());
        $this->assertEquals('EmbedPlus', $addon->getTitle());
        $this->assertEquals(array('name' => 'TubePress LLC', 'url' => 'http://tubepress.com'), $addon->getAuthor());
        $this->assertEquals(array(array('type' => 'MPL-2.0', 'url' => 'http://www.mozilla.org/MPL/2.0/')), $addon->getLicenses());
        $this->assertEquals('Allows TubePress to play YouTube videos with EmbedPlus', $addon->getDescription());
        $this->assertEquals(array('tubepress_addons_embedplus' => TUBEPRESS_ROOT . '/src/main/php/add-ons/embedplus/classes'), $addon->getPsr0ClassPathRoots());
        $this->assertEquals(array('tubepress_embedplus_impl_ioc_EmbedPlusExtension'), $addon->getIocContainerExtensions());
    }

    protected function getPathToManifest()
    {
        return realpath(dirname(__FILE__) . '/../../../../main/php/add-ons/embedplus/manifest.json');
    }
}
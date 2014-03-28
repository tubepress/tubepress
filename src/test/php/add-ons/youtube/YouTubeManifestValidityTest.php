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
         * @var $addon tubepress_spi_addon_AddonInterface
         */
        $addon = $this->getAddonFromManifest($this->getPathToManifest());

        $this->assertEquals('tubepress-youtube-addon', $addon->getName());
        $this->assertEquals('1.0.0', $addon->getVersion());
        $this->assertEquals('YouTube', $addon->getTitle());
        $this->assertEquals(array('name' => 'TubePress LLC', 'url' => 'http://tubepress.com'), $addon->getAuthor());
        $this->assertEquals(array(array('type' => 'MPL-2.0', 'url' => 'http://www.mozilla.org/MPL/2.0/')), $addon->getLicenses());
        $this->assertEquals('Allows TubePress work with YouTube', $addon->getDescription());
        $this->assertEquals(array('tubepress_addons_youtube' => TUBEPRESS_ROOT . '/src/main/php/add-ons/youtube/classes'), $addon->getPsr0ClassPathRoots());
        $this->assertEquals(array('tubepress_addons_youtube_impl_ioc_YouTubeIocContainerExtension'), $addon->getIocContainerExtensions());
    }

    protected function getPathToManifest()
    {
        return realpath(dirname(__FILE__) . '/../../../../main/php/add-ons/youtube/manifest.json');
    }
}
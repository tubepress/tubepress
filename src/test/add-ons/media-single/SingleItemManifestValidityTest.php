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

class tubepress_test_core_media_search_SingleItemManifestValidityTest extends tubepress_test_core_ioc_AbstractManifestValidityTest
{
    public function testManifest()
    {
        /**
         * @var $addon tubepress_api_addon_AddonInterface
         */
        $addon = $this->getAddonFromManifest($this->getPathToManifest());

        $this->assertEquals('tubepress/core-media-single', $addon->getName());
        $this->assertEquals('1.0.0', $addon->getVersion());
        $this->assertEquals('TubePress Single Media Item Functionality', $addon->getTitle());
        $this->assertEquals(array('name' => 'TubePress LLC', 'url' => 'http://tubepress.com'), $addon->getAuthor());
        $this->assertEquals(array(array('type' => 'MPL-2.0', 'url' => 'http://www.mozilla.org/MPL/2.0/')), $addon->getLicenses());
        $this->assertEquals('TubePress single media item functionality.', $addon->getDescription());
    }

    protected function getPathToManifest()
    {
        return TUBEPRESS_ROOT . '/src/main/add-ons/media-single/manifest.json';
    }
}
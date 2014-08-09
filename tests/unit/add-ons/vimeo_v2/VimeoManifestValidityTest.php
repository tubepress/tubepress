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

class tubepress_test_app_VimeoManifestValidityTest extends tubepress_test_platform_impl_boot_helper_uncached_contrib_AbstractManifestTest
{
    public function testManifest()
    {
        /**
         * @var $addon tubepress_platform_api_addon_AddonInterface
         */
        $addon = $this->getAddonFromManifest($this->getPathToManifest());

        $this->assertEquals('tubepress/vimeo', $addon->getName());
        $this->assertEquals('1.0.0', $addon->getVersion());
        $this->assertEquals('Vimeo', $addon->getTitle());
        $this->assertAuthors($addon, array(array('name' => 'TubePress LLC', 'url' => 'http://tubepress.com')));
        $this->assertLicenses($addon, array(array('type' => 'MPL-2.0', 'url' => 'http://www.mozilla.org/MPL/2.0/')));
        $this->assertEquals('Allows TubePress work with Vimeo', $addon->getDescription());
    }

    protected function getPathToManifest()
    {
        return TUBEPRESS_ROOT . '/src/add-ons/vimeo_v2/manifest.json';
    }

}
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
class tubepress_addons_core_CoreManifestValidityTest extends TubePressUnitTest
{
    public function testManifest()
    {
        $parsed = $this->getManifestDataAsAssociativeArray(dirname(__FILE__) . '/../../../../main/php/addons/core/core.json');

        $this->assertEquals('tubepress-core-addon', $parsed['name']);
        $this->assertEquals('1.0.0', $parsed['version']);
        $this->assertEquals('TubePress Core', $parsed['title']);
        $this->assertEquals(array('name' => 'TubePress LLC', 'url' => 'http://tubepress.org'), $parsed['author']);
        $this->assertEquals(array(array('type' => 'MPL-2.0', 'url' => 'http://www.mozilla.org/MPL/2.0/')), $parsed['licenses']);
        $this->assertEquals('TubePress core functionality', $parsed['description']);
        $this->assertEquals('scripts/bootstrap.php', $parsed['bootstrap']);
        $this->assertEquals(array('tubepress_addons_core' => 'classes'), $parsed['psr-0']);
        $this->assertEquals(array('tubepress_addons_core_impl_patterns_ioc_IocContainerExtension'), $parsed['ioc-container-extensions']);
        $this->assertEquals(array('tubepress_addons_core_impl_patterns_ioc_CoreIocContainerCompilerPass'), $parsed['ioc-compiler-passes']);
    }

    protected function getManifestDataAsAssociativeArray($pathToManifest)
    {
        $manifestContents = file_get_contents($pathToManifest);

        $parsedManifest = json_decode($manifestContents, true);

        $this->assertTrue(tubepress_impl_util_LangUtils::isAssociativeArray($parsedManifest));

        return $parsedManifest;
    }
}
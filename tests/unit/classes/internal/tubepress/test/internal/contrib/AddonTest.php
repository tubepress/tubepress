<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_internal_contrib_Addon<extended>
 */
class tubepress_test_internal_contrib_AddonTest extends tubepress_test_internal_contrib_AbstractContributableTest
{
    public function testClassMap()
    {
        $classMap = array('a' => 'c');

        /**
         * @var $addon tubepress_internal_contrib_Addon
         */
        $addon = $this->buildContributable();

        $addon->setClassMap($classMap);

        $this->assertSame($classMap, $addon->getClassMap());
    }

    public function testCompilerPasses()
    {
        $passes = array(

            'a' => 30,
            'b' => 90,
            'c' => 20
        );

        /**
         * @var $addon tubepress_internal_contrib_Addon
         */
        $addon = $this->buildContributable();

        $addon->setCompilerPasses($passes);

        $this->assertSame($passes, $addon->getMapOfCompilerPassClassNamesToPriorities());
    }

    public function testExtensions()
    {
        $extensions = array('a', 'b', 'c');

        /**
         * @var $addon tubepress_internal_contrib_Addon
         */
        $addon = $this->buildContributable();

        $addon->setExtensions($extensions);

        $this->assertSame($extensions, $addon->getExtensionClassNames());
    }


    /**
     * @return tubepress_internal_contrib_AbstractContributable
     */
    protected function buildSut($name, $version, $title, array $authors, array $licenses)
    {
        return new tubepress_internal_contrib_Addon($name, $version, $title, $authors, $licenses);
    }
}
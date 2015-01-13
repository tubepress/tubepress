<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_platform_impl_boot_helper_uncached_contrib_AddonFactory<extended>
 */
class tubepress_test_platform_impl_boot_helper_uncached_contrib_AddonFactoryTest extends tubepress_test_platform_impl_boot_helper_uncached_contrib_AbstractFactoryTest
{
    private $_mockBootSettingsInterface;

    private $_addonBlackList;

    /**
     * @return tubepress_platform_impl_boot_helper_uncached_contrib_AddonFactory
     */
    protected function buildSut(tubepress_platform_api_log_LoggerInterface $logger,
                                tubepress_platform_api_url_UrlFactoryInterface $urlFactory,
                                tubepress_platform_api_util_LangUtilsInterface $langUtils,
                                tubepress_platform_api_util_StringUtilsInterface $stringUtils)
    {
        $this->_mockBootSettingsInterface = $this->mock(tubepress_platform_api_boot_BootSettingsInterface::_);
        $this->_mockBootSettingsInterface->shouldReceive('getAddonBlacklistArray')->once()->andReturnUsing(array($this, 'getAddonBlackList'));
        $this->_addonBlackList = array();

        return new tubepress_platform_impl_boot_helper_uncached_contrib_AddonFactory(

            $logger, $urlFactory, $langUtils, $stringUtils, $this->_mockBootSettingsInterface
        );
    }

    public function testExtensions()
    {
        /**
         * @var $addon tubepress_platform_api_addon_AddonInterface
         */
        $addon = $this->fromManifest(array(
            'container-builder' => array(
                'extensions' => array(
                    'foo', 'bar'
                )
            )
        ));

        $this->assertEquals(array('foo', 'bar'), $addon->getExtensionClassNames());
    }

    public function testCompilerPasses()
    {
        /**
         * @var $addon tubepress_platform_api_addon_AddonInterface
         */
        $addon = $this->fromManifest(array(
            'container-builder' => array(
                'compiler-passes' => array(
                    'foo' => 1
                )
            )
        ));

        $this->assertEquals(array('foo' => 1), $addon->getMapOfCompilerPassClassNamesToPriorities());
    }

    public function testClassmap()
    {
        /**
         * @var $addon tubepress_platform_api_addon_AddonInterface
         */
        $addon = $this->fromManifest(array(
            'autoload' => array(
                'classmap' => array(
                    'foo' => 'bar'
                )
            )
        ));

        $this->assertEquals(array('foo' => sys_get_temp_dir() . '/bar'), $addon->getClassMap());
    }

    public function testValidConstruction()
    {
        /**
         * @var $addon tubepress_platform_api_addon_AddonInterface
         */
        $addon = $this->fromManifest();

        $this->assertEquals(array(), $addon->getMapOfCompilerPassClassNamesToPriorities());
        $this->assertEquals(array(), $addon->getExtensionClassNames());
        $this->assertEquals(array(), $addon->getClassMap());
    }

    public function getAddonBlackList()
    {
        return $this->_addonBlackList;
    }

    public function testBlacklisted()
    {
        $this->_addonBlackList = array('some-name');

        $this->confirmFailures(array(), array('some-name is blacklisted'));
    }

    /**
     * @dataProvider getBadCompilerPasses
     */
    public function testBadCompilerPasses($candidate, $message)
    {
        $data = array(
            'container-builder' => array(
                'compiler-passes' => $candidate
            )
        );

        $this->confirmFailures($data, array($message));
    }

    public function getBadCompilerPasses()
    {
        return array(

            array(array(),                        'Compiler passes is a non-associative array'),
            array(array('foo' => new stdClass()), 'Compiler passes contains invalid data'),
            array(array(3 => 'foo'),              'Compiler passes is a non-associative array'),
            array(array('bar' => 'foo'),          'Compiler passes contains invalid data'),
        );
    }

    /**
     * @dataProvider getBadExtensionsData
     */
    public function testGetBadExtensions($candidate, $message)
    {
        $data = array(
            'container-builder' => array(
                'extensions' => $candidate
            )
        );

        $this->confirmFailures($data, array($message));
    }

    public function getBadExtensionsData()
    {
        return array(

            array(array(),                        'Invalid extensions'),
            array(array('foo' => new stdClass()), 'Invalid extensions'),
            array(array('bar' => 'foo'),          'Invalid extensions'),
        );
    }

    /**
     * @dataProvider getBadClassMaps
     */
    public function testBadClassMaps($candidate, $message)
    {
        $data = array(
            'autoload' => array(
                'classmap' => $candidate
            )
        );

        $this->confirmFailures($data, array($message));
    }

    public function getBadClassMaps()
    {
        return array(

            array(array(),                        'Classmap is non-associative array'),
            array(array('foo' => new stdClass()), 'Classmap contains non-string data'),
            array(array(3 => 'foo'),              'Classmap is non-associative array'),
        );
    }
}
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
class tubepress_impl_patterns_ioc_CoreIocContainerTest extends TubePressUnitTest
{
    /**
     * @var tubepress_impl_patterns_ioc_CoreIocContainer
     */
    private $_sut;

    function onSetup()
    {
        $this->_sut = new tubepress_impl_patterns_ioc_CoreIocContainer();
    }

    public static function setUpBeforeClass()
    {
        require_once TUBEPRESS_ROOT . '/src/test/resources/plugins/FakeExtension.php';
        require_once TUBEPRESS_ROOT . '/src/test/resources/plugins/FakeCompilerPass.php';
    }

    function testBuildsNormally()
    {
        $this->assertNotNull($this->_sut);
    }

    function testServiceConstructions()
    {
        $toTest = array(

            tubepress_spi_environment_EnvironmentDetector::_ => tubepress_spi_environment_EnvironmentDetector::_,
            'ehough_fimble_api_FinderFactory'                => 'ehough_fimble_api_FinderFactory',
            tubepress_spi_plugin_PluginDiscoverer::_         => tubepress_spi_plugin_PluginDiscoverer::_,
            tubepress_spi_plugin_PluginRegistry::_           => tubepress_spi_plugin_PluginRegistry::_,
        );

        foreach ($toTest as $key => $value) {

            $this->_testServiceBuilt($key, $value);
        }
    }

    public function testTaggedServices()
    {
        $result = $this->_sut->findTaggedServiceIds('some tag');
        $this->assertEquals(array(), $result);
    }

    public function testCompile()
    {
        $this->_sut->compile();

        $this->assertTrue(true);
    }

    public function testRegisterExtension()
    {
        $this->_sut->registerExtension(new FakeExtension());

        $this->assertTrue(true);
    }

    public function testAddCompilerPass()
    {
        $this->_sut->addCompilerPass(new FakeCompilerPass());

        $this->assertTrue(true);
    }

    public function testHas()
    {
        $this->assertTrue($this->_sut->has('ehough_fimble_api_FinderFactory'));
        $this->assertFalse($this->_sut->has('x y z'));
    }

    public function testParams()
    {
        $this->assertFalse($this->_sut->hasParameter('some param'));
        $this->_sut->setParameter('some param', 'some value');
        $this->assertTrue($this->_sut->hasParameter('some param'));
        $this->assertEquals('some value', $this->_sut->getParameter('some param'));
    }

    private function _testServiceBuilt($id, $class)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $obj = $this->_sut->get($id);

        $this->assertTrue($obj instanceof $class, "Failed to build $id of type $class. Instead got " . gettype($obj) . var_export($obj, true));
    }


}
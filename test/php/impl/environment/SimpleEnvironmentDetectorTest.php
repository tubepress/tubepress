<?php

class org_tubepress_impl_environment_SimpleEnvironmentDetectorTest extends TubePressUnitTest {

    private $_sut;

    function setUp()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_environment_SimpleEnvironmentDetector();
    }

    function testIsPro()
    {
        $this->assertFalse($this->_sut->isPro());
    }

    function testIsWordPress()
    {
        $this->assertFalse($this->_sut->isWordPress());
    }

    public function testGetUserContentDirNonWordPress()
    {
        $dir = realpath(dirname(__FILE__) . '/../../../../');
        $this->assertEquals("$dir/tubepress-content", $this->_sut->getUserContentDirectory());
    }

    function testGetBaseInstallationPath()
    {
        $result = $this->_sut->getTubePressBaseInstallationPath();
        $dirname = basename($result);
        $this->assertEquals('tubepress', $dirname);
    }

    function testGetBaseInstallationBasename()
    {
        $result = $this->_sut->getTubePressInstallationDirectoryBaseName();
        $this->assertEquals('tubepress', $result);
    }

}

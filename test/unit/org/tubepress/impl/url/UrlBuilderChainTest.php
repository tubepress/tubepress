<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/url/UrlBuilderChain.class.php';
require_once dirname(__FILE__) . '/../../../../../includes/TubePressUnitTest.php';

class org_tubepress_impl_url_UrlBuilderChainTest extends TubePressUnitTest {
    
    private $_sut;
    private $_single;
    private $_arg;
    private $_result;
    
    function setUp()
    {
        parent::setUp();
        $this->_single = false;
        $this->_sut = new org_tubepress_impl_url_UrlBuilderChain();
    }

    function getMock($className)
    {
        $mock = parent::getMock($className);
        if ($className === 'org_tubepress_api_provider_ProviderCalculator') {
            $mock->expects($this->once())
                 ->method('calculateCurrentVideoProvider')
                 ->will($this->returnValue('providername'));
        }
        if ($className === 'org_tubepress_api_patterns_cor_Chain') {
            $mock->expects($this->once())
                 ->method('execute')
                 ->with($this->anything(), array(
                     'org_tubepress_impl_url_commands_YouTubeUrlBuilderCommand',
                     'org_tubepress_impl_url_commands_VimeoUrlBuilderCommand'
                  ))
                  ->will($this->returnCallback(array($this, 'fake')));
        }
        return $mock;
    }

    function testBuildMultiple()
    {
        $this->_single = false;
        $this->_arg = 'page';
        $this->_sut->buildGalleryUrl($this->_arg);
        $this->assertEquals('foo', $this->_result);
    }
    
    function testBuildSingle()
    {
        $this->_single = true;
        $this->_arg = 'singleid';
        $this->_sut->buildSingleVideoUrl($this->_arg);
        $this->assertEquals('foo', $this->_result);
    }
    function fake()
    {
        $this->_result = 'foo';
        return true;
    }
}


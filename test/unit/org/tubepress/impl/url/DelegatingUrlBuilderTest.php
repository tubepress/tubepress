<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/url/DelegatingUrlBuilder.class.php';
require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';

class org_tubepress_impl_url_DelegatingUrlBuilderTest extends TubePressUnitTest {
    
    private $_sut;
    private $_single;
    private $_arg;
    
    function setUp()
    {
        $this->initFakeIoc();
        $this->_single = false;
        $this->_sut = new org_tubepress_impl_url_DelegatingUrlBuilder();
    }

    function getMock($className)
    {
        $mock = parent::getMock($className);
        if ($className === 'org_tubepress_api_provider_ProviderCalculator') {
            $mock->expects($this->once())
                 ->method('calculateCurrentVideoProvider')
                 ->will($this->returnValue('providername'));
        }
        if ($className === 'org_tubepress_api_patterns_StrategyManager') {
            $mock->expects($this->once())
                 ->method('executeStrategy')
                 ->with(array(
                     'org_tubepress_impl_url_strategies_YouTubeUrlBuilderStrategy',
                     'org_tubepress_impl_url_strategies_VimeoUrlBuilderStrategy'
                  ), new PHPUnit_Framework_Constraint_IsEqual('providername'),
                     new PHPUnit_Framework_Constraint_IsEqual($this->_single),
                     new PHPUnit_Framework_Constraint_IsEqual($this->_arg))
                  ->will($this->returnValue('foo'));
        }
        return $mock;
    }

    function testBuildMultiple()
    {
        $this->_single = false;
        $this->_arg = 'page';
        $this->assertEquals('foo', $this->_sut->buildGalleryUrl($this->_arg));
    }
    
    function testBuildSingle()
    {
        $this->_single = true;
        $this->_arg = 'singleid';
        $this->assertEquals('foo', $this->_sut->buildSingleVideoUrl($this->_arg));
    }
}
?>

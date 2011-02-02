<?php

require_once dirname(__FILE__) . '/../../../../../../test/unit/TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/factory/DelegatingVideoFactory.class.php';

class org_tubepress_impl_factory_DelegatingVideoFactoryTest extends TubePressUnitTest {
    
    private $_sut;
    
    function setUp() {
    	$this->initFakeIoc();
        $this->_sut = new org_tubepress_impl_factory_DelegatingVideoFactory();
    }
    
        function getMock($className)
    {
        $mock = parent::getMock($className);
        if ($className === 'org_tubepress_api_patterns_StrategyManager') {
            $mock->expects($this->once())
                 ->method('executeStrategy')
                 ->with(array(
            'org_tubepress_impl_factory_strategies_YouTubeFactoryStrategy',
            'org_tubepress_impl_factory_strategies_VimeoFactoryStrategy'
        ), new PHPUnit_Framework_Constraint_IsEqual('bla'))
                  ->will($this->returnValue('foo'));
        }
        
        return $mock;
    }
    
   function testConvert()
   {
       $this->assertEquals('foo', $this->_sut->feedToVideoArray('bla'));
   }
}

?>

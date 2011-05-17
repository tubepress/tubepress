<?php

require_once dirname(__FILE__) . '/../../../../../includes/TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/factory/VideoFactoryChain.class.php';

class org_tubepress_impl_factory_VideoFactoryChainTest extends TubePressUnitTest {

    private $_sut;
    private $_result;

    function setUp()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_factory_VideoFactoryChain();
    }

    function getMock($className)
    {
        $mock = parent::getMock($className);
        
        if ($className === 'org_tubepress_api_patterns_cor_Chain') {
            
            $mock->expects($this->once())
                 ->method('execute')
                 ->with(
            $this->anything(),
            $this->equalTo(array(
                'org_tubepress_impl_factory_commands_YouTubeFactoryCommand',
                'org_tubepress_impl_factory_commands_VimeoFactoryCommand'
            )))
            ->will($this->returnCallback(array($this, 'fake')));
        }

        return $mock;
    }

    function testConvert()
    {
        $this->_sut->feedToVideoArray('bla');
        $this->assertEquals('foo', $this->_result);
    }
     
    function fake()
    {
        $this->_result = 'foo';
    }
}
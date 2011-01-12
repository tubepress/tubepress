<?php
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/patterns/StrategyManagerImpl.class.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/api/patterns/Strategy.class.php';
require_once dirname(__FILE__) . '/../../../../../../test/unit/TubePressUnitTest.php';

class org_tubepress_impl_patterns_StrategyManagerImplTest extends TubePressUnitTest {

	private $_sut;
    private $_mockStrategyOne;
    private $_mockStrategyTwo;
	
    function setUp()
    {
        $this->initFakeIoc();
        $this->_sut = new org_tubepress_impl_patterns_StrategyManagerImpl();
        $this->_mockStrategyOne = $this->getMock('org_tubepress_api_patterns_Strategy');
        $this->_mockStrategyTwo = $this->getMock('org_tubepress_api_patterns_Strategy');
    }
    
    function getMock($className)
    {
        $mock = parent::getMock($className);
        
        if ($className == 'fakestrat1') {
            $mock = $this->_mockStrategyOne;
        }
        if ($className == 'fakestrat2') {
            $mock = $this->_mockStrategyTwo;
        }
        return $mock;
    }
    
    /**
     * @expectedException Exception
     */
    function testExecuteWithNonStrategy()
    {
        $this->_sut->executeStrategy(array('org_tubepress_api_options_OptionsManager'));
    }
    
    /**
     * @expectedException Exception
     */
    function testExecuteWithNonArrayArgument()
    {
        $this->_sut->executeStrategy('bla');
    }

    function testExecSecondStrategy()
    {   
        $this->mockStartStop();
        
        $this->_mockStrategyOne->expects($this->once())
             ->method('canHandle')
             ->will($this->returnValue(false));
        $this->_mockStrategyTwo->expects($this->once())
             ->method('canHandle')
             ->will($this->returnValue(true));
        $this->_mockStrategyOne->expects($this->never())
             ->method('execute');
        $this->_mockStrategyTwo->expects($this->once())
             ->method('execute')
             ->with($this->equalTo(400))
             ->will($this->returnValue(500));
        
        $result = $this->_sut->executeStrategy(array('fakestrat1', 'fakestrat2'), 400);
        $this->assertEquals(500, $result);
    }
    
    /**
     * @expectedException Exception
     */
    function testNoStrategyCanHandle()
    {
        $this->mockStartStop();
        
        $this->_mockStrategyOne->expects($this->once())
             ->method('canHandle')
             ->will($this->returnValue(false));
        $this->_mockStrategyTwo->expects($this->once())
             ->method('canHandle')
             ->will($this->returnValue(false));
        $this->_mockStrategyOne->expects($this->never())
             ->method('execute');
        $this->_mockStrategyTwo->expects($this->never())
             ->method('execute');
        
        $result = $this->_sut->executeStrategy(array('fakestrat1', 'fakestrat2'));
    }
    
    private function mockStartStop()
    {
        $this->_mockStrategyOne->expects($this->once())->method('start');
        $this->_mockStrategyTwo->expects($this->once())->method('start');
        $this->_mockStrategyOne->expects($this->once())->method('stop');
        $this->_mockStrategyTwo->expects($this->once())->method('stop');
    }
}
?>

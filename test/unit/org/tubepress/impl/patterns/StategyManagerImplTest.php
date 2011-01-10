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
        $this->_sut = new org_tubepress_impl_patterns_StrategyManagerImpl();
        $this->_mockStrategyOne = $this->getMock('org_tubepress_api_patterns_Strategy');
        $this->_mockStrategyTwo = $this->getMock('org_tubepress_api_patterns_Strategy');
    }
    
    /**
     * @expectedException Exception
     */
    function testNoStrategysRegistered()
    {
        $this->_sut->executeStrategy('fake');
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
             ->method('execute');
        
        $this->_sut->registerStrategy('fakepoint', $this->_mockStrategyOne);
        $this->_sut->registerStrategy('fakepoint', $this->_mockStrategyTwo);
        
        $result = $this->_sut->executeStrategy('fakepoint');
    }
    
    /**
     * @expectedException Exception
     */
    function testRegisterStrategiesBadSecondArgument()
    {
        $this->_sut->registerStrategies('fake', 'fake');
    }
    
    function testRegisterStrategies()
    {
        $this->_sut->registerStrategies('fake', array($this->_mockStrategyOne, $this->_mockStrategyTwo));
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
        
        $this->_sut->registerStrategy('fakepoint', $this->_mockStrategyOne);
        $this->_sut->registerStrategy('fakepoint', $this->_mockStrategyTwo);
        
        $result = $this->_sut->executeStrategy('fakepoint');
    }
    
    /**
     * @expectedException Exception
     */
    function testRegisterBadStrategyName()
    {
        $this->_sut->registerStrategy(1, $this->_mockStrategyOne);
    }
    
    /**
     * @expectedException Exception
     */
    function testRegisterNonStrategy()
    {
        $this->_sut->registerStrategy('fake', 'nothing');
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

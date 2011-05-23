<?php
require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/impl/patterns/cor/ChainGang.class.php';
require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/api/patterns/cor/Command.class.php';

class org_tubepress_impl_patterns_cor_ChainGangTest extends TubePressUnitTest {

	private $_sut;
    private $_mockCommandOne;
    private $_mockCommandTwo;
    private $_result;
	
    function setUp()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_patterns_cor_ChainGang();
        $this->_mockCommandOne = $this->getMock('org_tubepress_api_patterns_cor_Command');
        $this->_mockCommandTwo = $this->getMock('org_tubepress_api_patterns_cor_Command');
        org_tubepress_impl_log_Log::setEnabled(false, array());
    }
    
    function getMock($className)
    {
        $mock = parent::getMock($className);
        
        if ($className == 'fakestrat1') {
            $mock = $this->_mockCommandOne;
        }
        if ($className == 'fakestrat2') {
            $mock = $this->_mockCommandTwo;
        }
        return $mock;
    }
    
    /**
     * @expectedException Exception
     */
    function testExecuteWithNonCommand()
    {
        $this->_sut->execute(array('org_tubepress_api_exec_ExecutionContext'));
    }
    
    /**
     * @expectedException Exception
     */
    function testExecuteWithNonArrayArgument()
    {
        $this->_sut->execute('bla');
    }

    function testExecSecondCommand()
    {   
        $commands = array('fakestrat1', 'fakestrat2');
        
        $this->_mockCommandOne->expects($this->once())
             ->method('execute')
             ->will($this->returnValue(false));
        $this->_mockCommandTwo->expects($this->once())
             ->method('execute')
             ->will($this->returnCallback(array($this, 'fake')));
        
        $result = $this->_sut->execute(new stdClass, $commands);
        $this->assertTrue($result);
        $this->assertEquals(500, $this->_result);
    }
    

    function testNoCommandCanHandle()
    {
        $this->_mockCommandOne->expects($this->once())
             ->method('execute')
             ->will($this->returnValue(false));
        $this->_mockCommandTwo->expects($this->once())
             ->method('execute')
             ->will($this->returnValue(false));
        
        $result = $this->_sut->execute(new stdClass, array('fakestrat1', 'fakestrat2'));
        $this->assertFalse($result);
    }
    
    function fake()
    {
        $this->_result = 500;
        return true;
    }
}


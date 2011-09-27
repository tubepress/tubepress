<?php
require_once BASE . '/sys/classes/org/tubepress/impl/patterns/cor/ChainGang.class.php';
require_once BASE . '/sys/classes/org/tubepress/api/patterns/cor/Command.class.php';

class org_tubepress_impl_patterns_cor_ChainGangTest extends TubePressUnitTest {

    private $_sut;

    function setUp()
    {
        parent::setUp();

        $this->_sut = new org_tubepress_impl_patterns_cor_ChainGang();

        org_tubepress_impl_log_Log::setEnabled(false, array());
    }

    /**
    * @expectedException Exception
    */
    function testExecuteWithNonContext()
    {
        $this->_sut->execute('hello', array('org_tubepress_api_exec_ExecutionContext'));
    }

    /**
     * @expectedException Exception
     */
    function testExecuteWithNonCommand()
    {
        $this->_sut->execute(new stdClass, array('org_tubepress_api_exec_ExecutionContext'));
    }

    function testCreateContextInstance()
    {
        $this->assertTrue(is_a($this->_sut->createContextInstance(), 'stdClass'));
    }

    /**
     * @expectedException Exception
     */
    function testExecuteWithNonArrayArgument()
    {
        $this->_sut->execute(new stdClass, 'bla');
    }

    function testCanHandle()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context = new stdClass();

        $mockCommand = $ioc->get('org_tubepress_spi_patterns_cor_Command');
        $mockCommand->shouldReceive('execute')->once()->with($context)->andReturn(true);

        $this->assertTrue($this->_sut->execute($context, array('org_tubepress_spi_patterns_cor_Command')));
    }

    function testNobodyCanHandle()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context = new stdClass();

        $mockCommand = $ioc->get('org_tubepress_spi_patterns_cor_Command');
        $mockCommand->shouldReceive('execute')->once()->with($context)->andReturn(false);

        $this->assertFalse($this->_sut->execute($context, array('org_tubepress_spi_patterns_cor_Command')));
    }
}


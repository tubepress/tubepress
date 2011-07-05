<?php
require_once dirname(__FILE__) . '/../../../../../sys/classes/org/tubepress/impl/patterns/cor/ChainGang.class.php';
require_once dirname(__FILE__) . '/../../../../../sys/classes/org/tubepress/api/patterns/cor/Command.class.php';

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

    function testCanHandle()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context = new stdClass();

        $mockCommand = $ioc->get('org_tubepress_api_patterns_cor_Command');
        $mockCommand->shouldReceive('execute')->once()->with($context)->andReturn(true);

        $this->assertTrue($this->_sut->execute($context, array('org_tubepress_api_patterns_cor_Command')));
    }

    function testNobodyCanHandle()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context = new stdClass();

        $mockCommand = $ioc->get('org_tubepress_api_patterns_cor_Command');
        $mockCommand->shouldReceive('execute')->once()->with($context)->andReturn(false);

        $this->assertFalse($this->_sut->execute($context, array('org_tubepress_api_patterns_cor_Command')));
    }
}


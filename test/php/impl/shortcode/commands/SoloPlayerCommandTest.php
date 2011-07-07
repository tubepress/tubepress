<?php

require_once dirname(__FILE__) . '/../../../../../sys/classes/org/tubepress/impl/shortcode/commands/SoloPlayerCommand.class.php';

class org_tubepress_impl_shortcode_commands_SoloPlayerCommandTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_shortcode_commands_SoloPlayerCommand();
	}

	function testExecuteWrongPlayer()
	{
	    $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();

	    $execContext = $ioc->get('org_tubepress_api_exec_ExecutionContext');
	    $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME)->andReturn(org_tubepress_api_const_options_values_PlayerValue::SHADOWBOX);

	    $this->assertFalse($this->_sut->execute(new stdClass()));
	}

	function testExecuteNoVideoId()
	{
	    $mockChainContext = new stdClass();

	    $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();

	    $execContext = $ioc->get('org_tubepress_api_exec_ExecutionContext');
	    $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME)->andReturn(org_tubepress_api_const_options_values_PlayerValue::SOLO);

	    $qss     = $ioc->get('org_tubepress_api_querystring_QueryStringService');
	    $qss->shouldReceive('getCustomVideo')->once()->andReturn('');

	    $this->assertFalse($this->_sut->execute($mockChainContext));
	}

	function testExecute()
	{
	    $mockChainContext = new stdClass();

	    $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();

	    $execContext = $ioc->get('org_tubepress_api_exec_ExecutionContext');
	    $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME)->andReturn(org_tubepress_api_const_options_values_PlayerValue::SOLO);
        $execContext->shouldReceive('set')->once()->with(org_tubepress_api_const_options_names_Output::VIDEO, 'video-id');

	    $qss     = $ioc->get('org_tubepress_api_querystring_QueryStringService');
	    $qss->shouldReceive('getCustomVideo')->once()->andReturn('video-id');

	    $chain = $ioc->get('org_tubepress_api_patterns_cor_Chain');
	    $chain->shouldReceive('execute')->once()->with($mockChainContext, array(
            'org_tubepress_impl_shortcode_commands_SingleVideoCommand'
        ));

	    $this->assertTrue($this->_sut->execute($mockChainContext));
	}
}
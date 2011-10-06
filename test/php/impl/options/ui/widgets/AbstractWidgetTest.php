<?php


abstract class org_tubepress_impl_options_ui_widgets_AbstractWidgetTest extends TubePressUnitTest {

	private $_messageService;

	public function setup()
	{
		parent::setUp();

		$ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

		$this->_messageService   = $ioc->get(org_tubepress_api_message_MessageService::_);
		$this->_messageService->shouldReceive('_')->andReturnUsing( function ($key) {
            return "<<message: $key>>";
        });

	}
}


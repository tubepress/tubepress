<?php
require_once BASE . '/sys/classes/org/tubepress/impl/options/WordPressStorageManager.class.php';

class org_tubepress_impl_options_WordPressStorageManagerTest extends TubePressUnitTest {

    private $_sut;

    function setUp()
    {
        parent::setUp();
        $get_option = new PHPUnit_Extensions_MockFunction('get_option');
        $get_option->expects($this->any())->will($this->returnCallback(array($this, 'callback')));

        $update_option = new PHPUnit_Extensions_MockFunction('update_option');

        $this->_sut = new org_tubepress_impl_options_WordPressStorageManager();
    }

    function testInit()
    {
        $this->_sut->init();
    }

    function testSet()
    {
        $ioc               = org_tubepress_impl_ioc_IocContainer::getInstance();
        $validationService = $ioc->get('org_tubepress_api_options_OptionValidator');

        $validationService->shouldReceive('validate')->with(org_tubepress_api_const_options_names_Advanced::DEBUG_ON, true)->once();

        $this->_sut->set(org_tubepress_api_const_options_names_Advanced::DEBUG_ON, true);
    }

    function callback($name)
    {
        return $name === 'version' ? 225 : "<value of $name>";
    }
}


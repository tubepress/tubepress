<?php
require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/impl/http/DefaultHttpRequestParameterService.class.php';
require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/api/plugin/PluginManager.class.php';

class org_tubepress_impl_http_DefaultHttpRequestParameterServiceTest extends TubePressUnitTest {

    private $_sut;

    function setup()
    {
        parent::setUp();

        $this->_sut = new org_tubepress_impl_http_DefaultHttpRequestParameterService();
    }

    function testParamExists()
    {
        $this->assertTrue($this->_sut->hasParam('something') === false);

        $_REQUEST['something'] = 5;
        $this->assertTrue($this->_sut->hasParam('something') === true);
    }

    function testGetParamValueNoExist()
    {
        $this->assertTrue($this->_sut->getParamValue('something') === null);
    }

    function testGetParam()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $pm  = $ioc->get(org_tubepress_api_plugin_PluginManager::_);

        $_REQUEST['something'] = array(1, 2, 3);

        $pm->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::VARIABLE_READ_FROM_EXTERNAL_INPUT, 'something', array(1, 2, 3))->andReturn('yo');

        $result = $this->_sut->getParamValue('something');

        $this->assertTrue($result === 'yo');
    }
}


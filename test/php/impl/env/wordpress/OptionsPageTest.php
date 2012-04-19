<?php

require_once BASE . '/sys/classes/org/tubepress/impl/env/wordpress/OptionsPage.class.php';

class org_tubepress_impl_env_wordpress_OptionsPageTest extends TubePressUnitTest {

    function setUp()
    {
        parent::setUp();
    }

    function testSubmitThrowsException()
    {
        $addOptionsPageFunctionMock = new PHPUnit_Extensions_MockFunction('add_options_page');

        $ioc  = org_tubepress_impl_ioc_IocContainer::getInstance();

        $hrps = $ioc->get(org_tubepress_api_http_HttpRequestParameterService::_);
        $hrps->shouldReceive('hasParam')->once()->with('tubepress_save')->andReturn(true);

        $wpsm = $ioc->get(org_tubepress_api_options_StorageManager::_);
        $wpsm->shouldReceive('init')->once();

        $formHandler = $ioc->get(org_tubepress_api_options_ui_FormHandler::_);
        $formHandler->shouldReceive('getHtml')->once()->andReturn('yo');
        $formHandler->shouldReceive('onSubmit')->once()->andThrow(new Exception('something!'));

        $this->_setupWorkingNonce();

        ob_start();
        org_tubepress_impl_env_wordpress_OptionsPage::executeOptionsPage();
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('<div id="message" class="error fade"><p><strong>something!</strong></p></div>yo', $contents);
    }

    function testSubmitValidValue()
    {
        $addOptionsPageFunctionMock = new PHPUnit_Extensions_MockFunction('add_options_page');

        $ioc  = org_tubepress_impl_ioc_IocContainer::getInstance();

        $hrps = $ioc->get(org_tubepress_api_http_HttpRequestParameterService::_);
        $hrps->shouldReceive('hasParam')->once()->with('tubepress_save')->andReturn(true);

        $wpsm = $ioc->get(org_tubepress_api_options_StorageManager::_);
        $wpsm->shouldReceive('init')->once();

        $formHandler = $ioc->get(org_tubepress_api_options_ui_FormHandler::_);
        $formHandler->shouldReceive('getHtml')->once()->andReturn('yo');
        $formHandler->shouldReceive('onSubmit')->once()->andReturn(null);

        ob_start();
        org_tubepress_impl_env_wordpress_OptionsPage::executeOptionsPage();
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('<div id="message" class="updated fade"><p><strong>Options updated</strong></p></div>yo', $contents);
    }

    function testSubmitInvalidValue()
    {
        $addOptionsPageFunctionMock = new PHPUnit_Extensions_MockFunction('add_options_page');

        $ioc  = org_tubepress_impl_ioc_IocContainer::getInstance();

        $hrps = $ioc->get(org_tubepress_api_http_HttpRequestParameterService::_);
        $hrps->shouldReceive('hasParam')->once()->with('tubepress_save')->andReturn(true);

        $wpsm = $ioc->get(org_tubepress_api_options_StorageManager::_);
        $wpsm->shouldReceive('init')->once();

        $formHandler = $ioc->get(org_tubepress_api_options_ui_FormHandler::_);
        $formHandler->shouldReceive('getHtml')->once()->andReturn('yo');
        $formHandler->shouldReceive('onSubmit')->once()->andReturn(array('bad value!', 'another bad value!'));

        ob_start();
        org_tubepress_impl_env_wordpress_OptionsPage::executeOptionsPage();
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('<div id="message" class="error fade"><p><strong>bad value!<br />another bad value!</strong></p></div>yo', $contents);
    }

    function testDisplayOptionsPage()
    {
        $addOptionsPageFunctionMock = new PHPUnit_Extensions_MockFunction('add_options_page');

        $ioc  = org_tubepress_impl_ioc_IocContainer::getInstance();

        $hrps = $ioc->get(org_tubepress_api_http_HttpRequestParameterService::_);
        $hrps->shouldReceive('hasParam')->once()->with('tubepress_save')->andReturn(false);

        $wpsm = $ioc->get(org_tubepress_api_options_StorageManager::_);
        $wpsm->shouldReceive('init')->once();

        $formHandler = $ioc->get(org_tubepress_api_options_ui_FormHandler::_);
        $formHandler->shouldReceive('getHtml')->once()->andReturn('yo');

        ob_start();
        org_tubepress_impl_env_wordpress_OptionsPage::executeOptionsPage();
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('yo', $contents);
    }

    function testMenuAction()
    {
        $add_options_page = new PHPUnit_Extensions_MockFunction('add_options_page');
        $add_options_page->expects($this->once())->with('TubePress Options', 'TubePress', 'manage_options', 'tubepress', array('org_tubepress_impl_env_wordpress_OptionsPage', 'executeOptionsPage'));

        org_tubepress_impl_env_wordpress_OptionsPage::menuAction();
    }

    function testInit()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $fs = $ioc->get(org_tubepress_api_filesystem_Explorer::_);
        $fs->shouldReceive('getTubePressInstallationDirectoryBaseName')->once()->andReturn('base_name');

	    $plugins_url = new PHPUnit_Extensions_MockFunction('plugins_url');
	    $plugins_url->expects($this->exactly(6))->will($this->_getPluginsUrlReturnMap());

        $wp_register_style = new PHPUnit_Extensions_MockFunction('wp_register_style');
        $wp_register_style->expects($this->exactly(3))->will($this->_getRegisterStyleReturnMap());

        $wp_register_script = new PHPUnit_Extensions_MockFunction('wp_register_script');
        $wp_register_script->expects($this->exactly(3))->will($this->_getRegisterScriptReturnMap());

        $wp_enqueue_style = new PHPUnit_Extensions_MockFunction('wp_enqueue_style');
        $wp_enqueue_style->expects($this->exactly(3))->will($this->_getEnqueueStyleReturnMap());

        $wp_enqueue_script = new PHPUnit_Extensions_MockFunction('wp_enqueue_script');
        $wp_enqueue_script->expects($this->exactly(3))->will($this->_getEnqueueScriptReturnMap());

        org_tubepress_impl_env_wordpress_OptionsPage::initAction('settings_page_tubepress');
    }

    private function _getEnqueueStyleReturnMap()
    {
        $returnMapBuilder = new PHPUnit_Extensions_MockObject_Stub_ReturnMapping_Builder();

        $returnMapBuilder->addEntry()->with(array('jquery-ui-flick'));
        $returnMapBuilder->addEntry()->with(array('tubepress-options-page'));
        $returnMapBuilder->addEntry()->with(array('jquery-ui-multiselect-widget'));

        return $returnMapBuilder->build();
    }

    private function _getEnqueueScriptReturnMap()
    {
        $returnMapBuilder = new PHPUnit_Extensions_MockObject_Stub_ReturnMapping_Builder();

        $returnMapBuilder->addEntry()->with(array('jquery-ui-tubepress'));
        $returnMapBuilder->addEntry()->with(array('jquery-ui-multiselect-widget'));
        $returnMapBuilder->addEntry()->with(array('jscolor-tubepress'));

        return $returnMapBuilder->build();
    }

    private function _getRegisterStyleReturnMap()
    {
        $returnMapBuilder = new PHPUnit_Extensions_MockObject_Stub_ReturnMapping_Builder();

        $returnMapBuilder->addEntry()->with(array('jquery-ui-flick', 'foobar'));
        $returnMapBuilder->addEntry()->with(array('tubepress-options-page', 'foobar2'));
        $returnMapBuilder->addEntry()->with(array('jquery-ui-multiselect-widget', 'foobar3'));

        return $returnMapBuilder->build();
    }

    private function _getPluginsUrlReturnMap()
    {
         $returnMapBuilder = new PHPUnit_Extensions_MockObject_Stub_ReturnMapping_Builder();

         $returnMapBuilder->addEntry()->with(array('base_name/sys/ui/static/css/jquery-ui-flick/jquery-ui-1.8.16.custom.css', 'base_name'))->will($this->returnValue('foobar'));
         $returnMapBuilder->addEntry()->with(array('base_name/sys/ui/static/css/wordpress-options-page.css', 'base_name'))->will($this->returnValue('foobar2'));
         $returnMapBuilder->addEntry()->with(array('base_name/sys/ui/static/css/jquery-ui-multiselect-widget/jquery.multiselect.css', 'base_name'))->will($this->returnValue('foobar3'));
         $returnMapBuilder->addEntry()->with(array('base_name/sys/ui/static/js/jscolor/jscolor.js', 'base_name'))->will($this->returnValue('fooey'));
         $returnMapBuilder->addEntry()->with(array('base_name/sys/ui/static/js/jquery-ui/jquery-ui-1.8.16.custom.min.js', 'base_name'))->will($this->returnValue('fooey2'));
         $returnMapBuilder->addEntry()->with(array('base_name/sys/ui/static/js/jquery-ui-multiselect-widget/jquery.multiselect.min.js', 'base_name'))->will($this->returnValue('fooey3'));

         return $returnMapBuilder->build();
    }

    private function _getRegisterScriptReturnMap()
    {
        $returnMapBuilder = new PHPUnit_Extensions_MockObject_Stub_ReturnMapping_Builder();

        $returnMapBuilder->addEntry()->with(array('jscolor-tubepress', 'fooey'));
        $returnMapBuilder->addEntry()->with(array('jquery-ui-tubepress', 'fooey2'));
        $returnMapBuilder->addEntry()->with(array('jquery-ui-multiselect-widget', 'fooey3'));

        return $returnMapBuilder->build();
    }

    private function _setupWorkingNonce()
    {
    	$adminCheck = new PHPUnit_Extensions_MockFunction('check_admin_referer');

    	$adminCheck->expects($this->once())->with('tubepress-save', 'tubepress-nonce')->will($this->returnValue(true));
    }

}

<?php

require_once BASE . '/sys/classes/org/tubepress/impl/env/wordpress/OptionsPage.class.php';

class org_tubepress_impl_env_wordpress_OptionsPageTest extends TubePressUnitTest {

    function setUp()
    {
        parent::setUp();

        $_POST = array();
    }

    function testSubmitThrowsException()
    {
        $_POST['tubepress_save'] = true;

        $addOptionsPageFunctionMock = new PHPUnit_Extensions_MockFunction('add_options_page');

        $ioc  = org_tubepress_impl_ioc_IocContainer::getInstance();

        $wpsm = $ioc->get('org_tubepress_api_options_StorageManager');
        $wpsm->shouldReceive('init')->once();

        $formHandler = $ioc->get(org_tubepress_api_options_ui_FormHandler::_);
        $formHandler->shouldReceive('getHtml')->once()->andReturn('yo');
        $formHandler->shouldReceive('onSubmit')->once()->with($_POST)->andThrow(new Exception('something!'));

        ob_start();
        org_tubepress_impl_env_wordpress_OptionsPage::executeOptionsPage();
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('<div id="message" class="error fade"><p><strong>something!</strong></p></div>yo', $contents);
    }

    function testSubmitValidValue()
    {
        $_POST['tubepress_save'] = true;

        $addOptionsPageFunctionMock = new PHPUnit_Extensions_MockFunction('add_options_page');

        $ioc  = org_tubepress_impl_ioc_IocContainer::getInstance();

        $wpsm = $ioc->get('org_tubepress_api_options_StorageManager');
        $wpsm->shouldReceive('init')->once();

        $formHandler = $ioc->get(org_tubepress_api_options_ui_FormHandler::_);
        $formHandler->shouldReceive('getHtml')->once()->andReturn('yo');
        $formHandler->shouldReceive('onSubmit')->once()->with($_POST)->andReturn(null);

        ob_start();
        org_tubepress_impl_env_wordpress_OptionsPage::executeOptionsPage();
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('<div id="message" class="updated fade"><p><strong>Options updated</strong></p></div>yo', $contents);
    }

    function testSubmitInvalidValue()
    {
        $_POST['tubepress_save'] = true;

        $addOptionsPageFunctionMock = new PHPUnit_Extensions_MockFunction('add_options_page');

        $ioc  = org_tubepress_impl_ioc_IocContainer::getInstance();

        $wpsm = $ioc->get('org_tubepress_api_options_StorageManager');
        $wpsm->shouldReceive('init')->once();

        $formHandler = $ioc->get(org_tubepress_api_options_ui_FormHandler::_);
        $formHandler->shouldReceive('getHtml')->once()->andReturn('yo');
        $formHandler->shouldReceive('onSubmit')->once()->with($_POST)->andReturn(array('bad value!', 'another bad value!'));

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

        $wpsm = $ioc->get('org_tubepress_api_options_StorageManager');
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
	    $plugins_url->expects($this->exactly(2))->will($this->_getPluginsUrlReturnMap());

        $wp_register_style = new PHPUnit_Extensions_MockFunction('wp_register_style');
        $wp_register_style->expects($this->once())->with('jquery-ui-lightness', 'foobar');

        $wp_register_script = new PHPUnit_Extensions_MockFunction('wp_register_script');
        $wp_register_script->expects($this->once())->with('jscolor-tubepress', 'fooey');

        $wp_enqueue_style = new PHPUnit_Extensions_MockFunction('wp_enqueue_style');
        $wp_enqueue_style->expects($this->once())->with('jquery-ui-lightness');

        $wp_enqueue_script = new PHPUnit_Extensions_MockFunction('wp_enqueue_script');
        $wp_enqueue_script->expects($this->exactly(2))->will($this->_getEnqueueScriptReturnMap());

        org_tubepress_impl_env_wordpress_OptionsPage::initAction('settings_page_tubepress');
    }

    private function _getPluginsUrlReturnMap()
    {
         $returnMapBuilder = new PHPUnit_Extensions_MockObject_Stub_ReturnMapping_Builder();

         $returnMapBuilder->addEntry()->with(array('base_name/sys/ui/static/css/jquery-ui-lightness/jquery-ui-1.8.16.custom.css', 'base_name'))->will($this->returnValue('foobar'));
         $returnMapBuilder->addEntry()->with(array('base_name/sys/ui/static/js/jscolor/jscolor.js', 'base_name'))->will($this->returnValue('fooey'));

         return $returnMapBuilder->build();
    }

    private function _getEnqueueScriptReturnMap()
    {
         $returnMapBuilder = new PHPUnit_Extensions_MockObject_Stub_ReturnMapping_Builder();

         $returnMapBuilder->addEntry()->with(array('jquery-ui-tabs'));
         $returnMapBuilder->addEntry()->with(array('jscolor-tubepress'));

         return $returnMapBuilder->build();
    }
}

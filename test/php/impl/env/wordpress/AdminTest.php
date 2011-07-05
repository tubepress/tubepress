<?php

require_once dirname(__FILE__) . '/../../../../../sys/classes/org/tubepress/impl/env/wordpress/Admin.class.php';

class org_tubepress_impl_env_wordpress_AdminTest extends TubePressUnitTest {

    function setUp()
    {
        parent::setUp();
    }

    function testConditionalExecuteOptionsPage()
    {
        $addOptionsPageFunctionMock = new PHPUnit_Extensions_MockFunction('add_options_page');

        $ioc  = org_tubepress_impl_ioc_IocContainer::getInstance();

        $wpsm = $ioc->get('org_tubepress_api_options_StorageManager');
        $wpsm->shouldReceive('init')->once();

        $formHandler = $ioc->get('org_tubepress_impl_options_FormHandler');
        $formHandler->shouldReceive('getHtml')->once()->andReturn('yo');

        ob_start();
        org_tubepress_impl_env_wordpress_Admin::conditionalExecuteOptionsPage();
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('yo', $contents);
    }

    function testMenuAction()
    {
        $add_options_page = new PHPUnit_Extensions_MockFunction('add_options_page');
        $add_options_page->expects($this->once())->with('TubePress Options', 'TubePress', 'manage_options', $this->stringContains('sys/classes/org/tubepress/impl/env/wordpress/Admin.class.php', true), array('org_tubepress_impl_env_wordpress_Admin', 'conditionalExecuteOptionsPage'));

        org_tubepress_impl_env_wordpress_Admin::menuAction();
    }

    function testInit()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $fs = $ioc->get('org_tubepress_api_filesystem_Explorer');
        $fs->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('base_install_path');

        $wp_register_style = new PHPUnit_Extensions_MockFunction('wp_register_style');
        $wp_register_style->expects($this->once())->with('jquery-ui-flick', '<tubepress_base_url>/sys/ui/static/css/jquery-ui-flick/jquery-ui-1.7.2.custom.css');

        $wp_register_script = new PHPUnit_Extensions_MockFunction('wp_register_script');
        $wp_register_script->expects($this->once())->with('jscolor-tubepress', '<tubepress_base_url>/sys/ui/static/js/jscolor/jscolor.js');

        $wp_enqueue_style = new PHPUnit_Extensions_MockFunction('wp_enqueue_style');
        $wp_enqueue_style->expects($this->once())->with('jquery-ui-flick');

        $wp_enqueue_script = new PHPUnit_Extensions_MockFunction('wp_enqueue_script');
        $wp_enqueue_script->expects($this->exactly(2))->will($this->_getEnqueueScriptReturnMap());

        org_tubepress_impl_env_wordpress_Admin::initAction('tubepress/Admin.class.php');
    }

    private function _getEnqueueScriptReturnMap()
    {
         $returnMapBuilder = new PHPUnit_Extensions_MockObject_Stub_ReturnMapping_Builder();

         $returnMapBuilder->addEntry()->with(array('jquery-ui-tabs'));
         $returnMapBuilder->addEntry()->with(array('jscolor-tubepress'));

         return $returnMapBuilder->build();
    }
}

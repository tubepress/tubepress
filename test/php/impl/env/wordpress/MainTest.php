<?php

require_once dirname(__FILE__) . '/../../../../../sys/classes/org/tubepress/impl/env/wordpress/Main.class.php';

class org_tubepress_impl_env_wordpress_MainTest extends TubePressUnitTest {

    function testContentFilter()
    {
        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $wpsm    = $ioc->get('org_tubepress_api_options_StorageManager');
        $wpsm->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Advanced::KEYWORD)->andReturn('trigger word');

        $parser = $ioc->get('org_tubepress_api_shortcode_ShortcodeParser');
        $parser->shouldReceive('somethingToParse')->times(2)->with('the content', 'trigger word')->andReturn(true);
        $parser->shouldReceive('somethingToParse')->times(2)->with('html for shortcode', 'trigger word')->andReturn(true, false);

        $gallery = $ioc->get('org_tubepress_api_shortcode_ShortcodeHtmlGenerator');
        $gallery->shouldReceive('getHtmlForShortcode')->once()->with('the content')->andReturn('html for shortcode');
        $gallery->shouldReceive('getHtmlForShortcode')->once()->with('html for shortcode')->andReturn('html for shortcode');

        $context = $ioc->get('org_tubepress_api_exec_ExecutionContext');
        $context->shouldReceive('getActualShortcodeUsed')->times(4)->andReturn('<current shortcode>');
        $context->shouldReceive('reset')->twice();

        $ms      = $ioc->get('org_tubepress_api_message_MessageService');

        $this->assertEquals('html for shortcode', org_tubepress_impl_env_wordpress_Main::contentFilter('the content'));
    }

    function testHeadAction()
    {
        $is_admin = new PHPUnit_Extensions_MockFunction('is_admin');
        $is_admin->expects($this->once())->will($this->returnValue(false));

        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $hh  = $ioc->get('org_tubepress_api_html_HeadHtmlGenerator');

        $hh->shouldReceive('getHeadInlineJs')->once()->andReturn('inline js');
        $hh->shouldReceive('getHeadHtmlMeta')->once()->andReturn('html meta');

        ob_start();
        org_tubepress_impl_env_wordpress_Main::headAction();
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('inline js
html meta', $contents);
    }

    function testInitAction()
    {
        $is_admin = new PHPUnit_Extensions_MockFunction('is_admin');
        $is_admin->expects($this->once())->will($this->returnValue(false));

        $wp_register_script = new PHPUnit_Extensions_MockFunction('wp_register_script');
        $wp_register_script->expects($this->once())->with('tubepress', '<tubepress_base_url>/sys/ui/static/js/tubepress.js');

        $wp_register_style = new PHPUnit_Extensions_MockFunction('wp_register_style');
        $wp_register_style->expects($this->once())->with('tubepress', '<tubepress_base_url>/sys/ui/themes/default/style.css');

        $wp_enqueue_script = new PHPUnit_Extensions_MockFunction('wp_enqueue_script');
        $wp_enqueue_script->expects($this->exactly(2))->will($this->_getEnqueueScriptReturnMap());

        $wp_enqueue_style = new PHPUnit_Extensions_MockFunction('wp_enqueue_style');
        $wp_enqueue_style->expects($this->once())->with('tubepress');

        org_tubepress_impl_env_wordpress_Main::initAction();
    }

    private function _getEnqueueScriptReturnMap()
    {
         $returnMapBuilder = new PHPUnit_Extensions_MockObject_Stub_ReturnMapping_Builder();

         $returnMapBuilder->addEntry()->with(array('jquery'));
         $returnMapBuilder->addEntry()->with(array('tubepress'));

         return $returnMapBuilder->build();
    }
}

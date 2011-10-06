<?php

require_once BASE . '/sys/classes/org/tubepress/impl/env/wordpress/Widget.class.php';

class org_tubepress_impl_env_wordpress_WidgetTest extends TubePressUnitTest {


    function testPrintWidgetControl()
    {
        $iocContainer = org_tubepress_impl_ioc_IocContainer::getInstance();

        $msg          = $iocContainer->get('org_tubepress_api_message_MessageService');
        $msg->shouldReceive('_')->atLeast(1)->andReturnUsing( function ($key) {
            return "<<$key>>";
        });

        $wpsm         = $iocContainer->get('org_tubepress_api_options_StorageManager');
        $wpsm->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Widget::TITLE)->andReturn('value of widget title');
        $wpsm->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Widget::TAGSTRING)->andReturn('value of widget shortcode');

        $explorer     = $iocContainer->get(org_tubepress_api_filesystem_Explorer::_);
        $explorer->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('fakepath');

        $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_impl_env_wordpress_Widget::WIDGET_CONTROL_TITLE, '<<Title>>');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_impl_env_wordpress_Widget::WIDGET_TITLE, 'value of widget title');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_impl_env_wordpress_Widget::WIDGET_CONTROL_SHORTCODE, '<<TubePress shortcode for the widget. See the <a href="http://tubepress.org/documentation"> documentation</a>.>>');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_impl_env_wordpress_Widget::WIDGET_SHORTCODE, 'value of widget shortcode');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_impl_env_wordpress_Widget::WIDGET_SUBMIT_TAG, org_tubepress_impl_env_wordpress_Widget::WIDGET_SUBMIT_TAG);
        $mockTemplate->shouldReceive('toString')->once()->andReturn('final result');

        $tplBuilder   = $iocContainer->get('org_tubepress_api_template_TemplateBuilder');
        $tplBuilder->shouldReceive('getNewTemplateInstance')->once()->with('fakepath/sys/ui/templates/wordpress/widget_controls.tpl.php')->andReturn($mockTemplate);

        ob_start();
        org_tubepress_impl_env_wordpress_Widget::printControlPanel();
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('final result', $contents);
    }

    function testPrintWidget()
    {
        $iocContainer = org_tubepress_impl_ioc_IocContainer::getInstance();
        $context      = $iocContainer->get(org_tubepress_api_exec_ExecutionContext::_);
        $parser       = $iocContainer->get('org_tubepress_api_shortcode_ShortcodeParser');
        $gallery      = $iocContainer->get('org_tubepress_api_shortcode_ShortcodeHtmlGenerator');
        $ms           = $iocContainer->get('org_tubepress_api_message_MessageService');

        $ms->shouldReceive('_')->atLeast(1)->andReturnUsing( function ($key) {
            return "<<$key>>";
        });

        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Widget::TAGSTRING)->andReturn('shortcode string');
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Display::THEME)->andReturn('theme');
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Widget::TITLE)->andReturn('widget title');
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Advanced::DEBUG_ON)->andReturn(false);
        $context->shouldReceive('getCustomOptions')->once()->andReturn(array(org_tubepress_api_const_options_names_Display::THUMB_WIDTH => 22135));
        $context->shouldReceive('setCustomOptions')->once()->with(array(
            org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE    => 3,
            org_tubepress_api_const_options_names_Meta::VIEWS                  => false,
            org_tubepress_api_const_options_names_Meta::DESCRIPTION            => true,
            org_tubepress_api_const_options_names_Display::DESC_LIMIT          => 50,
            org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME => org_tubepress_api_const_options_values_PlayerValue::POPUP,
            org_tubepress_api_const_options_names_Display::THUMB_HEIGHT        => 105,
            org_tubepress_api_const_options_names_Display::THUMB_WIDTH         => 22135,
            org_tubepress_api_const_options_names_Display::PAGINATE_ABOVE      => false,
            org_tubepress_api_const_options_names_Display::PAGINATE_BELOW      => false,
            org_tubepress_api_const_options_names_Display::THEME               => 'sidebar',
            org_tubepress_api_const_options_names_Display::FLUID_THUMBS        => false
        ));
        $context->shouldReceive('reset')->once();

        $gallery->shouldReceive('getHtmlForShortcode')->once()->with('')->andReturn('html result');

        $parser->shouldReceive('parse')->once()->with('shortcode string');

        ob_start();
        org_tubepress_impl_env_wordpress_Widget::printWidget(array(
    		'before_widget' => 'before_widget',
    		'before_title'  => 'before_title',
    		'after_title'   => 'after_title',
    		'after_widget'  => 'after_widget'
        ));
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('before_widgetbefore_titlewidget titleafter_titlehtml resultafter_widget', $contents);
    }

    function testInitAction()
    {
        $iocContainer = org_tubepress_impl_ioc_IocContainer::getInstance();
        $ms           = $iocContainer->get('org_tubepress_api_message_MessageService');
        $widgetOps = array('classname' => 'widget_tubepress', 'description' => '<<Displays YouTube or Vimeo videos with TubePress>>');

        $ms->shouldReceive('_')->atLeast(1)->andReturnUsing( function ($key) {
            return "<<$key>>";
        });

        $wp_register_sidebar_widget = new PHPUnit_Extensions_MockFunction('wp_register_sidebar_widget');
        $wp_register_sidebar_widget->expects($this->once())->with('tubepress', 'TubePress', array('org_tubepress_impl_env_wordpress_Widget', 'printWidget'), $widgetOps);

        $wp_register_widget_control = new PHPUnit_Extensions_MockFunction('wp_register_widget_control');
        $wp_register_widget_control->expects($this->once())->with('tubepress', 'TubePress', array('org_tubepress_impl_env_wordpress_Widget', 'printControlPanel'));

        org_tubepress_impl_env_wordpress_Widget::initAction();
    }
}

<?php

require_once BASE . '/sys/classes/org/tubepress/impl/plugin/listeners/WordPressBoot.class.php';

class org_tubepress_impl_plugin_listeners_WordPressBootTest extends TubePressUnitTest
{
    private $_sut;

    function setup()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_plugin_listeners_WordPressBoot();
    }

    function testWordPress()
    {
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();

        $ed  = $ioc->get('org_tubepress_api_environment_Detector');
        $ed->shouldReceive('isWordPress')->once()->andReturn(true);

        $fse  = $ioc->get('org_tubepress_api_filesystem_Explorer');
        $fse->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('some/path');

        $get_option = new PHPUnit_Extensions_MockFunction('get_option');
        $get_option->expects($this->once())->with('siteurl')->will($this->returnValue('valueofsiteurl'));

        $loadPluginTextDomain = new PHPUnit_Extensions_MockFunction('load_plugin_textdomain');
        $loadPluginTextDomain->expects($this->once())->with('tubepress', false, 'path/sys/i18n');

        $add_filter = new PHPUnit_Extensions_MockFunction('add_filter');
        $add_filter->expects($this->once())->with('the_content', array('org_tubepress_impl_env_wordpress_Main', 'contentFilter'));

        $add_action = new PHPUnit_Extensions_MockFunction('add_action');
        $add_action->expects($this->exactly(5))->will($this->_getAddActionReturnMap());

        $this->_sut->on_boot();

        global $tubepress_base_url;
        $this->assertEquals('valueofsiteurl/wp-content/plugins/path', $tubepress_base_url);
    }

    function testNonWordPress()
    {
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();

        $ed  = $ioc->get('org_tubepress_api_environment_Detector');
        $ed->shouldReceive('isWordPress')->once()->andReturn(false);

        $this->_sut->on_boot();
    }

    private function _getAddActionReturnMap()
    {
        $returnMapBuilder = new PHPUnit_Extensions_MockObject_Stub_ReturnMapping_Builder();

        $returnMapBuilder->addEntry()->with(array('wp_head',     array('org_tubepress_impl_env_wordpress_Main', 'headAction')));
        $returnMapBuilder->addEntry()->with(array('init',        array('org_tubepress_impl_env_wordpress_Main', 'initAction')));
        $returnMapBuilder->addEntry()->with(array('admin_menu',            array('org_tubepress_impl_env_wordpress_Admin', 'menuAction')));
        $returnMapBuilder->addEntry()->with(array('admin_enqueue_scripts', array('org_tubepress_impl_env_wordpress_Admin', 'initAction')));
        $returnMapBuilder->addEntry()->with(array('widgets_init', array('org_tubepress_impl_env_wordpress_Widget', 'initAction')));

        return $returnMapBuilder->build();
    }


}


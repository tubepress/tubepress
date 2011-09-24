<?php

require_once BASE . '/sys/classes/org/tubepress/impl/theme/SimpleThemeHandler.class.php';

class org_tubepress_impl_theme_SimpleThemeHandlerTest extends TubePressUnitTest
{
    private $_sut;

    public function setup()
    {
    parent::setUp();
        $this->_sut = new org_tubepress_impl_theme_SimpleThemeHandler();
    }

    public function testGetCssPathAbsolute()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        
        $envDetector = $ioc->get('org_tubepress_api_environment_Detector');
        $envDetector->shouldReceive('isWordPress')->once()->andReturn(false);
        
        $fs  = $ioc->get('org_tubepress_api_filesystem_Explorer');
        $fs->shouldReceive('getTubePressBaseInstallationPath')->twice()->andReturn('basePath');

        $result = $this->_sut->getCssPath('foo');
        $this->assertEquals('basePath/sys/ui/themes/default/style.css', $result);
    }

    public function testCalculateCurrentThemeNameNoCustomTheme()
    {
        $ioc                       = org_tubepress_impl_ioc_IocContainer::getInstance();
        $execContext  = $ioc->get('org_tubepress_api_exec_ExecutionContext');
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Display::THEME)->andReturn('');

        $result = $this->_sut->calculateCurrentThemeName();
        $this->assertEquals('default', $result);
    }

    public function testCalculateCurrentThemeNameCustomTheme()
    {
        $ioc                       = org_tubepress_impl_ioc_IocContainer::getInstance();
        $execContext  = $ioc->get('org_tubepress_api_exec_ExecutionContext');
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Display::THEME)->andReturn('foo');

        $result = $this->_sut->calculateCurrentThemeName();
        $this->assertEquals('foo', $result);
    }

    /**
     * @expectedException Exception
     */
    public function testGetTemplateInstanceNoSuchTemplate()
    {
        $ioc                       = org_tubepress_impl_ioc_IocContainer::getInstance();
        $execContext  = $ioc->get('org_tubepress_api_exec_ExecutionContext');
        $execContext->shouldReceive('get')->zeroOrMoreTimes()->with(org_tubepress_api_const_options_names_Display::THEME)->andReturn('');

        $fs = $ioc->get('org_tubepress_api_filesystem_Explorer');
        $fs->shouldReceive('getTubePressBaseInstallationPath')->zeroOrMoreTimes()->andReturn('basePath');

        $this->_sut->getTemplateInstance('foo');
    }

    public function testGetUserContentDirWordPress()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
    
        $execContext = $ioc->get('org_tubepress_api_environment_Detector');
        $execContext->shouldReceive('isWordPress')->once()->andReturn(true);
    
        if (!defined('ABSPATH')) {
        	
        	define('ABSPATH', '/value-of-abspath/');
        }
        
        $this->assertEquals('/value-of-abspath/wp-content/tubepress-content', $this->_sut->getUserContentDirectory());
    }
    
    public function testGetUserContentDirNonWordPress()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
    
        $execContext = $ioc->get('org_tubepress_api_environment_Detector');
        $execContext->shouldReceive('isWordPress')->once()->andReturn(false);

        $fs = $ioc->get('org_tubepress_api_filesystem_Explorer');
        $fs->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('foobar');
        
        $this->assertEquals('foobar/tubepress-content', $this->_sut->getUserContentDirectory());
    }
    
    public function testGetTemplateInstance()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $execContext  = $ioc->get('org_tubepress_api_exec_ExecutionContext');
        $execContext->shouldReceive('get')->zeroOrMoreTimes()->with(org_tubepress_api_const_options_names_Display::THEME)->andReturn('foo');

        $fs                        = $ioc->get('org_tubepress_api_filesystem_Explorer');
        $fs->shouldReceive('getTubePressBaseInstallationPath')->zeroOrMoreTimes()->andReturn(realpath(dirname(__FILE__) . '/../../../../'));
        
        $envDetector = $ioc->get('org_tubepress_api_environment_Detector');
        $envDetector->shouldReceive('isWordPress')->once()->andReturn(false);

        $tb = $ioc->get('org_tubepress_api_template_TemplateBuilder');
        $tb->shouldReceive('getNewTemplateInstance')->zeroOrMoreTimes()->andReturn('result');

        $this->assertEquals('result', $this->_sut->getTemplateInstance('gallery.tpl.php'));
    }
}
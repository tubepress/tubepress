<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/theme/SimpleThemeHandler.class.php';
require_once dirname(__FILE__) . '/../../../../../test/unit/TubePressUnitTest.php';

class org_tubepress_theme_SimpleThemeHandlerTest extends TubePressUnitTest
{
    private $_sut;
    
    public function setup()
    {
	$this->initFakeIoc();
        $this->_sut = new org_tubepress_theme_SimpleThemeHandler();
    }
    
	public function testGetCssPathRelative()
	{
		$result = $this->_sut->getCssPath('foo', true);
		$this->assertEquals('ui/themes/default/style.css', $result);
	}

	public function testGetCssPathAbsolute()
	{
		$result = $this->_sut->getCssPath('foo');
		$this->assertEquals(realpath(dirname(__FILE__) . '/../../../../../') . '/ui/themes/default/style.css', $result);
	}

	public function testCalculateCurrentThemeNameNoCustomTheme()
	{
		$result = $this->_sut->calculateCurrentThemeName();
		$this->assertEquals('default', $result);
	}

	public function testCalculateCurrentThemeNameCustomTheme()
	{
		$this->setOptions(array(org_tubepress_api_const_options_Display::THEME => 'foo'));
		$result = $this->_sut->calculateCurrentThemeName();
		$this->assertEquals('foo', $result);
	}

    public function testGetTemplateInstanceNoSuchTemplate()
    {
		$this->setExpectedException('Exception');
		$result = $this->_sut->getTemplateInstance('foo');
		$this->assertEquals('foo', $result);
    }

    public function testGetTemplateInstance()
    {
		$result = $this->_sut->getTemplateInstance('gallery.tpl.php');
		$this->assertTrue(is_a($result, 'org_tubepress_api_template_Template'));
    }

    
}
?>

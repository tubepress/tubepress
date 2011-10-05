<?php

require_once 'AbstractOptionDescriptorBasedWidgetTest.php';
require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/widgets/MultiSelectInput.class.php';

class org_tubepress_impl_options_ui_widgets_MultiSelectWidgetTest extends TubePressUnitTest {

    private $_sut;

    private $_ods;

    public function setUp()
    {
        parent::setUp();

        $this->_ods = $this->_getFakeOds();

        $this->_sut = new org_tubepress_impl_options_ui_widgets_MultiSelectInput($this->_ods, 'label', 'description');
    }

    public function testGetTitle()
    {
        $this->assertEquals('label', $this->_sut->getTitle());
    }

    public function testGetDesc()
    {
        $this->assertEquals('description', $this->_sut->getDescription());
    }

    public function testIsProOnly()
    {
        $this->assertFalse($this->_sut->isProOnly());
    }

    public function testGetProviders()
    {
        $this->assertEquals(array(
            org_tubepress_api_provider_Provider::YOUTUBE,
            org_tubepress_api_provider_Provider::VIMEO,
            ), $this->_sut->getArrayOfApplicableProviderNames());
    }

    public function testOnSubmit()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $sm  = $ioc->get(org_tubepress_api_options_StorageManager::_);

        $sm->shouldReceive('set')->once()->with('name-one', true);
        $sm->shouldReceive('set')->once()->with('name-two', false);

        $this->_sut->onSubmit(array('label' => array('name-one')));
    }

    public function testGetHtml()
    {
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $templateBldr = $ioc->get(org_tubepress_api_template_TemplateBuilder::_);
        $fse          = $ioc->get(org_tubepress_api_filesystem_Explorer::_);
        $sm           = $ioc->get(org_tubepress_api_options_StorageManager::_);
        $template = \Mockery::mock(org_tubepress_api_template_Template::_);
        $template->shouldReceive('setVariable')->once()->with(org_tubepress_impl_options_ui_widgets_MultiSelectInput::TEMPLATE_VAR_NAME, 'label');
        $template->shouldReceive('setVariable')->once()->with(org_tubepress_impl_options_ui_widgets_MultiSelectInput::TEMPLATE_VAR_DESCRIPTORS, $this->_ods);
        $template->shouldReceive('setVariable')->once()->with(org_tubepress_impl_options_ui_widgets_MultiSelectInput::TEMPLATE_VAR_CURRENTVALUES, array('name-two'));
        $template->shouldReceive('toString')->once()->andReturn('boogity');
        $sm->shouldReceive('get')->once()->with('name-one')->andReturn(false);
        $sm->shouldReceive('get')->once()->with('name-two')->andReturn(true);
        $fse->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('<<basepath>>');

        $templateBldr->shouldReceive('getNewTemplateInstance')->once()->with('<<basepath>>/sys/ui/templates/options_page/widgets/multiselect.tpl.php')->andReturn($template);


        $this->assertEquals('boogity', $this->_sut->getHtml());
    }

    /**
     * @expectedException Exception
     */
    public function testBuildBadDescriptors()
    {
        new org_tubepress_impl_options_ui_widgets_MultiSelectInput(3, 'label', 'desc');
    }

    /**
    * @expectedException Exception
    */
    public function testBuildBadLabel()
    {
        new org_tubepress_impl_options_ui_widgets_MultiSelectInput($this->_getFakeOds(), null, 'desc');
    }

    /**
    * @expectedException Exception
    */
    public function testNonOd()
    {
        new org_tubepress_impl_options_ui_widgets_MultiSelectInput(array('yo'), 'label', 'desc');
    }

    /**
    * @expectedException Exception
    */
    public function testNonBooleanOd()
    {
        $three = \Mockery::mock(org_tubepress_api_options_OptionDescriptor::_);
        $three->shouldReceive('isBoolean')->once()->andReturn(false);

        $a   = array($three);

        new org_tubepress_impl_options_ui_widgets_MultiSelectInput($a, 'label', 'desc');
    }

    private function _getFakeOds()
    {
        $one = \Mockery::mock(org_tubepress_api_options_OptionDescriptor::_);
        $one->shouldReceive('isBoolean')->once()->andReturn(true);
        $one->shouldReceive('getName')->andReturn('name-one');

        $two = \Mockery::mock(org_tubepress_api_options_OptionDescriptor::_);
        $two->shouldReceive('isBoolean')->once()->andReturn(true);
        $two->shouldReceive('getName')->andReturn('name-two');

        return array($one, $two);
    }
}


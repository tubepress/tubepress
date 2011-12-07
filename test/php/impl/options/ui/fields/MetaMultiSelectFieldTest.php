<?php

require_once 'AbstractFieldTest.php';
require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/fields/MetaMultiSelectField.class.php';

class org_tubepress_impl_options_ui_fields_MetaMultiSelectFieldTest extends org_tubepress_impl_options_ui_fields_AbstractFieldTest {

    private $_sut;

    private $_ods;

    public function setUp()
    {
        parent::setUp();

        $this->_ods = $this->_getFakeOds();

        $this->_sut = new org_tubepress_impl_options_ui_fields_MetaMultiSelectField($this->_ods, 'crazy');
    }

    public function testGetTitle()
    {
        $this->assertEquals('<<message: Meta display>>', $this->_sut->getTitle());
    }

    public function testGetDesc()
    {
        $this->assertEquals('<<message: Meta-information to display with each video>>', $this->_sut->getDescription());
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

        $odNames      = $this->_getOdNames();
        $indexOfFalse = array_rand($odNames);
        $postVars     = array();

        for ($x = 0; $x < count($odNames); $x++) {

            $keep = $x !== $indexOfFalse;

            $sm->shouldReceive('set')->once()->with($odNames[$x], $keep);

            if ($keep) {

                $postVars[] = $odNames[$x];
            }
        }

        $this->_sut->onSubmit(array('metadropdown' => $postVars));
    }

    public function testGetHtml()
    {
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $templateBldr = $ioc->get(org_tubepress_api_template_TemplateBuilder::_);
        $fse          = $ioc->get(org_tubepress_api_filesystem_Explorer::_);
        $sm           = $ioc->get(org_tubepress_api_options_StorageManager::_);
        $template     = \Mockery::mock(org_tubepress_api_template_Template::_);
        $template->shouldReceive('setVariable')->once()->with(org_tubepress_impl_options_ui_fields_AbstractMultiSelectField::TEMPLATE_VAR_NAME, 'metadropdown');
        $template->shouldReceive('setVariable')->once()->with(org_tubepress_impl_options_ui_fields_AbstractMultiSelectField::TEMPLATE_VAR_DESCRIPTORS, $this->_ods);
        $template->shouldReceive('setVariable')->once()->with(org_tubepress_impl_options_ui_fields_AbstractMultiSelectField::TEMPLATE_VAR_CURRENTVALUES, $this->_getOdNames());
        $template->shouldReceive('toString')->once()->andReturn('boogity');
        $odNames = $this->_getOdNames();

        foreach ($odNames as $odName) {

            $sm->shouldReceive('get')->once()->with($odName)->andReturn(true);
        }

        $fse->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('<<basepath>>');

        $templateBldr->shouldReceive('getNewTemplateInstance')->once()->with('<<basepath>>/sys/ui/templates/options_page/fields/multiselect.tpl.php')->andReturn($template);

        $this->assertEquals('boogity', $this->_sut->getHtml());
    }

    protected function _getOdNames()
    {
        return array(

            org_tubepress_api_const_options_names_Meta::AUTHOR,
            org_tubepress_api_const_options_names_Meta::CATEGORY,
            org_tubepress_api_const_options_names_Meta::DESCRIPTION,
            org_tubepress_api_const_options_names_Meta::ID,
            org_tubepress_api_const_options_names_Meta::LENGTH,
            org_tubepress_api_const_options_names_Meta::LIKES,
            org_tubepress_api_const_options_names_Meta::RATING,
            org_tubepress_api_const_options_names_Meta::RATINGS,
            org_tubepress_api_const_options_names_Meta::KEYWORDS,
            org_tubepress_api_const_options_names_Meta::TITLE,
            org_tubepress_api_const_options_names_Meta::UPLOADED,
            org_tubepress_api_const_options_names_Meta::URL,
            org_tubepress_api_const_options_names_Meta::VIEWS,
        );
    }

    private function _getFakeOds()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $odr = $ioc->get(org_tubepress_api_options_OptionDescriptorReference::_);

        $names = $this->_getOdNames();

        $ods = array();

        foreach ($names as $name) {

            $od = \Mockery::mock(org_tubepress_api_options_OptionDescriptor::_);
            $od->shouldReceive('isBoolean')->once()->andReturn(true);
            $od->shouldReceive('getName')->andReturn($name);

            $ods[] = $od;

            $odr->shouldReceive('findOneByName')->with($name)->once()->andReturn($od);
        }

        return $ods;
    }
}


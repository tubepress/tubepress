<?php

require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/fields/AbstractMultiSelectField.class.php';
require_once BASE . '/sys/classes/org/tubepress/api/options/OptionDescriptor.class.php';

class org_tubepress_impl_template_templates_optionspage_fields_MultiSelectTemplateTest extends TubePressUnitTest {

    public function test()
    {
        $one   = \Mockery::mock(org_tubepress_api_options_OptionDescriptor::_);
        $two   = \Mockery::mock(org_tubepress_api_options_OptionDescriptor::_);
        $three = \Mockery::mock(org_tubepress_api_options_OptionDescriptor::_);

        $one->shouldReceive('getName')->atLeast()->once()->andReturn('name-one');
        $two->shouldReceive('getName')->atLeast()->once()->andReturn('name-two');
        $one->shouldReceive('getLabel')->atLeast()->once()->andReturn('label-one');
        $two->shouldReceive('getLabel')->atLeast()->once()->andReturn('label-two');
        $three->shouldReceive('getName')->atLeast()->once()->andReturn('name-three');
        $three->shouldReceive('getLabel')->atLeast()->once()->andReturn('label-three');

        $descriptors = array($one, $two, $three);

        ${org_tubepress_impl_options_ui_fields_AbstractMultiSelectField::TEMPLATE_VAR_NAME} = 'some-name';
        ${org_tubepress_impl_options_ui_fields_AbstractMultiSelectField::TEMPLATE_VAR_DESCRIPTORS} = $descriptors;
        ${org_tubepress_impl_options_ui_fields_AbstractMultiSelectField::TEMPLATE_VAR_CURRENTVALUES} = array('crack', 'name-one', 'pittsburgh', 'steelers', 'name-three');

        ob_start();
        include BASE . '/sys/ui/templates/options_page/fields/multiselect.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(org_tubepress_impl_util_StringUtils::removeEmptyLines($this->_expected()), org_tubepress_impl_util_StringUtils::removeEmptyLines($result));
    }

    private function _expected()
    {
        return <<<EOT
<select name="some-name[]">
	<option value="name-one" selected="selected">label-one</option>
	<option value="name-two" >label-two</option>
	<option value="name-three" selected="selected">label-three</option>
	</select>
EOT;
    }

}
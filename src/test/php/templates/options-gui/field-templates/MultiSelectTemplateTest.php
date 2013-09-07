<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_test_impl_template_templates_optionspage_fields_MultiSelectTemplateTest extends tubepress_test_TubePressUnitTest
{
    public function test()
    {
        $one   = new tubepress_spi_options_OptionDescriptor('name-one');
        $two   = new tubepress_spi_options_OptionDescriptor('name-two');
        $three = new tubepress_spi_options_OptionDescriptor('name-three');

        $one->setLabel('label-one');
        $two->setLabel('label-two');
        $three->setLabel('label-three');

        $descriptors = array($one, $two, $three);

        $id = 'some-name';
        $foo = $descriptors;
        $currentlySelectedValues = array('one', 'two', 'foo');
        $ungroupedChoices = array('foo', 'bar');
        $groupedChoices = array('x' => array('y'));
        $selectText = 'yells';

        ob_start();
        include TUBEPRESS_ROOT . '/src/main/resources/options-gui/field-templates/multiselect.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(tubepress_impl_util_StringUtils::removeEmptyLines($this->_expected()), tubepress_impl_util_StringUtils::removeEmptyLines($result));
    }

    private function _expected()
    {
        return <<<EOT

<select id="some-name" name="some-name[]" multiple="multiple" class="form-control multiselect">
        <option value="0" selected="selected">foo</option>
        <option value="1" >bar</option>
        <optgroup label="x">
                <option value="0" selected="selected">y</option>
        </optgroup>
    </select>
<script type="text/javascript">
    jQuery(function() {
        jQuery('#some-name').multiselect({
            buttonClass : 'btn btn-default btn-sm',
            dropRight   : true,
            buttonText  : function (options, select) { return 'yells'; }
        });
    });
</script>
EOT;
    }

}
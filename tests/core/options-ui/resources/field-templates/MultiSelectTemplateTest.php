<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_test_impl_template_templates_optionspage_fields_MultiSelectTemplateTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_util_StringUtils
     */
    private $_stringUtils;

    public function onSetup()
    {
        $this->_stringUtils = new tubepress_impl_util_StringUtils();
    }

    public function test()
    {
        $optionNames = array('name-one', 'name-two', 'name-three');

        $id = 'some-name';
        $foo = $optionNames;
        $currentlySelectedValues = array('one', 'two', 'foo');
        $ungroupedChoices = array('foo', 'bar');
        $groupedChoices = array('x' => array('y'));
        $selectText = 'yells';

        ob_start();
        include TUBEPRESS_ROOT . '/src/core/options-ui/resources/field-templates/multiselect.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($this->_stringUtils->removeEmptyLines($this->_expected()), $this->_stringUtils->removeEmptyLines($result));
    }

    private function _expected()
    {
        return <<<EOT

<select id="some-name" name="some-name[]" multiple="multiple" class="form-control multiselect tubepress-bootstrap-multiselect-field" data-selecttext="yells">
        <option value="0" selected="selected">foo</option>
        <option value="1" >bar</option>
        <optgroup label="x">
                <option value="0" selected="selected">y</option>
        </optgroup>
    </select>
EOT;
    }

}
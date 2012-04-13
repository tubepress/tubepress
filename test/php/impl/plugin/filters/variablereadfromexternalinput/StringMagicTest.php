<?php

require_once dirname(__FILE__) . '/../AbstractStringMagicFilterTest.php';
require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/plugin/filters/variablereadfromexternalinput/StringMagic.class.php';

class org_tubepress_impl_plugin_filters_variablereadfromexternalinput_StringMagicTest extends org_tubepress_impl_plugin_filters_AbstractStringMagicFilterTest
{

    protected function _buildSut()
    {
        return new org_tubepress_impl_plugin_filters_variablereadfromexternalinput_StringMagic();
    }

    protected function _performAltering($sut, $value, $name)
    {
        return $sut->alter_variableReadFromExternalInput($value, $name);
    }
}
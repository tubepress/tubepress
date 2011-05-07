<?php
require_once dirname(__FILE__) . '/../../../../../../../../sys/classes/org/tubepress/api/const/options/values/ModeValue.class.php';
require_once dirname(__FILE__) . '/../../../../../../TubePressUnitTest.php';

class org_tubepress_api_const_options_values_ModeValueTest extends TubePressUnitTest {

    function testConstants()
    {
        $expected = array(org_tubepress_api_const_options_values_ModeValue::FAVORITES, org_tubepress_api_const_options_values_ModeValue::FEATURED, org_tubepress_api_const_options_values_ModeValue::MOST_DISCUSSED, org_tubepress_api_const_options_values_ModeValue::MOST_RECENT, org_tubepress_api_const_options_values_ModeValue::MOST_RESPONDED, org_tubepress_api_const_options_values_ModeValue::PLAYLIST, org_tubepress_api_const_options_values_ModeValue::POPULAR, org_tubepress_api_const_options_values_ModeValue::TAG, org_tubepress_api_const_options_values_ModeValue::TOP_FAVORITES,org_tubepress_api_const_options_values_ModeValue::TOP_RATED, org_tubepress_api_const_options_values_ModeValue::USER, 'vimeoUploadedBy', 'vimeoLikes', 'vimeoAppearsIn', 'vimeoSearch', 'vimeoCreditedTo', 'vimeoChannel','vimeoAlbum', 'vimeoGroup');

        self::checkArrayEquality(self::getConstantsForClass('org_tubepress_api_const_options_values_ModeValue'), $expected);
    }
}

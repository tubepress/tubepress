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
class tubepress_test_impl_template_templates_defaultthemes_default_PaginationTest extends tubepress_test_TubePressUnitTest
{
    public function testTemplate()
    {
        $expected = file_get_contents(TUBEPRESS_ROOT . '/src/test/add-ons/media-gallery/fixtures/pagination/pagination.txt');

        $path = TUBEPRESS_ROOT . '/src/main/add-ons/themes/web/themes/default/pagination.tpl.php';
        ${tubepress_core_template_api_const_VariableNames::PAGINATION_CURRENT_PAGE}     = 4;
        ${tubepress_core_template_api_const_VariableNames::PAGINATION_TOTAL_ITEMS}      = 99;
        ${tubepress_core_template_api_const_VariableNames::PAGINATION_RESULTS_PER_PAGE} = 10;
        ${tubepress_core_template_api_const_VariableNames::PAGINATION_HREF_FORMAT}      = 'xyz%d';
        ${tubepress_core_template_api_const_VariableNames::PAGINATION_TEXT_PREV}        = 'prevv';
        ${tubepress_core_template_api_const_VariableNames::PAGINATION_TEXT_NEXT}        = 'nextt';

        ob_start();
        include $path;
        $actual = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", ob_get_contents());
        ob_end_clean();
        $expected = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $expected);

        $this->assertEquals($expected, $actual);
    }
}
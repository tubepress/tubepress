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
        $expected = file_get_contents(TUBEPRESS_ROOT . '/src/test/resources/fixtures/templates/default-themes/default/pagination.txt');

        $path = TUBEPRESS_ROOT . '/src/main/web/themes/default/pagination.tpl.php';
        ${tubepress_api_const_template_Variable::PAGINATION_CURRENT_PAGE}     = 4;
        ${tubepress_api_const_template_Variable::PAGINATION_TOTAL_ITEMS}      = 99;
        ${tubepress_api_const_template_Variable::PAGINATION_RESULTS_PER_PAGE} = 10;
        ${tubepress_api_const_template_Variable::PAGINATION_HREF_FORMAT}      = 'xyz%d';
        ${tubepress_api_const_template_Variable::PAGINATION_TEXT_PREV}        = 'prevv';
        ${tubepress_api_const_template_Variable::PAGINATION_TEXT_NEXT}        = 'nextt';

        ob_start();
        include $path;
        $actual = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", ob_get_contents());
        ob_end_clean();
        $expected = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $expected);

        $this->assertEquals($expected, $actual);
    }
}
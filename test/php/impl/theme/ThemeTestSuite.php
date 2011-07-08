<?php
require_once BASE . '/test/includes/TubePressUnitTest.php';
require_once 'SimpleThemeHandlerTest.php';

class org_tubepress_impl_theme_ThemeTestSuite
{
    public static function suite()
    {
        return new TubePressUnitTestSuite(array(
            'org_tubepress_impl_theme_SimpleThemeHandlerTest'
        ));
    }
}


<?php

require_once 'DropdownWidgetTest.php';
require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/widgets/ThemeInput.class.php';

class org_tubepress_impl_options_ui_widgets_ThemeWidgetTest extends org_tubepress_impl_options_ui_widgets_DropdownWidgetTest {

    protected function _buildSut($name)
    {
        return new org_tubepress_impl_options_ui_widgets_ThemeInput($name);
    }

    protected function _performAdditionGetDescriptionSetup()
    {
        $ioc               = org_tubepress_impl_ioc_IocContainer::getInstance();
        $themeHandler      = $ioc->get(org_tubepress_api_theme_ThemeHandler::_);
        $filesystem        = $ioc->get(org_tubepress_api_filesystem_Explorer::_);

        $filesystem->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('<<basepath>>');
        $themeHandler->shouldReceive('getUserContentDirectory')->once()->andReturn('<<user content dir>>');

    }
}


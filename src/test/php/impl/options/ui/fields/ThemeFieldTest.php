<?php

require_once 'DropdownFieldTest.php';
require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/fields/ThemeField.class.php';

class org_tubepress_impl_options_ui_fields_ThemeFieldTest extends org_tubepress_impl_options_ui_fields_DropdownFieldTest {

    protected function _buildSut($name)
    {
        return new org_tubepress_impl_options_ui_fields_ThemeField($name);
    }

    protected function _performAdditionGetDescriptionSetup()
    {
        $ioc               = org_tubepress_impl_ioc_IocContainer::getInstance();
        $themeHandler      = $ioc->get(org_tubepress_api_environment_EnvironmentDetector::_);
        $filesystem        = $ioc->get(org_tubepress_api_filesystem_Explorer::_);

        $filesystem->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('<<basepath>>');
        $themeHandler->shouldReceive('getUserContentDirectory')->once()->andReturn('<<user content dir>>');

    }
}


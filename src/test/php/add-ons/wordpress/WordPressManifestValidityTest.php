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
class_exists('tubepress_test_impl_addon_AbstractManifestValidityTest') ||
    require dirname(__FILE__) . '/../../classes/tubepress/test/impl/addon/AbstractManifestValidityTest.php';

class tubepress_test_addons_core_WordPressManifestValidityTest extends tubepress_test_impl_addon_AbstractManifestValidityTest
{
    public function testManifest()
    {
        /**
         * @var $addon tubepress_spi_addon_Addon
         */
        $addon = $this->getAddonFromManifest(dirname(__FILE__) . '/../../../../main/php/add-ons/wordpress/wordpress.json');

        $this->assertEquals('tubepress-wordpress-addon', $addon->getName());
        $this->assertEquals('1.0.0', $addon->getVersion());
        $this->assertEquals('WordPress', $addon->getTitle());
        $this->assertEquals(array('name' => 'TubePress LLC', 'url' => 'http://tubepress.com'), $addon->getAuthor());
        $this->assertEquals(array(array('type' => 'MPL-2.0', 'url' => 'http://www.mozilla.org/MPL/2.0/')), $addon->getLicenses());
        $this->assertEquals('Allows TubePress to integrate with WordPress', $addon->getDescription());
        $this->assertEquals(array('tubepress_addons_wordpress' => TUBEPRESS_ROOT . '/src/main/php/add-ons/wordpress/classes'), $addon->getPsr0ClassPathRoots());
        $this->assertEquals(array('tubepress_addons_wordpress_impl_ioc_WordPressIocContainerExtension'), $addon->getIocContainerExtensions());
        $this->validateClassMap($this->_getExpectedClassMap(), $addon->getClassMap());
    }

    private function _getExpectedClassMap()
    {
        return array(
            'tubepress_addons_wordpress_api_const_options_names_WordPress' => 'classes/tubepress/addons/wordpress/api/const/options/names/WordPress.php',
            'tubepress_addons_wordpress_impl_DefaultContentFilter' => 'classes/tubepress/addons/wordpress/impl/DefaultContentFilter.php',
            'tubepress_addons_wordpress_impl_DefaultFrontEndCssAndJsInjector' => 'classes/tubepress/addons/wordpress/impl/DefaultFrontEndCssAndJsInjector.php',
            'tubepress_addons_wordpress_impl_DefaultWidgetHandler' => 'classes/tubepress/addons/wordpress/impl/DefaultWidgetHandler.php',
            'tubepress_addons_wordpress_impl_DefaultWordPressFunctionWrapper' => 'classes/tubepress/addons/wordpress/impl/DefaultWordPressFunctionWrapper.php',
            'tubepress_addons_wordpress_impl_DefaultWpAdminHandler' => 'classes/tubepress/addons/wordpress/impl/DefaultWpAdminHandler.php',
            'tubepress_addons_wordpress_impl_listeners_boot_WordPressApiIntegrator' => 'classes/tubepress/addons/wordpress/impl/listeners/boot/WordPressApiIntegrator.php',
            'tubepress_addons_wordpress_impl_listeners_html_CssJsDequerer' => 'classes/tubepress/addons/wordpress/impl/listeners/html/CssJsDequerer.php',
            'tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener' => 'classes/tubepress/addons/wordpress/impl/listeners/template/options/OptionsUiTemplateListener.php',
            'tubepress_addons_wordpress_impl_message_WordPressMessageService' => 'classes/tubepress/addons/wordpress/impl/message/WordPressMessageService.php',
            'tubepress_addons_wordpress_impl_options_WordPressOptionsProvider' => 'classes/tubepress/addons/wordpress/impl/options/WordPressOptionsProvider.php',
            'tubepress_addons_wordpress_impl_options_WordPressStorageManager' => 'classes/tubepress/addons/wordpress/impl/options/WordPressStorageManager.php',
            'tubepress_addons_wordpress_impl_ioc_WordPressIocContainerExtension' => 'classes/tubepress/addons/wordpress/impl/ioc/WordPressIocContainerExtension.php',
            'tubepress_addons_wordpress_spi_ContentFilter' => 'classes/tubepress/addons/wordpress/spi/ContentFilter.php',
            'tubepress_addons_wordpress_spi_FrontEndCssAndJsInjector' => 'classes/tubepress/addons/wordpress/spi/FrontEndCssAndJsInjector.php',
            'tubepress_addons_wordpress_spi_WidgetHandler' => 'classes/tubepress/addons/wordpress/spi/WidgetHandler.php',
            'tubepress_addons_wordpress_spi_WordPressFunctionWrapper' => 'classes/tubepress/addons/wordpress/spi/WordPressFunctionWrapper.php',
            'tubepress_addons_wordpress_spi_WpAdminHandler' => 'classes/tubepress/addons/wordpress/spi/WpAdminHandler.php'
        );
    }
}
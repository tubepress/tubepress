<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Adds WordPress-specific services.
 */
class tubepress_plugins_wordpress_impl_patterns_ioc_WordPressCoreIocContainerExtension implements ehough_iconic_api_extension_IExtension
{
    /**
     * Loads a specific configuration.
     *
     * @param ehough_iconic_impl_ContainerBuilder $container A ContainerBuilder instance
     *
     * @return void
     */
    public final function load(ehough_iconic_impl_ContainerBuilder $container)
    {
        $this->_registerMessageService($container);
        $this->_registerOptionsUiFormHandler($container);
        $this->_registerOptionsStorageManager($container);
    }

    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     */
    public final function getAlias()
    {
        return 'wordpress';
    }

    private function _registerMessageService(ehough_iconic_impl_ContainerBuilder $container)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $container->register(

            tubepress_spi_const_patterns_ioc_ServiceIds::MESSAGE,
            'tubepress_plugins_wordpress_impl_message_WordPressMessageService'

        );
    }

    private function _registerOptionsStorageManager(ehough_iconic_impl_ContainerBuilder $container)
    {
        $container->register(

            tubepress_spi_const_patterns_ioc_ServiceIds::OPTION_STORAGE_MANAGER,
            'tubepress_plugins_wordpress_impl_options_WordPressStorageManager'
        );
    }

    private function _registerOptionsUiFormHandler(ehough_iconic_impl_ContainerBuilder $container)
    {
        $tabsId = 'tubepress_impl_options_ui_DefaultTabsHandler';

        /** @noinspection PhpUndefinedMethodInspection */
        $container->register(

            $tabsId, $tabsId

        );

        $filterId = 'tubepress_impl_options_ui_fields_FilterMultiSelectField';

        $container->register($filterId, $filterId);

        /** @noinspection PhpUndefinedMethodInspection */
        $container->register(

            tubepress_spi_const_patterns_ioc_ServiceIds::OPTIONS_UI_FORMHANDLER,
            'tubepress_plugins_wordpress_impl_options_ui_WordPressOptionsFormHandler'

        )->addArgument(new ehough_iconic_impl_Reference($tabsId))
         ->addArgument(new ehough_iconic_impl_Reference($filterId));
    }
}
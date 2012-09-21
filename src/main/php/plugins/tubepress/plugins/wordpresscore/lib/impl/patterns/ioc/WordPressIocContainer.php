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
 * Core services IOC container. The job of this class is to ensure that each kernel service (see the constants
 * of this class) is wired up.
 */
final class tubepress_plugins_wordpresscore_lib_impl_patterns_ioc_WordPressIocContainer extends tubepress_impl_patterns_ioc_AbstractReadOnlyIocContainer
{
    const SERVICE_CONTENT_FILTER         = 'contentFilter';
    const SERVICE_CSS_AND_JS_INJECTOR    = 'cssAndJsInjector';
    const SERVICE_MESSAGE                = 'messageService';
    const SERVICE_OPTIONS_STORAGE        = 'optionsStorageManager';
    const SERVICE_OPTIONS_UI_FORMHANDLER = 'optionsUiFormHandler';
    const SERVICE_WIDGET_HANDLER         = 'widgetHandler';
    const SERVICE_WP_ADMIN_HANDLER       = 'wpAdminHandler';
    const SERVICE_WP_FUNCTION_WRAPPER    = 'wpFunctionWrapper';

    /**
     * @var ehough_iconic_api_IContainer
     */
    private $_delegate;

    public function __construct()
    {
        $this->_delegate = new ehough_iconic_impl_ContainerBuilder();

        $this->_registerContentFilter();
        $this->_registerCssAndJsInjector();
        $this->_registerMessageService();
        $this->_registerOptionsStorageManager();
        $this->_registerOptionsUiFormHandler();
        $this->_registerWidgetHandler();
        $this->_registerWpAdminHandler();
        $this->_registerWpFunctionWrapper();
    }

    private function _registerContentFilter()
    {
        $this->_delegate->register(

            self::SERVICE_CONTENT_FILTER,
            'tubepress_plugins_wordpresscore_lib_impl_DefaultContentFilter'
        );
    }

    private function _registerCssAndJsInjector()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->_delegate->register(

            self::SERVICE_CSS_AND_JS_INJECTOR,
            'tubepress_plugins_wordpresscore_lib_impl_DefaultFrontEndCssAndJsInjector'

        );
    }

    private function _registerMessageService()
    {
        $this->_delegate->register(

            self::SERVICE_MESSAGE,
            'tubepress_plugins_wordpresscore_lib_impl_message_WordPressMessageService'
        );
    }

    private function _registerOptionsStorageManager()
    {
        $this->_delegate->register(

            self::SERVICE_OPTIONS_STORAGE,
            'tubepress_plugins_wordpresscore_lib_impl_options_WordPressStorageManager'
        );
    }

    private function _registerOptionsUiFormHandler()
    {
        $tabClassNames = array(

            'tubepress_impl_options_ui_tabs_GallerySourceTab',
            'tubepress_impl_options_ui_tabs_ThumbsTab',
            'tubepress_impl_options_ui_tabs_EmbeddedTab',
            'tubepress_impl_options_ui_tabs_MetaTab',
            'tubepress_impl_options_ui_tabs_ThemeTab',
            'tubepress_impl_options_ui_tabs_FeedTab',
            'tubepress_impl_options_ui_tabs_CacheTab',
            'tubepress_impl_options_ui_tabs_AdvancedTab',
        );

        $tabReferences = array();

        foreach ($tabClassNames as $tabClassName) {

            $this->_delegate->register($tabClassName, $tabClassName);

            array_push($tabReferences, new ehough_iconic_impl_Reference($tabClassName));
        }

        $tabsId = 'tubepress_impl_options_ui_DefaultTabsHandler';

        /** @noinspection PhpUndefinedMethodInspection */
        $this->_delegate->register(

            $tabsId, $tabsId

        )->addArgument($tabReferences);

        $filterId = 'tubepress_impl_options_ui_fields_FilterMultiSelectField';

        $this->_delegate->register($filterId, $filterId);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->_delegate->register(

            self::SERVICE_OPTIONS_UI_FORMHANDLER,
            'tubepress_plugins_wordpresscore_lib_impl_options_ui_WordPressOptionsFormHandler'

        )->addArgument(new ehough_iconic_impl_Reference($tabsId))
         ->addArgument(new ehough_iconic_impl_Reference($filterId));
    }

    private function _registerWidgetHandler()
    {
        $this->_delegate->register(

            self::SERVICE_WIDGET_HANDLER,
            'tubepress_plugins_wordpresscore_lib_impl_DefaultWidgetHandler'
        );
    }

    private function _registerWpAdminHandler()
    {
        $this->_delegate->register(

            self::SERVICE_WP_ADMIN_HANDLER,
            'tubepress_plugins_wordpresscore_lib_impl_DefaultWpAdminHandler'
        );
    }

    private function _registerWpFunctionWrapper()
    {
        $this->_delegate->register(

            self::SERVICE_WP_FUNCTION_WRAPPER,
            'tubepress_plugins_wordpresscore_lib_impl_DefaultWordPressFunctionWrapper'
        );
    }


    /**
     * Gets a service.
     *
     * @param string $id              The service identifier
     * @param int    $invalidBehavior The behavior when the service does not exist
     *
     * @return object The associated service
     *
     * @throws InvalidArgumentException if the service is not defined
     */
    public final function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
    {
        return $this->_delegate->get($id, $invalidBehavior);
    }

    /**
     * Returns true if the given service is defined.
     *
     * @param string $id The service identifier
     *
     * @return boolean True if the service is defined, false otherwise
     */
    public final function has($id)
    {
        return $this->_delegate->has($id);
    }

    /**
     * Gets a parameter.
     *
     * @param string $name The parameter name
     *
     * @return mixed  The parameter value
     *
     * @throws InvalidArgumentException if the parameter is not defined
     */
    public final function getParameter($name)
    {
        return $this->_delegate->getParameter($name);
    }

    /**
     * Checks if a parameter exists.
     *
     * @param string $name The parameter name
     *
     * @return boolean The presence of parameter in container
     */
    public final function hasParameter($name)
    {
        return $this->_delegate->hasParameter($name);
    }
}

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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_bootstrap_Bootstrapper',
    'org_tubepress_api_exec_ExecutionContext',
    'org_tubepress_api_const_options_names_Advanced',
    'org_tubepress_api_const_options_names_GallerySource',
    'org_tubepress_api_const_plugin_EventName',
    'org_tubepress_api_const_plugin_FilterPoint',
    'org_tubepress_api_environment_Detector',
    'org_tubepress_api_filesystem_Explorer',
    'org_tubepress_api_ioc_IocService',
    'org_tubepress_api_options_StorageManager',
    'org_tubepress_api_plugin_PluginManager',
    'org_tubepress_api_theme_ThemeHandler',
    'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_impl_log_Log',
    'TubePress'
));

/**
 * Performs TubePress-wide initialization.
 */
class org_tubepress_impl_bootstrap_TubePressBootstrapper implements org_tubepress_api_bootstrap_Bootstrapper
{
    const LOG_PREFIX = 'TubePress Bootstrapper';

    private static $_alreadyBooted = false;

    /**
     * Performs TubePress-wide initialization.
     *
     * @return null
     */
    public function boot()
    {
        /* don't boot twice! */
        if (self::$_alreadyBooted) {

            return;
        }

        try {

            $this->_doBoot();

        } catch (Exception $e) {

            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Caught exception while booting: '.  $e->getMessage());
        }
    }

    private function _doBoot()
    {
        $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();
        $context     = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $envDetector = $ioc->get(org_tubepress_api_environment_Detector::_);
        $pm          = $ioc->get(org_tubepress_api_plugin_PluginManager::_);
        $sm  		 = $ioc->get(org_tubepress_api_options_StorageManager::_);

        /** Init the storage manager. */
        $sm->init();

        /* WordPress likes to keep control of the output */
        if ($envDetector->isWordPress()) {

            ob_start();
        }

        /* Turn on logging if we need to */
        org_tubepress_impl_log_Log::setEnabled($context->get(org_tubepress_api_const_options_names_Advanced::DEBUG_ON), $_GET);
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Booting!');

        /* load plugins */
        $this->loadSystemPlugins($ioc);
        $this->_loadUserPlugins($ioc);

        /* tell everyone we're booting */
        $pm->notifyListeners(org_tubepress_api_const_plugin_EventName::BOOT);

        /* remember that we booted. */
        self::$_alreadyBooted = true;
    }

    private function _loadUserPlugins(org_tubepress_api_ioc_IocService $ioc)
    {
        $pm         = $ioc->get(org_tubepress_api_plugin_PluginManager::_);
        $fe         = $ioc->get(org_tubepress_api_filesystem_Explorer::_);
        $th         = $ioc->get(org_tubepress_api_theme_ThemeHandler::_);
        $pluginPath = $th->getUserContentDirectory() . '/plugins';
        $pluginDirs = $fe->getDirectoriesInDirectory($pluginPath, self::LOG_PREFIX);

        foreach ($pluginDirs as $pluginDir) {

            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Examining potential plugin directory at %s', $pluginDir);

            $files = $fe->getFilenamesInDirectory($pluginDir, self::LOG_PREFIX);

            foreach ($files as $file) {

                if ('.php' == substr($file, -4) && is_readable($file)) {

                    org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Loading PHP file at <tt>%s</tt>', $file);

                    include_once $file;
                }
            }
        }
    }

    /**
     * Load system-defined plugins.
     *
     * @param org_tubepress_api_ioc_IocService $ioc The IOC container.
     *
     * @return void
     */
    protected function loadSystemPlugins(org_tubepress_api_ioc_IocService $ioc)
    {
        $pm = $ioc->get(org_tubepress_api_plugin_PluginManager::_);

        /* pre validation option setting */
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::OPTION_SET_PRE_VALIDATION,
                $ioc->get('org_tubepress_impl_plugin_filters_prevalidationoptionset_StringMagic'));
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::OPTION_SET_PRE_VALIDATION,
            $ioc->get('org_tubepress_impl_plugin_filters_prevalidationoptionset_YouTubePlaylistPlPrefixRemover'));

        /* embedded template filters */
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_EMBEDDED, $ioc->get('org_tubepress_impl_plugin_filters_embeddedtemplate_CoreVariables'));

        /* embedded HTML filters */
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::HTML_EMBEDDED, $ioc->get('org_tubepress_impl_plugin_filters_embeddedhtml_PlayerJavaScriptApi'));

        /* gallery HTML filters */
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::HTML_GALLERY, $ioc->get('org_tubepress_impl_plugin_filters_galleryhtml_GalleryJs'));

        /* gallery init js */
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::JAVASCRIPT_GALLERYINIT, $ioc->get('org_tubepress_impl_plugin_filters_galleryinitjs_GalleryInitJsBaseParams'));

        /* gallery template filters */
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_GALLERY, $ioc->get('org_tubepress_impl_plugin_filters_gallerytemplate_CoreVariables'));
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_GALLERY, $ioc->get('org_tubepress_impl_plugin_filters_gallerytemplate_EmbeddedPlayerName'));
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_GALLERY, $ioc->get('org_tubepress_impl_plugin_filters_gallerytemplate_Pagination'));
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_GALLERY, $ioc->get('org_tubepress_impl_plugin_filters_gallerytemplate_Player'));
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_GALLERY, $ioc->get('org_tubepress_impl_plugin_filters_gallerytemplate_VideoMeta'));

        /* player template filters */
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_PLAYER, $ioc->get('org_tubepress_impl_plugin_filters_playertemplate_CoreVariables'));

        /* provider result filters */
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::PROVIDER_RESULT, $ioc->get('org_tubepress_impl_plugin_filters_providerresult_ResultCountCapper'));
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::PROVIDER_RESULT, $ioc->get('org_tubepress_impl_plugin_filters_providerresult_VideoBlacklist'));
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::PROVIDER_RESULT, $ioc->get('org_tubepress_impl_plugin_filters_providerresult_PerPageSorter'));
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::PROVIDER_RESULT, $ioc->get('org_tubepress_impl_plugin_filters_providerresult_VideoPrepender'));

        /* search input template filter */
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_SEARCHINPUT, $ioc->get('org_tubepress_impl_plugin_filters_searchinputtemplate_CoreVariables'));

        /* single video template filters */
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_SINGLEVIDEO, $ioc->get('org_tubepress_impl_plugin_filters_singlevideotemplate_CoreVariables'));
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_SINGLEVIDEO, $ioc->get('org_tubepress_impl_plugin_filters_singlevideotemplate_VideoMeta'));

        /* external input filters */
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::VARIABLE_READ_FROM_EXTERNAL_INPUT, $ioc->get('org_tubepress_impl_plugin_filters_variablereadfromexternalinput_StringMagic'));

        $pm->registerListener(org_tubepress_api_const_plugin_EventName::BOOT, $ioc->get('org_tubepress_impl_plugin_listeners_WordPressBoot'));
        $pm->registerListener(org_tubepress_api_const_plugin_EventName::BOOT, $ioc->get('org_tubepress_impl_plugin_listeners_SkeletonExistsListener'));
    }
}

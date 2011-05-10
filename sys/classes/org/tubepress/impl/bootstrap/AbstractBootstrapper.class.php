<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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
    'org_tubepress_api_const_plugin_FilterPoint',
    'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_impl_log_Log',
));

/**
 * Performs TubePress-wide initialization.
 */
abstract class org_tubepress_impl_bootstrap_AbstractBootstrapper implements org_tubepress_api_bootstrap_Bootstrapper
{
    private static $_alreadyBooted = false;

    /**
     * Performs TubePress-wide initialization.
     * 
     * @return null
     */
    public function boot()
    {
        try {
            $this->_wrappedBoot();
        } catch (Exception $e) {
            org_tubepress_impl_log_Log::log($this->_getName(), 'Caught exception while booting: '.  $e->getMessage());
        }
    }
    
    private function _wrappedBoot()
    {
        /* don't boot twice! */
        if (self::$_alreadyBooted) {
            return;
        }

        $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();
        $tpom        = $ioc->get('org_tubepress_api_options_OptionsManager');
        $envDetector = $ioc->get('org_tubepress_api_environment_Detector');

        /* WordPress likes to keep control of the output */
        if ($envDetector->isWordPress()) {
            ob_start();
        }

        /* Turn on logging if we need to */
        org_tubepress_impl_log_Log::setEnabled($tpom->get(org_tubepress_api_const_options_names_Advanced::DEBUG_ON), $_GET);
        org_tubepress_impl_log_Log::log($this->_getName(), 'Booting!');

        /* load system plugins */
        $this->_loadSystemPlugins($ioc);
        
        /* continue booting process */
        $this->_doBoot();

        /* remember that we booted. */
        self::$_alreadyBooted = true;
    }

    /**
     * Get the name of this bootstrapper.
     *
     * @return void
     */
    protected abstract function _getName();

    /**
     * Perform boot procedure.
     *
     * @return void
     */
    protected abstract function _doBoot();
    
    private function _loadSystemPlugins(org_tubepress_api_ioc_IocService $ioc)
    {
        $pm      = $ioc->get('org_tubepress_api_plugin_PluginManager');

        /* gallery HTML filters */
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::HTML_GALLERY, $ioc->get('org_tubepress_impl_plugin_galleryhtml_GalleryJs'));

        /* gallery template filters */
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_GALLERY, $ioc->get('org_tubepress_impl_plugin_gallerytemplate_EmbeddedPlayerName'));
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_GALLERY, $ioc->get('org_tubepress_impl_plugin_gallerytemplate_Pagination'));
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_GALLERY, $ioc->get('org_tubepress_impl_plugin_gallerytemplate_Player'));
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_GALLERY, $ioc->get('org_tubepress_impl_plugin_gallerytemplate_VideoMeta'));
        
        /* provider result filters */
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::PROVIDER_RESULT, $ioc->get('org_tubepress_impl_plugin_providerresult_ResultCountCapper'));
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::PROVIDER_RESULT, $ioc->get('org_tubepress_impl_plugin_providerresult_VideoBlacklist'));
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::PROVIDER_RESULT, $ioc->get('org_tubepress_impl_plugin_providerresult_Shuffler'));
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::PROVIDER_RESULT, $ioc->get('org_tubepress_impl_plugin_providerresult_VideoPrepender'));
        
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_SINGLEVIDEO, $ioc->get('org_tubepress_impl_plugin_singlevideotemplate_EmbeddedSource'));
        $pm->registerFilter(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_SINGLEVIDEO, $ioc->get('org_tubepress_impl_plugin_singlevideotemplate_VideoMeta'));
    }
}

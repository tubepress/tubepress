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

function_exists('tubepress_load_classes')
|| require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_api_bootstrap_Bootstrapper',
    'org_tubepress_impl_log_Log',
    'org_tubepress_api_const_filters_ExecutionPoint'));

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

        /* register default filters */
        $fs      = $ioc->get('org_tubepress_api_filesystem_Explorer');
        $baseDir = $fs->getTubePressBaseInstallationPath() . '/sys/classes/org/tubepress/impl/filters/';

        $this->_loadFilters($baseDir . 'html', $fs, $ioc);
        $this->_loadFilters($baseDir . 'template', $fs, $ioc);
        $this->_loadFilters($baseDir . 'feedresult', $fs, $ioc);

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

    private function _loadFilters($directory, $fs, $ioc)
    {
        org_tubepress_impl_log_Log::log($this->_getName(), 'Loading TubePress filters from <tt>%s</tt>', $directory);

        /* get a list of the files in the directory */
        $pluginPaths = $fs->getFilenamesInDirectory($directory, $this->_getName());

        /* we want to provide the filter manager to the filters */
        $tubepressFilterManager = $ioc->get('org_tubepress_api_patterns_FilterManager');

        /* include the PHP files that we can read */
        foreach ($pluginPaths as $pluginPath) {

            if ('.php' == substr($pluginPath, -4) && is_readable($pluginPath)) {

                org_tubepress_impl_log_Log::log($this->_getName(), 'Loading TubePress filter at <tt>%s</tt>', $pluginPath);

                include_once $pluginPath;

            } else {

                org_tubepress_impl_log_Log::log($this->_getName(), 'Ignoring non-filter file at <tt>%s</tt>', $pluginPath);

            }
        }
    }

}

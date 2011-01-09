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
    'org_tubepress_impl_util_FilesystemUtils',
    'org_tubepress_impl_log_Log'));

/**
 * Performs TubePress-wide initialization.
 */
abstract class org_tubepress_impl_bootstrap_AbstractBootstrapper implements org_tubepress_api_bootstrap_Bootstrapper
{
    private static $_alreadyBooted = false;

    /**
     * Performs TubePress-wide initialization and preflight checks.
     */
    public function boot()
    {
        /* don't boot twice! */
        if (self::$_alreadyBooted) {
            return;
        }

        org_tubepress_impl_log_Log::log($this->_getName(), 'Booting!');

        /* load default filters */
        $ioc       = org_tubepress_impl_ioc_IocContainer::getInstance();
        $fs        = $ioc->get('org_tubepress_api_filesystem_Explorer');
        $baseDir = $fs->getTubePressBaseInstallationPath();
        
        $this->_loadFilters($baseDir . '/default_filters', $fs, $ioc);

        /* continue booting process */
        $this->_doBoot();

        /* remember that we booted. */
        self::$_alreadyBooted = true;
    }

    protected abstract function _getName();
    
    protected abstract function _doBoot();

    private function _loadFilters($directory, $fs, $ioc)
    {
        org_tubepress_impl_log_Log::log($this->_getName(), 'Loading TubePress plugins from <tt>%s</tt>', $directory);

        /* get a list of the files in the directory */
        $pluginPaths = $fs->getFilenamesInDirectory($directory, $this->_getName());

        /* we want to provide the filter manager to the filters */
        $tubepressFilterManager = $ioc->get('org_tubepress_api_patterns_FilterManager');
        
        /* include the PHP files that we can read */
        foreach ($pluginPaths as $pluginPath) {

            if ('.php' == substr($pluginPath, -4) && is_readable($pluginPath)) {
                 
                org_tubepress_impl_log_Log::log($this->_getName(), 'Loading TubePress filter at <tt>%s</tt>', $pluginPath);
                
                include $pluginPath;

            } else {

                org_tubepress_impl_log_Log::log($this->_getName(), 'Ignoring non-filter file at <tt>%s</tt>', $pluginPath);

            }
        }
    }

}

<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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
    || require dirname(__FILE__) . '/../../../tubepress_classloader.php';
tubepress_load_classes(array('net_sourceforge_phpcrafty_ComponentFactory',
    'org_tubepress_env_EnvironmentDetector'));

/**
 * Dependency injector for TubePress that uses phpcrafty
 */
class org_tubepress_ioc_IocDelegateUtils
{
    public static function getDelegate($providerToBeanNameArray, $defaultDelegateBeanName)
    {
        $ioc      = org_tubepress_ioc_IocContainer::getInstance();
        $provider = self::getProvider($ioc);
        
        if (array_key_exists($provider, $providerToBeanNameArray)) {
            org_tubepress_log_Log::log('IOC Delegate Utils', 'Found custom delegate: %s', $providerToBeanNameArray[$provider]);
            return $ioc->get($providerToBeanNameArray[$provider]);
        }
        
        org_tubepress_log_Log::log('IOC Delegate Utils', 'Falling back to default delegate: %s', $defaultDelegateBeanName);
        return $ioc->get($defaultDelegateBeanName);
    }
    
    private static function getProvider(org_tubepress_ioc_IocService $ioc)
    {
        $tpom     = $ioc->get('org_tubepress_options_manager_OptionsManager');
        $provider = $ioc->get('org_tubepress_api_provider_Provider');
        
        return $provider->calculateCurrentVideoProvider($tpom);
    }
}

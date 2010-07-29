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
tubepress_load_classes(array('net_sourceforge_phpcrafty_ComponentFactory'));

/**
 * Dependency injector for TubePress that uses phpcrafty
 */
class org_tubepress_ioc_IocDelegateUtils
{
    public static function getDelegate(org_tubepress_ioc_IocService $ioc, $providerToBeanNameArray, $defaultDelegateBeanName)
    {
        $provider = self::getProvider($ioc);
        if (array_key_exists($provider, $providerToBeanNameArray)) {
            return $ioc->get($providerToBeanNameArray[$provider]);
        }
        return $ioc->get($defaultDelegateBeanName);
    }
    
    private static function getProvider(org_tubepress_ioc_IocService $ioc)
    {
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        return org_tubepress_video_feed_provider_Provider::calculateCurrentVideoProvider($tpom);
    }
}
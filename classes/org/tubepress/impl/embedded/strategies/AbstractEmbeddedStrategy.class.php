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

tubepress_load_classes(array('org_tubepress_api_patterns_Strategy',
    'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_api_ioc_IocService',
    'org_tubepress_api_options_OptionsManager'));

/**
 * Base class for embedded strategies.
 */
class org_tubepress_impl_embedded_strategies_AbstractEmbeddedStrategy implements org_tubepress_api_patterns_Strategy
{
    private $_ioc;
    private $_tpom;
    
    public function start()
    {
        $this->_ioc  = org_tubepress_impl_ioc_IocContainer::getInstance();
        $this->_tpom = $ioc->get('org_tubepress_api_options_OptionsManager');
    }

    public function stop()
    {
        unset($this->_ioc);
        unset($this->_tpom);
    }

    public function canHandle($providerName, $videoId)
    {
        return $this->_canHandle($providerName, $videoId, $this->_ioc, $this->_tpom);
    }

    public function execute($providerName, $videoId)
    {    
        return $this->_execute($providerName, $videoId, $this->_ioc, $this->_tpom);
    }
    
    protected abstract function _canHandle($providerName, $videoId, org_tubepress_api_ioc_IocService $ioc, org_tubepress_api_options_OptionsManager $tpom);

    protected abstract function _execute($providerName, $videoId, org_tubepress_api_ioc_IocService $ioc, org_tubepress_api_options_OptionsManager $tpom);
}

?>

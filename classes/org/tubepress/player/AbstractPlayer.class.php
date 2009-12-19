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
    || require(dirname(__FILE__) . '/../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_player_Player',
    'org_tubepress_ioc_ContainerAware',
    'org_tubepress_options_manager_OptionsManager',
    'org_tubepress_template_Template',
    'org_tubepress_ioc_IocService'));

/**
 * A TubePress "player", such as lightWindow, GreyBox, popup window, etc
 */
abstract class org_tubepress_player_AbstractPlayer implements org_tubepress_player_Player, org_tubepress_ioc_ContainerAware
{   
    private $_optionsManager;
    private $_iocContainer;
    private $_template;
    
    public function setContainer(org_tubepress_ioc_IocService $container) { $this->_iocContainer = $container; }
    public function setOptionsManager(org_tubepress_options_manager_OptionsManager $optionsManager) { $this->_optionsManager = $optionsManager; }
    public function setTemplate(org_tubepress_template_Template $template) { $this->_template = $template; }
    
    protected function getContainer() { return $this->_iocContainer; }
    protected function getOptionsManager() { return $this->_optionsManager; }
    protected function getTemplate() { return $this->_template; }
}


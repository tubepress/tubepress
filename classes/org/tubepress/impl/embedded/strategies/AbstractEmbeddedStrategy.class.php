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
    || require dirname(__FILE__) . '/../../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_api_patterns_Strategy',
    'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_api_const_options_names_Embedded',
    'org_tubepress_impl_embedded_EmbeddedPlayerUtils',
    'org_tubepress_api_options_OptionsManager'));

/**
 * Base class for embedded strategies.
 */
abstract class org_tubepress_impl_embedded_strategies_AbstractEmbeddedStrategy implements org_tubepress_api_patterns_Strategy
{
    private $_ioc;
    private $_tpom;
    
    /**
     * Called *before* canHandle() and execute() to allow the strategy
     *  to initialize itself.
     */
    public function start()
    {
        $this->_ioc  = org_tubepress_impl_ioc_IocContainer::getInstance();
        $this->_tpom = $this->_ioc->get('org_tubepress_api_options_OptionsManager');
    }

    /**
     * Called *after* canHandle() and execute() to allow the strategy
     *  to tear itself down.
     */
    public function stop()
    {
        unset($this->_ioc);
        unset($this->_tpom);
    }

    /**
     * Returns true if this strategy is able to handle
     *  the request.
     */
    public function canHandle()
    {
        /* grab the arguments */
        $args = func_get_args();
        
        self::_checkArgs($args);
        
        $providerName = $args[0];
        $videoId      = $args[1];
        
        return $this->_canHandle($providerName, $videoId, $this->_ioc, $this->_tpom);
    }

    /**
     * Execute the strategy.
     */
    public function execute()
    {    
        global $tubepress_base_url;
       
        $args         = func_get_args();
        self::_checkArgs($args);
        
        $providerName = $args[0];
        $videoId      = $args[1];
        $theme        = $this->_ioc->get('org_tubepress_api_theme_ThemeHandler');
        $template     = $theme->getTemplateInstance($this->_getTemplatePath($providerName, $videoId, $this->_ioc, $this->_tpom));

        $fullscreen      = $this->_tpom->get(org_tubepress_api_const_options_names_Embedded::FULLSCREEN);
        $playerColor     = org_tubepress_impl_embedded_EmbeddedPlayerUtils::getSafeColorValue($this->_tpom->get(org_tubepress_api_const_options_names_Embedded::PLAYER_COLOR), '999999');
        $playerHighlight = org_tubepress_impl_embedded_EmbeddedPlayerUtils::getSafeColorValue($this->_tpom->get(org_tubepress_api_const_options_names_Embedded::PLAYER_HIGHLIGHT), 'FFFFFF');
        $autoPlay        = $this->_tpom->get(org_tubepress_api_const_options_names_Embedded::AUTOPLAY);

        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_DATA_URL, $this->_getEmbeddedDataUrl($providerName, $videoId, $this->_ioc, $this->_tpom));
        $template->setVariable(org_tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL, $tubepress_base_url);
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_AUTOSTART, org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToString($autoPlay));
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_WIDTH, $this->_tpom->get(org_tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH));
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_HEIGHT, $this->_tpom->get(org_tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT));
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_COLOR_PRIMARY, $playerColor);
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_COLOR_HIGHLIGHT, $playerHighlight);
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_FULLSCREEN, org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToString($fullscreen));

        return $template->toString();
    }
    
    protected abstract function _canHandle($providerName, $videoId, org_tubepress_api_ioc_IocService $ioc, org_tubepress_api_options_OptionsManager $tpom);

    protected abstract function _getTemplatePath($providerName, $videoId, org_tubepress_api_ioc_IocService $ioc, org_tubepress_api_options_OptionsManager $tpom);

    protected abstract function _getEmbeddedDataUrl($providerName, $videoId, org_tubepress_api_ioc_IocService $ioc, org_tubepress_api_options_OptionsManager $tpom);

    private static function _checkArgs($args)
    {
        /* a little sanity checking */
        if (count($args) !== 2) {
            throw new Exception(sprintf("Wrong argument count sent to canHandle(). Expects 2, you sent %d", count($args)));
        }
    }
}

?>

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
 * Hooks JW Player into TubePress.
 */
class tubepress_plugins_jwplayer_JwPlayer
{
    private static $_regexColor = '/^([0-9a-f]{1,2}){3}$/i';

    public static function init()
    {
        $odr                        = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionDescriptorReference();
        $eventDispatcher            = tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher();

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_BACK);
        $option->setDefaultValue('FFFFFF');
        $option->setLabel('Background color');                         //>(translatable)<                                                                                                                                                                                                                                 //>(translatable)<
        $option->setDescription('Default is FFFFFF');                  //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_FRONT);
        $option->setDefaultValue('000000');
        $option->setLabel('Front color');                              //>(translatable)<                                                                                                                                                                                                                                 //>(translatable)<
        $option->setDescription('Default is 000000');                  //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT);
        $option->setDefaultValue('000000');
        $option->setLabel('Light color');                              //>(translatable)<                                                                                                                                                                                                                                 //>(translatable)<
        $option->setDescription('Default is 000000');                  //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN);
        $option->setDefaultValue('000000');
        $option->setLabel('Screen color');                             //>(translatable)<                                                                                                                                                                                                                                 //>(translatable)<
        $option->setDescription('Default is 000000');                  //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $odr->registerOptionDescriptor($option);

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::EMBEDDED_TEMPLATE_CONSTRUCTION,

            array(new tubepress_plugins_jwplayer_impl_filters_embeddedtemplate_JwPlayerTemplateVars(), 'onEmbeddedTemplate')
        );
    }
}

tubepress_plugins_jwplayer_JwPlayer::init();
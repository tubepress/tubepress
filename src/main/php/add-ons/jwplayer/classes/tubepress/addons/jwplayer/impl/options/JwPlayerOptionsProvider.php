<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Hooks JW Player into TubePress.
 */
class tubepress_addons_jwplayer_impl_options_JwPlayerOptionsProvider implements tubepress_spi_options_PluggableOptionDescriptorProvider
{
    private static $_regexColor = '/^([0-9a-f]{1,2}){3}$/i';

    /**
     * Fetch all the option descriptors from this provider.
     *
     * @return tubepress_spi_options_OptionDescriptor[]
     */
    function getOptionDescriptors()
    {
        $toReturn = array();
            
        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK);
        $option->setDefaultValue('FFFFFF');
        $option->setLabel('Background color');                         //>(translatable)<                                                                                                                                                                                                                                 //>(translatable)<
        $option->setDescription(sprintf('Default is %s', "FFFFFF"));   //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_FRONT);
        $option->setDefaultValue('000000');
        $option->setLabel('Front color');                              //>(translatable)<                                                                                                                                                                                                                                 //>(translatable)<
        $option->setDescription(sprintf('Default is %s', "000000"));   //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT);
        $option->setDefaultValue('000000');
        $option->setLabel('Light color');                              //>(translatable)<                                                                                                                                                                                                                                 //>(translatable)<
        $option->setDescription(sprintf('Default is %s', "000000"));   //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN);
        $option->setDefaultValue('000000');
        $option->setLabel('Screen color');                             //>(translatable)<                                                                                                                                                                                                                                 //>(translatable)<
        $option->setDescription(sprintf('Default is %s', "000000"));   //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $toReturn[] = $option;

        return $toReturn;
    }
}
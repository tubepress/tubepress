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
 * Embedded player command for EmbedPlus
 */
class tubepress_impl_embedded_commands_EmbedPlusCommand extends tubepress_impl_embedded_commands_AbstractEmbeddedCommand
{
    protected function _canHandle($providerName, $videoId, tubepress_spi_context_ExecutionContext $context)
    {
        return $providerName === tubepress_spi_provider_Provider::YOUTUBE
            && $context->get(tubepress_api_const_options_names_Embedded::PLAYER_IMPL) === tubepress_api_const_options_values_PlayerImplementationValue::EMBEDPLUS;
    }

    protected function _getTemplatePath($providerName, $videoId, tubepress_spi_context_ExecutionContext $context)
    {
        return 'embedded_flash/embedplus.tpl.php';
    }

    protected function _getEmbeddedDataUrl($providerName, $videoId, tubepress_spi_context_ExecutionContext $context)
    {    
        return new ehough_curly_Url(sprintf('http://www.youtube.com/embed/%s', $videoId));
    }
    
    protected function _getEmbeddedImplName()
    {
        return tubepress_api_const_options_values_PlayerImplementationValue::EMBEDPLUS;
    }
}

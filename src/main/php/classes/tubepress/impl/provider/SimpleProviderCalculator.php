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
 * Calculates video provider in use.
 */
class tubepress_impl_provider_SimpleProviderCalculator implements tubepress_spi_provider_ProviderCalculator
{
    /**
     * Determine the current video provider.
     *
     * @return string 'youtube', 'vimeo', or 'directory'
     */
    public final function calculateCurrentVideoProvider()
    {
        $executionContext = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();

        $video = $executionContext->get(tubepress_api_const_options_names_Output::VIDEO);

        /* requested a single video... */
        if ($video != '') {

            return $this->calculateProviderOfVideoId($video);
        }

        /* calculate based on gallery content */
        $currentMode = $executionContext->get(tubepress_api_const_options_names_Output::GALLERY_SOURCE);

        if (strpos($currentMode, 'vimeo') === 0) {

            return tubepress_spi_provider_Provider::VIMEO;
        }

        return tubepress_spi_provider_Provider::YOUTUBE;
    }

    public final function calculateProviderOfVideoId($videoId)
    {
        if (is_numeric($videoId) === true) {

            return tubepress_spi_provider_Provider::VIMEO;
        }

        return tubepress_spi_provider_Provider::YOUTUBE;
    }
}

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
 * Applies the embedded service name to the template.
 */
class tubepress_plugins_core_impl_filters_gallerytemplate_EmbeddedPlayerName
{
    public function onGalleryTemplate(tubepress_api_event_TubePressEvent $event)
    {
        $template     = $event->getSubject();
        $providerName = $event->getArgument('providerName');

        $template->setVariable(tubepress_api_const_template_Variable::EMBEDDED_IMPL_NAME, self::_getEmbeddedServiceName($providerName));
    }

    private static function _getEmbeddedServiceName($providerName)
    {
        $context = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $stored  = $context->get(tubepress_api_const_options_names_Embedded::PLAYER_IMPL);

        $longTailWithYouTube = $stored === 'longtail'
            && $providerName === 'youtube';

        $embedPlusWithYouTube = $stored === 'embedplus'
            && $providerName === 'youtube';

        if ($longTailWithYouTube || $embedPlusWithYouTube) {

            return $stored;
        }

        return $providerName;
    }
}

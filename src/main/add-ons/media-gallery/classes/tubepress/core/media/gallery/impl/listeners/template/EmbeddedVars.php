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
 * Applies the embedded service name to the template.
 */
class tubepress_core_media_gallery_impl_listeners_template_EmbeddedVars
{
    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_core_options_api_ContextInterface $context)
    {
        $this->_context = $context;
    }

    public function onGalleryTemplate(tubepress_core_event_api_EventInterface $event)
    {
        $template = $event->getSubject();
        $page     = $event->getArgument('page');

        $template->setVariable(tubepress_core_template_api_const_VariableNames::EMBEDDED_IMPL_NAME, $this->_getEmbeddedServiceName($page));
    }

    private function _getEmbeddedServiceName(tubepress_core_provider_api_Page $page)
    {
        $stored      = $this->_context->get(tubepress_core_embedded_api_Constants::OPTION_PLAYER_IMPL);
        $videoArray  = $page->getItems();

        /**
         * @var $randomVideo tubepress_core_provider_api_MediaItem
         */
        $randomVideo = $videoArray[array_rand($videoArray)];
        $provider    = $randomVideo->getAttribute(tubepress_core_provider_api_Constants::ATTRIBUTE_PROVIDER_NAME);

        $longTailWithYouTube = $stored === 'longtail' && $provider === 'youtube';

        $embedPlusWithYouTube = $stored === 'embedplus' && $provider === 'youtube';

        if ($longTailWithYouTube || $embedPlusWithYouTube) {

            return $stored;
        }

        return $provider;
    }
}

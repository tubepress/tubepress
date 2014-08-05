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
 *
 */
class tubepress_youtube2_impl_listeners_embedded_EmbeddedListener
{
    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_platform_api_util_LangUtilsInterface
     */
    private $_langUtils;

    /**
     * @var tubepress_platform_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    public function __construct(tubepress_app_api_options_ContextInterface     $context,
                                tubepress_platform_api_util_LangUtilsInterface $langUtils,
                                tubepress_platform_api_url_UrlFactoryInterface $urlFactory)
    {
        $this->_context    = $context;
        $this->_langUtils  = $langUtils;
        $this->_urlFactory = $urlFactory;
    }

    public function onEmbeddedTemplateSelection(tubepress_lib_api_event_EventInterface $event)
    {
        if (!$this->_handlesEvent($event)) {

            return;
        }

        $event->setSubject('embedded/youtube');
    }

    public function onEmbeddedTemplatePreRender(tubepress_lib_api_event_EventInterface $event)
    {
        if (!$this->_handlesEvent($event)) {

            return;
        }

        $existingArgs = $event->getSubject();
        $mediaItem    = $event->getArgument('mediaItem');

        $existingArgs[tubepress_app_api_template_VariableNames::EMBEDDED_DATA_URL] = $this->_getDataUrl($mediaItem);

        $event->setSubject($existingArgs);
    }

    private function _getDataUrl(tubepress_app_api_media_MediaItem $mediaItem)
    {
        $link       = $this->_urlFactory->fromString('https://www.youtube.com/embed/' . $mediaItem->getId());
        $embedQuery = $link->getQuery();
        $url        = $this->_urlFactory->fromCurrent();
        $origin     = $url->getScheme() . '://' . $url->getHost();

        $autoPlay        = $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_AUTOPLAY);
        $loop            = $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_LOOP);
        $showInfo        = $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_SHOW_INFO);
        $autoHide        = $this->_context->get(tubepress_youtube2_api_Constants::OPTION_AUTOHIDE);
        $fullscreen      = $this->_context->get(tubepress_youtube2_api_Constants::OPTION_FULLSCREEN);
        $modestBranding  = $this->_context->get(tubepress_youtube2_api_Constants::OPTION_MODEST_BRANDING);
        $showRelated     = $this->_context->get(tubepress_youtube2_api_Constants::OPTION_SHOW_RELATED);

        $embedQuery->set('autohide',       $this->_getAutoHideValue($autoHide));
        $embedQuery->set('autoplay',       $this->_langUtils->booleanToStringOneOrZero($autoPlay));
        $embedQuery->set('enablejsapi',    '1');
        $embedQuery->set('fs',             $this->_langUtils->booleanToStringOneOrZero($fullscreen));
        $embedQuery->set('modestbranding', $this->_langUtils->booleanToStringOneOrZero($modestBranding));
        $embedQuery->set('origin',         $origin);
        $embedQuery->set('rel',            $this->_langUtils->booleanToStringOneOrZero($showRelated));
        $embedQuery->set('showinfo',       $this->_langUtils->booleanToStringOneOrZero($showInfo));
        $embedQuery->set('wmode',          'opaque');

        if ($loop) {

            $embedQuery->set('loop', $this->_langUtils->booleanToStringOneOrZero($loop));
            $embedQuery->set('playlist', $mediaItem->getId());
        }

        return $link;
    }

    private function _getAutoHideValue($autoHide)
    {
        switch ($autoHide) {

            case tubepress_youtube2_api_Constants::AUTOHIDE_HIDE_BOTH:

                return 1;

            case tubepress_youtube2_api_Constants::AUTOHIDE_SHOW_BOTH:

                return 0;

            default:

                return 2;
        }
    }

    private function _handlesEvent(tubepress_lib_api_event_EventInterface $event)
    {
        /**
         * @var $mediaItem tubepress_app_api_media_MediaItem
         */
        $mediaItem = $event->getArgument('mediaItem');

        /**
         * @var $provider tubepress_app_api_media_MediaProviderInterface
         */
        $provider = $mediaItem->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_PROVIDER);

        return $provider->getName() === 'youtube';
    }
}
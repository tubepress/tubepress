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
class tubepress_app_impl_listeners_gallery_template_CoreGalleryTemplateListener
{
    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_app_api_options_ContextInterface $context)
    {
        $this->_context = $context;
    }

    public function onGalleryTemplatePreRender(tubepress_lib_api_event_EventInterface $event)
    {
        /**
         * @var $existingArgs array
         */
        $existingArgs = $event->getSubject();

        $this->_setItemArrayAndGalleryId($event, $existingArgs);
        $this->_setThumbnailSizes($existingArgs);

        $event->setSubject($existingArgs);
    }

    private function _setItemArrayAndGalleryId(tubepress_lib_api_event_EventInterface $event,
                                               array                                  &$templateVars)
    {
        $mediaItemPage = $event->getArgument('page');
        $galleryId     = $this->_context->get(tubepress_app_api_options_Names::HTML_GALLERY_ID);

        $templateVars[tubepress_app_api_template_VariableNames::MEDIA_PAGE]     = $mediaItemPage;
        $templateVars[tubepress_app_api_template_VariableNames::HTML_WIDGET_ID] = $galleryId;
    }

    private function _setThumbnailSizes(array &$templateVars)
    {
        $thumbWidth  = $this->_context->get(tubepress_app_api_options_Names::GALLERY_THUMB_WIDTH);
        $thumbHeight = $this->_context->get(tubepress_app_api_options_Names::GALLERY_THUMB_HEIGHT);

        $templateVars[tubepress_app_api_template_VariableNames::GALLERY_THUMBNAIL_WIDTH_PX]  = $thumbWidth;
        $templateVars[tubepress_app_api_template_VariableNames::GALLERY_THUMBNAIL_HEIGHT_PX] = $thumbHeight;
    }
}
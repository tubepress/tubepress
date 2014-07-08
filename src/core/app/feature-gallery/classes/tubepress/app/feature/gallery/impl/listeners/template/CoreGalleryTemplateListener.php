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
class tubepress_app_feature_gallery_impl_listeners_template_CoreGalleryTemplateListener
{
    /**
     * @var tubepress_lib_translation_api_TranslatorInterface
     */
    private $_translator;

    /**
     * @var tubepress_app_options_api_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_app_options_api_ContextInterface        $context,
                                tubepress_lib_translation_api_TranslatorInterface $translator)
    {
        $this->_context    = $context;
        $this->_translator = $translator;
    }

    public function onGalleryTemplate(tubepress_lib_event_api_EventInterface $event)
    {
        /**
         * @var $template tubepress_lib_template_api_TemplateInterface
         */
        $template = $event->getSubject();

        $this->_setItemArrayAndGalleryId($event, $template);
        $this->_setThumbnailSizes($template);
        $this->_setTranslator($template);
    }

    private function _setTranslator(tubepress_lib_template_api_TemplateInterface $template)
    {
        $template->setVariable('translator', $this->_translator);
    }

    private function _setItemArrayAndGalleryId(tubepress_lib_event_api_EventInterface       $event,
                                               tubepress_lib_template_api_TemplateInterface $template)
    {
        $mediaItemPage = $event->getArgument('page');
        $mediaItems    = $mediaItemPage->getItems();
        $galleryId     = $this->_context->get(tubepress_app_html_api_Constants::OPTION_GALLERY_ID);

        $template->setVariable(tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_MEDIA_ITEM_ARRAY, $mediaItems);
        $template->setVariable(tubepress_app_html_api_Constants::TEMPLATE_VAR_GALLERY_ID, $galleryId);
    }

    private function _setThumbnailSizes(tubepress_lib_template_api_TemplateInterface $template)
    {
        $thumbWidth  = $this->_context->get(tubepress_app_feature_gallery_api_Constants::OPTION_THUMB_WIDTH);
        $thumbHeight = $this->_context->get(tubepress_app_feature_gallery_api_Constants::OPTION_THUMB_HEIGHT);

        $template->setVariable(tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_WIDTH, $thumbWidth);
        $template->setVariable(tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_HEIGHT, $thumbHeight);
    }
}
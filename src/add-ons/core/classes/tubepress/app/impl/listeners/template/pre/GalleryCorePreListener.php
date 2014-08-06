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
class tubepress_app_impl_listeners_template_pre_GalleryCorePreListener
{
    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_lib_api_template_TemplatingInterface
     */
    private $_templating;

    private static $_ajaxPlayerTemplateMap = array(

        tubepress_app_api_options_AcceptableValues::PLAYER_LOC_JQMODAL   => 'jqmodal/ajax',
        tubepress_app_api_options_AcceptableValues::PLAYER_LOC_NORMAL    => 'normal/ajax',
        tubepress_app_api_options_AcceptableValues::PLAYER_LOC_POPUP     => 'popup/ajax',
        tubepress_app_api_options_AcceptableValues::PLAYER_LOC_SHADOWBOX => 'shadowbox/ajax',
    );

    private static $_staticPlayerTemplateMap = array(

        tubepress_app_api_options_AcceptableValues::PLAYER_LOC_JQMODAL   => 'jqmodal/static',
        tubepress_app_api_options_AcceptableValues::PLAYER_LOC_NORMAL    => 'normal/static',
        tubepress_app_api_options_AcceptableValues::PLAYER_LOC_POPUP     => 'popup/static',
        tubepress_app_api_options_AcceptableValues::PLAYER_LOC_SHADOWBOX => 'shadowbox/static',
    );

    public function __construct(tubepress_app_api_options_ContextInterface     $context,
                                tubepress_lib_api_template_TemplatingInterface $templating)
    {
        $this->_context    = $context;
        $this->_templating = $templating;
    }

    public function onGalleryTemplatePreRender(tubepress_lib_api_event_EventInterface $event)
    {
        /**
         * @var $existingArgs array
         */
        $existingArgs = $event->getSubject();

        $this->_setSimpleVarsFromContext($existingArgs);
        $this->_setStaticPlayerVars($existingArgs);

        $event->setSubject($existingArgs);
    }

    public function onStaticPlayerTemplateSelection(tubepress_lib_api_event_EventInterface $event)
    {
        $requestedLocation = $this->_context->get(tubepress_app_api_options_Names::PLAYER_LOCATION);

        if (isset(self::$_staticPlayerTemplateMap[$requestedLocation])) {

            $event->setSubject('gallery/players/' . self::$_staticPlayerTemplateMap[$requestedLocation]);
        }
    }

    public function onAjaxPlayerTemplateSelection(tubepress_lib_api_event_EventInterface $event)
    {
        $requestedLocation = $this->_context->get(tubepress_app_api_options_Names::PLAYER_LOCATION);

        if (isset(self::$_ajaxPlayerTemplateMap[$requestedLocation])) {

            $event->setSubject('gallery/players/' . self::$_ajaxPlayerTemplateMap[$requestedLocation]);
        }
    }

    private function _setStaticPlayerVars(array &$templateVars)
    {
        /**
         * @var $mediaPage tubepress_app_api_media_MediaPage
         */
        $mediaPage = $templateVars['mediaPage'];
        $items     = $mediaPage->getItems();

        if (count($items) === 0) {

            return;
        }

        $playerTemplateVars = array(
            tubepress_app_api_template_VariableNames::MEDIA_ITEM => $items[0]
        );

        if (isset($templateVars[tubepress_app_api_template_VariableNames::EMBEDDED_SOURCE])) {

            $playerTemplateVars[tubepress_app_api_template_VariableNames::EMBEDDED_SOURCE] =
                $templateVars[tubepress_app_api_template_VariableNames::EMBEDDED_SOURCE];
        }

        try {

            $playerHtml = $this->_templating->renderTemplate('gallery/player/static', $playerTemplateVars);

            $templateVars[tubepress_app_api_template_VariableNames::PLAYER_HTML] = $playerHtml;

        } catch (RuntimeException $e) {

            if ($e->getMessage() !== 'No engine is able to work with the template "gallery/player/static".') {

                throw $e;
            }
        }
    }

    private function _setSimpleVarsFromContext(array &$templateVars)
    {
        $galleryId   = $this->_context->get(tubepress_app_api_options_Names::HTML_GALLERY_ID);
        $thumbWidth  = $this->_context->get(tubepress_app_api_options_Names::GALLERY_THUMB_WIDTH);
        $thumbHeight = $this->_context->get(tubepress_app_api_options_Names::GALLERY_THUMB_HEIGHT);

        $templateVars[tubepress_app_api_template_VariableNames::HTML_WIDGET_ID]              = $galleryId;
        $templateVars[tubepress_app_api_template_VariableNames::GALLERY_THUMBNAIL_WIDTH_PX]  = $thumbWidth;
        $templateVars[tubepress_app_api_template_VariableNames::GALLERY_THUMBNAIL_HEIGHT_PX] = $thumbHeight;
    }
}
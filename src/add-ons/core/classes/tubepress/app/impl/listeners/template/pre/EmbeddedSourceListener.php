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
class tubepress_app_impl_listeners_template_pre_EmbeddedSourceListener
{
    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_lib_api_template_TemplatingInterface
     */
    private $_templating;

    public function __construct(tubepress_app_api_options_ContextInterface     $context,
                                tubepress_lib_api_template_TemplatingInterface $templating)
    {
        $this->_context    = $context;
        $this->_templating = $templating;
    }

    public function applyEmbeddedSource(tubepress_lib_api_event_EventInterface $event)
    {
        /**
         * @var $existingTemplateVars array
         */
        $existingTemplateVars = $event->getSubject();

        /**
         * @var $mediaItem tubepress_app_api_media_MediaItem
         */
        $mediaItem    = $existingTemplateVars['mediaItem'];
        $embedWidth   = $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_WIDTH);
        $embedHeight  = $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_HEIGHT);
        $embeddedHtml = $this->_templating->renderTemplate('single/embedded', array(

            tubepress_app_api_template_VariableNames::MEDIA_ITEM         => $mediaItem,
            tubepress_app_api_template_VariableNames::EMBEDDED_WIDTH_PX  => $embedWidth,
            tubepress_app_api_template_VariableNames::EMBEDDED_HEIGHT_PX => $embedHeight,
        ));

        $existingTemplateVars[tubepress_app_api_template_VariableNames::EMBEDDED_SOURCE] = $embeddedHtml;

        $event->setSubject($existingTemplateVars);
    }
}
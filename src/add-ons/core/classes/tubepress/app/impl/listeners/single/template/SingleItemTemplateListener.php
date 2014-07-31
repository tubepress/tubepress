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
 * Adds some core variables to the single media item template.
 */
class tubepress_app_impl_listeners_single_template_SingleItemTemplateListener
{
    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_lib_api_translation_TranslatorInterface
     */
    private $_translator;

    public function __construct(tubepress_app_api_options_ContextInterface        $context,
                                tubepress_lib_api_translation_TranslatorInterface $translator)
    {
        $this->_context    = $context;
        $this->_translator = $translator;
    }

    public function onSingleTemplatePreRender(tubepress_lib_api_event_EventInterface $event)
    {
        $existingVars = $event->getSubject();

        if (!$event->hasArgument('item')) {

            return;
        }

        $mediaItem = $event->getArgument('item');
        $width     = $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_WIDTH);

        $newVars = array(
            tubepress_app_api_template_VariableNames::EMBEDDED_WIDTH_PX => $width,
            tubepress_app_api_template_VariableNames::MEDIA_ITEM        => $mediaItem,
        );

        $existingVars = array_merge($existingVars, $newVars);

        $event->setSubject($existingVars);
    }
}
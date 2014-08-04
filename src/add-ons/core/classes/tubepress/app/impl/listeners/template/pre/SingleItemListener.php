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
class tubepress_app_impl_listeners_template_pre_SingleItemListener
{
    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_app_api_options_ContextInterface $context)
    {
        $this->_context    = $context;
    }

    public function onSingleTemplatePreRender(tubepress_lib_api_event_EventInterface $event)
    {
        $existingVars = $event->getSubject();

        if (!$event->hasArgument('mediaItem')) {

            return;
        }

        $mediaItem = $event->getArgument('mediaItem');
        $width     = $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_WIDTH);

        $newVars = array(
            tubepress_app_api_template_VariableNames::EMBEDDED_WIDTH_PX => $width,
            tubepress_app_api_template_VariableNames::MEDIA_ITEM        => $mediaItem,
        );

        $existingVars = array_merge($existingVars, $newVars);

        $event->setSubject($existingVars);
    }
}
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
 * Generates HTML for the embedded media player.
 */
class tubepress_app_impl_listeners_embedded_SourceListener
{
    /**
     * @var tubepress_lib_api_template_TemplatingInterface
     */
    private $_templating;

    public function __construct(tubepress_lib_api_template_TemplatingInterface $templating)
    {
        $this->_templating = $templating;
    }

    public function addEmbeddedHtmlToTemplate(tubepress_lib_api_event_EventInterface $event)
    {
        $existingTemplateVars = $event->getSubject();
        $embeddedHtml         = $this->_templating->renderTemplate('embed', $existingTemplateVars);
        $toSet                = array_merge($existingTemplateVars, array(
            tubepress_app_api_template_VariableNames::EMBEDDED_SOURCE => $embeddedHtml
        ));
        $event->setSubject($toSet);
    }
}
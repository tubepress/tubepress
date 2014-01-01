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
 * Handles applying video meta info to the gallery template.
 */
class tubepress_addons_core_impl_listeners_template_ThumbGalleryVideoMeta
{
    public function onGalleryTemplate(tubepress_api_event_EventInterface $event)
    {
        $context                   = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $messageService            = tubepress_impl_patterns_sl_ServiceLocator::getMessageService();
        $optionDescriptorReference = tubepress_impl_patterns_sl_ServiceLocator::getOptionDescriptorReference();
        $metaNames                 = tubepress_impl_util_LangUtils::getDefinedConstants('tubepress_api_const_options_names_Meta');
        $shouldShow                = array();
        $labels                    = array();
        $template                  = $event->getSubject();

        foreach ($metaNames as $metaName) {

            $optionDescriptor = $optionDescriptorReference->findOneByName($metaName);

            if (!$optionDescriptor) {

                $shouldShow[$metaName] = false;
                $labels[$metaName]     = '';
                continue;
            }

            $shouldShow[$metaName] = $context->get($metaName);
            $labels[$metaName]     = $messageService->_($optionDescriptor->getLabel());
        }

        $template->setVariable(tubepress_api_const_template_Variable::META_SHOULD_SHOW, $shouldShow);
        $template->setVariable(tubepress_api_const_template_Variable::META_LABELS, $labels);
    }
}

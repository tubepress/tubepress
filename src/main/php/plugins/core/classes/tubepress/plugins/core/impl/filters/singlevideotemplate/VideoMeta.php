<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Handles applying video meta info to the gallery template.
 */
class tubepress_plugins_core_impl_filters_singlevideotemplate_VideoMeta
{
    public function onSingleVideoTemplate(tubepress_api_event_TubePressEvent $event)
    {
        $template                  = $event->getSubject();
        $context                   = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $messageService            = tubepress_impl_patterns_sl_ServiceLocator::getMessageService();
        $optionDescriptorReference = tubepress_impl_patterns_sl_ServiceLocator::getOptionDescriptorReference();
        $metaNames                 = tubepress_impl_util_LangUtils::getDefinedConstants('tubepress_api_const_options_names_Meta');
        $shouldShow                = array();
        $labels                    = array();

        foreach ($metaNames as $metaName) {

            $optionDescriptor = $optionDescriptorReference->findOneByName($metaName);

            $shouldShow[$metaName] = $context->get($metaName);
            $labels[$metaName]     = $messageService->_($optionDescriptor->getLabel());
        }

        $template->setVariable(tubepress_api_const_template_Variable::META_SHOULD_SHOW, $shouldShow);
        $template->setVariable(tubepress_api_const_template_Variable::META_LABELS, $labels);
    }
}

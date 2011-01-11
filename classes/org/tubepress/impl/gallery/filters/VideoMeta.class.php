<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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
class org_tubepress_impl_gallery_filters_VideoMeta
{
    public function filter($template)
    {
        $tpom           = $ioc->get('org_tubepress_options_manager_OptionsManager');
        $messageService = $ioc->get('org_tubepress_message_MessageService');

        $metaNames  = org_tubepress_options_reference_OptionsReference::getOptionNamesForCategory(org_tubepress_options_Category::META);
        $shouldShow = array();
        $labels     = array();

        foreach ($metaNames as $metaName) {
            $shouldShow[$metaName] = $tpom->get($metaName);
            $labels[$metaName]     = $messageService->_('video-' . $metaName);
        }
        $template->setVariable(org_tubepress_template_Template::META_SHOULD_SHOW, $shouldShow);
        $template->setVariable(org_tubepress_template_Template::META_LABELS, $labels);

        return $template;
    }
}

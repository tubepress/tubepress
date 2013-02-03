<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Displays the video source tab.
 */
class tubepress_impl_options_ui_tabs_GallerySourceTab extends tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab
{
    /**
     * @var string The name of this tab.
     */
    const TAB_NAME = 'gallery-source';

    const TEMPLATE_VAR_CURRENT_MODE = 'tubepress_impl_options_ui_tabs_GallerySourceTab__mode';

    /**
     * Get the untranslated title of this tab.
     *
     * @return string The untranslated title of this tab.
     */
    protected final function getRawTitle()
    {
        return 'Which videos?';  //>(translatable)<
    }

    /**
     * Override point.
     *
     * Allows subclasses to perform additional modifications to the template.
     *
     * @param ehough_contemplate_api_Template $template The template for this tab.
     */
    protected final function addToTemplate(ehough_contemplate_api_Template $template)
    {
        $executionContext = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();

        $currentMode = $executionContext->get(tubepress_api_const_options_names_Output::GALLERY_SOURCE);

        $template->setVariable(self::TEMPLATE_VAR_CURRENT_MODE, $currentMode);
    }

    /**
     * Override point.
     *
     * Allows subclasses to change the template path.
     *
     * @param $originaltemplatePath string The original template path.
     *
     * @return string The (possibly) modified template path.
     */
    protected final function getModifiedTemplatePath($originaltemplatePath)
    {
        return 'src/main/resources/system-templates/options_page/gallery_source_tab.tpl.php';
    }

    public final function getName()
    {
        return self::TAB_NAME;
    }
}
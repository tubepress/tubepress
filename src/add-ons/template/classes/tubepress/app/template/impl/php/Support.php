<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_app_template_impl_php_Support implements ehough_templating_loader_LoaderInterface, ehough_templating_TemplateNameParserInterface
{
    /**
     * @var tubepress_app_template_impl_ThemeTemplateLocator
     */
    private $_themeTemplateLocator;

    public function __construct(tubepress_app_template_impl_ThemeTemplateLocator $locator)
    {
        $this->_themeTemplateLocator = $locator;
    }

    /**
     * Loads a template.
     *
     * @param ehough_templating_TemplateReferenceInterface $template A template
     *
     * @return ehough_templating_storage_Storage|bool    false if the template cannot be loaded, a ehough_templating_storage_Storage instance otherwise
     *
     * @api
     */
    public function load(ehough_templating_TemplateReferenceInterface $template)
    {
        if (!$this->_themeTemplateLocator->exists($template->getLogicalName())) {

            return false;
        }

        $path = $this->_themeTemplateLocator->getAbsolutePath($template->getLogicalName());

        return new ehough_templating_storage_FileStorage($path);
    }

    /**
     * Returns true if the template is still fresh.
     *
     * @param ehough_templating_TemplateReferenceInterface $template A template
     * @param int $time The last modification time of the cached template (timestamp)
     *
     * @return bool
     *
     * @api
     */
    public function isFresh(ehough_templating_TemplateReferenceInterface $template, $time)
    {
        return $this->_themeTemplateLocator->isFresh($template->getLogicalName(), $time);
    }

    /**
     * Convert a template name to a ehough_templating_TemplateReferenceInterface instance.
     *
     * @param string|ehough_templating_TemplateReferenceInterface $name A template name or a ehough_templating_TemplateReferenceInterface instance
     *
     * @return ehough_templating_TemplateReferenceInterface A template
     *
     * @api
     */
    public function parse($name)
    {
        return new ehough_templating_TemplateReference("$name.tpl.php", 'php');
    }
}

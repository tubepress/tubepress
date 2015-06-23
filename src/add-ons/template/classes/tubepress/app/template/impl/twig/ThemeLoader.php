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

class tubepress_app_template_impl_twig_ThemeLoader implements Twig_LoaderInterface
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
     * Gets the source code of a template, given its name.
     *
     * @param string $name The name of the template to load
     *
     * @return string The template source code
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function getSource($name)
    {
        if (!$this->_themeTemplateLocator->exists($name)) {

            throw $this->_loaderError($name);
        }

        return $this->_themeTemplateLocator->getSource($name);
    }

    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * @param string $name The name of the template to load
     *
     * @return string The cache key
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function getCacheKey($name)
    {
        if (!$this->_themeTemplateLocator->exists($name)) {

            throw $this->_loaderError($name);
        }

        return $this->_themeTemplateLocator->getCacheKey($name);
    }

    /**
     * Returns true if the template is still fresh.
     *
     * @param string $name The template name
     * @param int    $time The last modification time of the cached template
     *
     * @return bool    true if the template is fresh, false otherwise
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function isFresh($name, $time)
    {
        if (!$this->_themeTemplateLocator->exists($name)) {

            throw $this->_loaderError($name);
        }

        return $this->_themeTemplateLocator->isFresh($name, $time);
    }

    private function _loaderError($name)
    {
        return new Twig_Error_Loader("Twig template $name not found");
    }
}
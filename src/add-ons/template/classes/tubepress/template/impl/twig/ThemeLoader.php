<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_template_impl_twig_ThemeLoader implements Twig_LoaderInterface
{
    /**
     * @var tubepress_template_impl_ThemeTemplateLocator
     */
    private $_themeTemplateLocator;

    public function __construct(tubepress_template_impl_ThemeTemplateLocator $locator)
    {
        $this->_themeTemplateLocator = $locator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource($name)
    {
        if (!$this->_themeTemplateLocator->exists($name)) {

            throw $this->_loaderError($name);
        }

        return $this->_themeTemplateLocator->getSource($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name)
    {
        if (!$this->_themeTemplateLocator->exists($name)) {

            throw $this->_loaderError($name);
        }

        return $this->_themeTemplateLocator->getCacheKey($name);
    }

    /**
     * {@inheritdoc}
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

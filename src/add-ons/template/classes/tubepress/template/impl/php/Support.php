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
class tubepress_template_impl_php_Support implements \Symfony\Component\Templating\Loader\LoaderInterface, \Symfony\Component\Templating\TemplateNameParserInterface
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
    public function load(\Symfony\Component\Templating\TemplateReferenceInterface $template)
    {
        if (!$this->_themeTemplateLocator->exists($template->getLogicalName())) {

            return false;
        }

        $path = $this->_themeTemplateLocator->getAbsolutePath($template->getLogicalName());

        return new \Symfony\Component\Templating\Storage\FileStorage($path);
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh(\Symfony\Component\Templating\TemplateReferenceInterface $template, $time)
    {
        return $this->_themeTemplateLocator->isFresh($template->getLogicalName(), $time);
    }

    /**
     * {@inheritdoc}
     */
    public function parse($name)
    {
        return new \Symfony\Component\Templating\TemplateReference("$name.tpl.php", 'php');
    }
}

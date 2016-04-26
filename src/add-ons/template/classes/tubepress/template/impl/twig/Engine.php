<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 *
 * This file is based nearly entirely on Syfmony's Twig Bridge, the license for which
 * follows:
 *
 *
 * Copyright (c) 2004-2013 Fabien Potencier
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * This engine knows how to render Twig templates.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class tubepress_template_impl_twig_Engine implements \Symfony\Component\Templating\EngineInterface
{
    /**
     * @var Twig_Environment
     */
    protected $environment;

    /**
     * @var \Symfony\Component\Templating\TemplateNameParserInterface
     */
    protected $parser;

    /**
     * Constructor.
     *
     * @param Twig_Environment $environment A Twig_Environment instance
     */
    public function __construct(Twig_Environment $environment)
    {
        $this->environment = $environment;
        $this->parser      = new \Symfony\Component\Templating\TemplateNameParser();
    }

    /**
     * {@inheritdoc}
     */
    public function render($name, array $parameters = array())
    {
        $name = $this->_toTwigName($name);

        return $this->load($name)->render($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        if ($name instanceof Twig_Template) {

            return true;
        }

        $name   = $this->_toTwigName($name);
        $loader = $this->environment->getLoader();

        if ($loader instanceof Twig_ExistsLoaderInterface) {

            return $loader->exists((string) $name);
        }

        try {

            // cast possible TemplateReferenceInterface to string because the
            // EngineInterface supports them but Twig_LoaderInterface does not
            $loader->getSource((string) $name);

        } catch (Twig_Error_Loader $e) {

            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        return $this->exists($name);
    }

    /**
     * Loads the given template.
     *
     * @param string|\Symfony\Component\Templating\TemplateReferenceInterface|Twig_Template $name A template name or an instance of
     *                                                                                            \Symfony\Component\Templating\TemplateReferenceInterface or Twig_Template
     *
     * @return Twig_TemplateInterface A Twig_TemplateInterface instance
     *
     * @throws InvalidArgumentException if the template does not exist
     */
    protected function load($name)
    {
        if ($name instanceof Twig_Template) {

            return $name;
        }

        try {

            return $this->environment->loadTemplate((string) $name);

        } catch (Twig_Error_Loader $e) {

            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function _toTwigName($logicalName)
    {
        return "$logicalName.html.twig";
    }
}

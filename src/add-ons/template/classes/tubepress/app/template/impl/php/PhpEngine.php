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

class tubepress_app_template_impl_php_PhpEngine extends ehough_templating_PhpEngine
{
    /**
     * Loads the given template.
     *
     * @param string|ehough_templating_TemplateReferenceInterface $name A template name or a ehough_templating_TemplateReferenceInterface instance
     *
     * @return ehough_templating_storage_Storage A Storage instance
     *
     * @throws InvalidArgumentException if the template cannot be found
     */
    protected function load($name)
    {
        $template = $this->parser->parse($name);
        $storage  = $this->loader->load($template);

        if (false === $storage) {

            throw new InvalidArgumentException(sprintf('The template "%s" does not exist.', $template));
        }

        return $storage;
    }
}
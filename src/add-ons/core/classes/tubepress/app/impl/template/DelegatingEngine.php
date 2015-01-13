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

class tubepress_app_impl_template_DelegatingEngine extends ehough_templating_DelegatingEngine
{
    /**
     * Get an engine able to render the given template.
     *
     * @param string|ehough_templating_TemplateReferenceInterface $name A template name or a ehough_templating_TemplateReferenceInterface instance
     *
     * @return ehough_templating_EngineInterface The engine
     *
     * @throws RuntimeException if no engine able to work with the template is found
     *
     * @api
     */
    public function getEngine($name)
    {
        foreach ($this->engines as $engine) {
            if ($engine->supports($name) && $engine->exists($name)) {
                return $engine;
            }
        }

        throw new RuntimeException(sprintf('No engine is able to work with the template "%s".', $name));
    }
}
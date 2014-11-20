<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_app_impl_template_twig_FsLoader extends Twig_Loader_Filesystem
{
    protected function normalizeName($name)
    {
        if (strpos($name, '::') !== false) {

            $exploded = explode('::', $name);

            if (count($exploded) === 2) {

                $name = $exploded[1];
            }
        }

        return parent::normalizeName($name);
    }
}
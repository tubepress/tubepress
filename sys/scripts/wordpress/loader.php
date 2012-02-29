<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
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

/*
 * This is a little ugly, but it's the only way I know to
 * properly load WordPress if required. Please remember that this
 * code *cannot* be put inside of a class.
 */

$ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
$env = $ioc->get('org_tubepress_api_environment_Detector');

if ($env->isWordPress()) {

    $fs = $ioc->get('org_tubepress_api_filesystem_Explorer');
    include $fs->getTubePressBaseInstallationPath() . '/../../../wp-blog-header.php';
}


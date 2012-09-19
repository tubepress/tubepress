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

class tubepress_impl_wordpress_DefaultFrontEndCssAndJsInjector implements tubepress_spi_wordpress_FrontEndCssAndJsInjector
{
    /**
     * Prints out HTML and CSS into the HTML <head>.
     *
     * @return void
     */
    public final function printInHtmlHead()
    {
        $wordPressFunctionWrapper = tubepress_impl_wordpress_WordPressServiceLocator::getWordPressFunctionWrapper();

        /* no need to print anything in the head of the admin section */
        if ($wordPressFunctionWrapper->is_admin()) {

            return;
        }

        $hh  = tubepress_impl_patterns_ioc_KernelServiceLocator::getHeadHtmlGenerator();

        /* this inline JS helps initialize TubePress */
        $inlineJs = $hh->getHeadInlineJs();

        /* this meta stuff prevents search engines from indexing gallery pages > 1 */
        $meta = $hh->getHeadHtmlMeta();

        print <<<EOT
$inlineJs
$meta
EOT;
    }

    /**
     * Registers all the styles and scripts for the front end.
     *
     * @return void
     */
    public final function registerStylesAndScripts()
    {
        $wordPressFunctionWrapper = tubepress_impl_wordpress_WordPressServiceLocator::getWordPressFunctionWrapper();

        /* no need to queue any of this stuff up in the admin section or login page */
        if ($wordPressFunctionWrapper->is_admin() || __FILE__ === 'wp-login.php') {

            return;
        }

        $fse      = tubepress_impl_patterns_ioc_KernelServiceLocator::getEnvironmentDetector();
        $baseName = $fse->getTubePressInstallationDirectoryBaseName();

        $jsUrl  = $wordPressFunctionWrapper->plugins_url("$baseName/sys/ui/static/js/tubepress.js", $baseName);
        $cssUrl = $wordPressFunctionWrapper->plugins_url("$baseName/sys/ui/themes/default/style.css", $baseName);

        $wordPressFunctionWrapper->wp_register_script('tubepress', $jsUrl);
        $wordPressFunctionWrapper->wp_register_style('tubepress', $cssUrl);

        $wordPressFunctionWrapper->wp_enqueue_script('jquery');
        $wordPressFunctionWrapper->wp_enqueue_script('tubepress');

        $wordPressFunctionWrapper->wp_enqueue_style('tubepress');
    }
}


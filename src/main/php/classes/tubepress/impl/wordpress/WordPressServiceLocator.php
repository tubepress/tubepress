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

/**
 * A service locator for WordPress services.
 */
class tubepress_impl_wordpress_WordPressServiceLocator
{
    /**
     * @var tubepress_spi_wordpress_FrontEndCssAndJsInjector
     */
    private static $_frontEndCssAndJsInjector;

    /**
     * @var tubepress_spi_wordpress_WordPressFunctionWrapper
     */
    private static $_wordPressFunctionWrapper;

    /**
     * @return tubepress_spi_wordpress_FrontEndCssAndJsInjector The HTML injector.
     */
    public static function getFrontEndCssAndJsInjector()
    {
        return self::$_frontEndCssAndJsInjector;
    }

    /**
     * @return tubepress_spi_wordpress_WordPressFunctionWrapper The WP function wrapper.
     */
    public static function getWordPressFunctionWrapper()
    {
        return self::$_wordPressFunctionWrapper;
    }

    /**
     * @param tubepress_spi_wordpress_FrontEndCssAndJsInjector $injector The injector.
     */
    public static function setFrontEndCssAndJsInjector(tubepress_spi_wordpress_FrontEndCssAndJsInjector $injector)
    {
        self::$_frontEndCssAndJsInjector = $injector;
    }

    /**
     * @param tubepress_spi_wordpress_WordPressFunctionWrapper $wordPressFunctionWrapper The WP function wrapper.
     */
    public static function setWordPressFunctionWrapper(tubepress_spi_wordpress_WordPressFunctionWrapper $wordPressFunctionWrapper)
    {
        self::$_wordPressFunctionWrapper = $wordPressFunctionWrapper;
    }
}

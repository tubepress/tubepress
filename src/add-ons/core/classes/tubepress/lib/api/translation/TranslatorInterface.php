<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 *
 * This code is based largely on Symfony's Translator Component interface. The license
 * for Symfony follows:
 *
 * Copyright (c) 2004-2014 Fabien Potencier
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
 * General purpose message abstraction for TubePress
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_lib_api_translation_TranslatorInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_lib_api_translation_TranslatorInterface';

    /**
     * Translates the given message.
     *
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param array       $parameters An array of parameters for the message
     * @param string|null $domain     The domain for the message or null to use the default
     * @param string|null $locale     The locale or null to use the default
     *
     * @throws InvalidArgumentException If the locale contains invalid characters
     *
     * @return string The translated string
     *
     * @api
     */
    function trans($id, array $parameters = array(), $domain = null, $locale = null);

    /**
     * Translates the given choice message by choosing a translation according to a number.
     *
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param int         $number     The number to use to find the indice of the message
     * @param array       $parameters An array of parameters for the message
     * @param string|null $domain     The domain for the message or null to use the default
     * @param string|null $locale     The locale or null to use the default
     *
     * @throws InvalidArgumentException If the locale contains invalid characters
     *
     * @return string The translated string
     *
     * @api
     */
    function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null);

    /**
     * Sets the current locale.
     *
     * @param string $locale The locale
     *
     * @throws InvalidArgumentException If the locale contains invalid characters
     *
     * @api
     */
    function setLocale($locale);

    /**
     * Returns the current locale.
     *
     * @return string The locale
     *
     * @api
     */
    function getLocale();
}

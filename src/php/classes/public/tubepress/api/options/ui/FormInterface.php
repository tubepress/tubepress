<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * The options page.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_api_options_ui_FormInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_api_options_ui_FormInterface';

    /**
     * @param array   $errors        An associative array, which may be empty, of field IDs to error messages.
     * @param boolean $justSubmitted True if the form was just submitted, false otherwise.
     *
     * @return string The HTML for the options page.
     *
     * @api
     * @since 4.0.0
     */
    function getHtml(array $errors = array(), $justSubmitted = false);

    /**
     * Invoked when the page is submitted by the user.
     *
     * @return array An associative array, which may be empty, of field IDs to error messages.
     *
     * @api
     * @since 4.0.0
     */
    function onSubmit();

    /**
     * @return tubepress_api_url_UrlInterface[]
     *
     * @api
     * @since 4.0.0
     */
    function getUrlsCSS();

    /**
     * @return tubepress_api_url_UrlInterface[]
     *
     * @api
     * @since 4.0.0
     */
    function getUrlsJS();

    /**
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    function getCSS();

    /**
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    function getJS();
}
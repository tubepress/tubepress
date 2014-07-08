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

/**
 * A TubePress add-on or theme.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_platform_api_contrib_ContributableInterface
{
    /**
     * @return string The globally unique name of this add-on. Must be 100 characters or less,
     *                all lowercase, and contain only URL-safe characters ([a-z0-9-_\.]+).
     *
     * @api
     * @since 4.0.0
     */
    function getName();

    /**
     * @return string The version of this add-on. This *should* be a semantic version number.
     *
     * @api
     * @since 4.0.0
     */
    function getVersion();

    /**
     * @return string A user-friendly title for the add-on. 255 characters or less.
     *
     * @api
     * @since 4.0.0
     */
    function getTitle();

    /**
     * @return array An associative array of author information. The possible array keys are
     *               'name', 'email', and 'url'. 'name' is required, and the other fields are optional.
     *
     * @api
     * @since 4.0.0
     */
    function getAuthor();

    /**
     * @return array An array of associative arrays of license information. The possible array keys are
     *               'url' and 'type'. 'url' is required and must link to the license text. 'type'
     *               may be supplied if the license is one of the official open source licenses found
     *               at http://www.opensource.org/licenses/alphabetical
     *
     * @api
     * @since 4.0.0
     */
    function getLicenses();

    /**
     * @return string Optional. A longer description of this add-on. 1000 characters or less.
     *
     * @api
     * @since 4.0.0
     */
    function getDescription();

    /**
     * @return array Optional. An array of keywords that might help folks discover this add-on. Only
     *               letters, numbers, hypens, and dots. Each keyword must be 30 characters or less.
     *
     * @api
     * @since 4.0.0
     */
    function getKeywords();

    /**
     * @return string Optional. A link to the add-on's homepage. May be null.
     *
     * @api
     * @since 4.0.0
     */
    function getHomepageUrl();

    /**
     * @return string Optional. A link to the add-on's documentation. May be null.
     *
     * @api
     * @since 4.0.0
     */
    function getDocumentationUrl();

    /**
     * @return string Optional. A link to a live demo of the add-on. May be null.
     *
     * @api
     * @since 4.0.0
     */
    function getDemoUrl();

    /**
     * @return string Optional. A link to a download URL. May be null.
     *
     * @api
     * @since 4.0.0
     */
    function getDownloadUrl();

    /**
     * @return string Optional. A link to a bug tracker for this add-on. May be null.
     *
     * @api
     * @since 4.0.0
     */
    function getBugTrackerUrl();

    /**
     * @return string[] An array of strings, which may be empty but not null, of screenshots of this contributable.
     *                  URLs may either be absolute, or relative. In the latter case, they will be considered to be
     *                  relative from the contributable root. Array keys are considered to be thumbnails, and
     *                  values are considered to be full-sized images.
     *
     * @api
     * @since 4.0.0
     */
    function getScreenshots();
}

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

/**
 * A TubePress add-on or theme.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_platform_api_contrib_ContributableInterface
{
    /**
     * @return string Required. The globally unique name of this contributable.
     *
     *                Must be 100 characters or less, all lowercase, and contain only URL-safe characters
     *                and slashes ([a-z0-9-_\./]{1,100}).
     *
     * @api
     * @since 4.0.0
     */
    function getName();

    /**
     * @return tubepress_platform_api_version_Version Required. The version of this contributable.
     *
     * @api
     * @since 4.0.0
     */
    function getVersion();

    /**
     * @return string Required. A user-friendly title for the contributable. 255 characters or less.
     *
     * @api
     * @since 4.0.0
     */
    function getTitle();

    /**
     * @return tubepress_platform_api_collection_MapInterface[] Required. One or more authors. Each author
     *                                                          may contain the following property names.
     *
     *               key 'name'  : required, string
     *               key 'email' : optional, string
     *               key 'url'   : optional, tubepress_platform_api_url_UrlInterface,
     *               key 'role'  : optional, string
     *
     * @api
     * @since 4.0.0
     */
    function getAuthors();

    /**
     * @return tubepress_platform_api_collection_MapInterface Required. The license which may contain the following
     *                                                        property names.
     *
     *               key 'urls' : required, tubepress_platform_api_url_UrlInterface[]. URL(s) to the license(s) text.
     *               key 'type' : required, string. An identifier to indicate to developer's the general license type.
     *                            Consider using one of http://www.opensource.org/licenses/alphabetical.
     *
     * @api
     * @since 4.0.0
     */
    function getLicense();

    /**
     * @return string Optional. A longer description of this contributable that may be shown to the user.
     *
     *                5000 characters or less.
     *
     * @api
     * @since 4.0.0
     */
    function getDescription();

    /**
     * @return array Optional. An array of keywords that might help users discover this contributable.
     *
     *               Each keyword must be comprised of only letters, numbers, hypens, and dots. Each keyword must
     *               be 30 characters or less.
     *
     * @api
     * @since 4.0.0
     */
    function getKeywords();

    /**
     * @return tubepress_platform_api_url_UrlInterface Optional. A link to the contributable's homepage. May be null.
     *
     * @api
     * @since 4.0.0
     */
    function getHomepageUrl();

    /**
     * @return tubepress_platform_api_url_UrlInterface Optional. A link to the contributable's documentation. May be null.
     *
     * @api
     * @since 4.0.0
     */
    function getDocumentationUrl();

    /**
     * @return tubepress_platform_api_url_UrlInterface Optional. A link to a live demo of the contributable. May be null.
     *
     * @api
     * @since 4.0.0
     */
    function getDemoUrl();

    /**
     * @return tubepress_platform_api_url_UrlInterface Optional. A link to a download URL. May be null.
     *
     * @api
     * @since 4.0.0
     */
    function getDownloadUrl();

    /**
     * @return tubepress_platform_api_url_UrlInterface Optional. A link to a bug tracker for this contributable. May be null.
     *
     * @api
     * @since 4.0.0
     */
    function getBugTrackerUrl();

    /**
     * @return tubepress_platform_api_url_UrlInterface Optional. A link to the source code for this contributable. May be null.
     *
     * @api
     * @since 4.0.0
     */
    function getSourceCodeUrl();

    /**
     * @return tubepress_platform_api_url_UrlInterface Optional. A link to the support forum for this contributable. May be null.
     *
     * @api
     * @since 4.0.0
     */
    function getForumUrl();

    /**
     * @return array Optional. One or more screenshots of this contributable.
     *
     *               Each element of this array is an array of two tubepress_platform_api_url_UrlInterface instances
     *               that represent the screenshot.
     *
     *               The first URL points to the thumbnail version of the image pointed to by the second URL.
     *
     *               All URLs must be absolute, and the path must end with .png or .jpg.
     *
     * @api
     * @since 4.0.0
     */
    function getScreenshots();

    /**
     * @return tubepress_platform_api_collection_MapInterface
     *
     * @api
     * @since 4.0.0
     */
    function getProperties();
}

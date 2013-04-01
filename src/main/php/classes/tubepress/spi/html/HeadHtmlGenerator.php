<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Generates HTML for use in the <head>.
 */
interface tubepress_spi_html_HeadHtmlGenerator
{
    const _ = 'tubepress_spi_html_HeadHtmlGenerator';

    function getHeadJqueryInclusion();

    function getHeadInlineJs();

    function getHeadJsIncludeString();

    function getHeadCssIncludeString();

    function getHeadHtmlMeta();
}
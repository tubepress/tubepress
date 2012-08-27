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
 * Official filter points around the TubePress core.
 */
interface org_tubepress_api_const_plugin_FilterPoint
{
    /**
     * Modify any HTML that TubePress generates.
     *
     * To use this filter point, create a class that includes a function with the method signature defined below.
     * Then in your plugin file (tubepress-content/plugins/yourplugin/yourplugin.php), register the class with:
     *
     *     TubePress::registerFilter('html', $yourClassInstance);
     *
     *
     * @param string $html The raw HTML to modify.
     *
     * @return string The (possibly modified) HTML. Never null.
     *
     * function alter_html($html);
     */
    const HTML_ANY = 'html';

    /**
     * Modify the HTML for the embedded video player.
     *
     * To use this filter point, create a class that includes a function with the method signature defined below.
     * Then in your plugin file (tubepress-content/plugins/yourplugin/yourplugin.php), register the class with:
     *
     *     TubePress::registerFilter('embeddedHtml', $yourClassInstance);
     *
     *
     * @param string $template          The raw embedded player HTML to modify.
     * @param string $videoId           The video ID currently being loaded into the embedded player.
     * @param string $videoProviderName The name of the video provider ("vimeo" or "youtube")
     * @param string $embeddedImplName  The name of the embedded implementation ("youtube", "longtail", or "vimeo")
     *
     * @return string The (possibly modified) HTML for the embedded player. Never null.
     *
     * function alter_embeddedHtml($html, $videoId, $videoProviderName, $embeddedImplName);
     */
    const HTML_EMBEDDED = 'embeddedHtml';

    /**
     * Modify the HTML for a thumbnail gallery.
     *
     * To use this filter point, create a class that includes a function with the method signature defined below.
     * Then in your plugin file (tubepress-content/plugins/yourplugin/yourplugin.php), register the class with:
     *
     *     TubePress::registerFilter('galleryHtml', $yourClassInstance);
     *
     *
     * @param string                                    $html              The HTML to modify.
     * @param org_tubepress_api_provider_ProviderResult $providerResult    The provider result for this gallery.
     * @param int                                       $page              The current page number.
     * @param string                                    $videoProviderName The name of the video provider ("vimeo" or "youtube")
     *
     * @return string The (possibly modified) html. Never null.
     *
     * function alter_galleryHtml($html, org_tubepress_api_provider_ProviderResult $providerResult, $page, $providerName);
     */
    const HTML_GALLERY = 'galleryHtml';

    /**
     * function alter_paginationHtml($paginationHtml, $providerName);
     */
    const HTML_PAGINATION = 'paginationHtml';

    /**
     * Modify the HTML for the TubePress "player"
     *
     * To use this filter point, create a class that includes a function with the method signature defined below.
     * Then in your plugin file (tubepress-content/plugins/yourplugin/yourplugin.php), register the class with:
     *
     *     TubePress::registerFilter('playerHtml', $yourClassInstance);
     *
     *
     * @param string                              $html              The HTML to modify.
     * @param org_tubepress_api_video_Video       $video             The video to modify.
     * @param string                              $videoProviderName The name of the video provider ("vimeo" or "youtube")
     * @param string                              $playerName        The TubePress "player" name (e.g. "shadowbox", "normal", "youtube", etc)
     *
     * @return string The (possibly modified) player HTML. Never null.
     *
     * function alter_playerHtml($html, org_tubepress_api_video_Video $video, $providerName, $playerName);
     */
    const HTML_PLAYER = 'playerHtml';

    /**
     * function alter_searchInputHtml($rawHtml);
     */
    const HTML_SEARCHINPUT = 'searchInputHtml';

    /**
     * Modify the HTML for a single video embed.
     *
     * To use this filter point, create a class that includes a function with the method signature defined below.
     * Then in your plugin file (tubepress-content/plugins/yourplugin/yourplugin.php), register the class with:
     *
     *     TubePress::registerFilter('singleVideoHtml', $yourClassInstance);
     *
     *
     * @param string                        $html              The HTML to modify.
     * @param org_tubepress_api_video_Video $video             The video to modify.
     * @param string                        $videoProviderName The name of the video provider ("vimeo" or "youtube")
     *
     * @return org_tubepress_api_template_Template The (possibly modified) html. Never null.
     *
     *
     * function alter_singleVideoHtml($rawHtml, org_tubepress_api_video_Video $video, $providerName);
     */
    const HTML_SINGLEVIDEO = 'singleVideoHtml';

    /**
     * Modify the name-value pairs sent to TubePressGallery.init().
     *
     * To use this filter point, create a class that includes a function with the method signature defined below.
     * Then in your plugin file (tubepress-content/plugins/yourplugin/yourplugin.php), register the class with:
     *
     *     TubePress::registerFilter('galleryInitJavaScript', $yourClassInstance);
     *
     *
     * @param array $args An associative array (name => value) of args to send to TubePressGallery.init();
     *
     * @return array The (possibly modified) array. Never null.
     *
     *
     * function alter_galleryInitJavaScript($args);
     */
    const JAVASCRIPT_GALLERYINIT = 'galleryInitJavaScript';

    /**
     * Applied to a single option name/value pair before it is applied to TubePress's execution context
     *  or persistence storage. This filter is invoked *before* the option name or value is validated!
     *
     * To use this filter point, create a class that includes a function with the method signature defined below.
     * Then in your plugin file (tubepress-content/plugins/yourplugin/yourplugin.php), register the class with:
     *
     *     TubePress::registerFilter('preValidationOptionSet', $yourClassInstance);
     *
     * @param string $value The option value being set.
     * @param string $name  The name of the option being set.
     *
     * @return unknown_type The (possibly modified) option value. May be null.
     *
     * function alter_preValidationOptionSet($value, $name);
     */
    const OPTION_SET_PRE_VALIDATION = 'preValidationOptionSet';

    /**
     * Filters the TubePress provider result.
     *
     * function alter_providerResult(org_tubepress_api_provider_ProviderResult $providerResult, $providerName);
     */
    const PROVIDER_RESULT = 'providerResult';

    /**
     * Modify the embedded player template.
     *
     * To use this filter point, create a class that includes a function with the method signature defined below.
     * Then in your plugin file (tubepress-content/plugins/yourplugin/yourplugin.php), register the class with:
     *
     *     TubePress::registerFilter('embeddedTemplate', $yourClassInstance);
     *
     *
     * @param org_tubepress_api_template_Template $template          The template to modify.
     * @param string                              $videoId           The video ID currently being loaded into the embedded player.
     * @param string                              $videoProviderName The name of the video provider ("vimeo" or "youtube")
     * @param org_tubepress_api_url_Url           $dataUrl           The embedded data URL.
     * @param string                              $embeddedImplName  The name of the embedded implementation ("youtube", "longtail", or "vimeo")
     *
     * @return org_tubepress_api_template_Template The (possibly modified) template. Never null.
     *
     * function alter_embeddedTemplate(org_tubepress_api_template_Template $template, $videoId, $videoProviderName,
     * 								   org_tubepress_api_url_Url $dataUrl, $embeddedImplName)
     */
    const TEMPLATE_EMBEDDED = 'embeddedTemplate';

    /**
     * Modify the template for a thumbnail gallery.
     *
     * To use this filter point, create a class that includes a function with the method signature defined below.
     * Then in your plugin file (tubepress-content/plugins/yourplugin/yourplugin.php), register the class with:
     *
     *     TubePress::registerFilter('galleryTemplate', $yourClassInstance);
     *
     *
     * @param org_tubepress_api_template_Template       $template          The template to modify.
     * @param org_tubepress_api_provider_ProviderResult $providerResult    The provider result for this gallery.
     * @param int                                       $page              The current page number.
     * @param string                                    $videoProviderName The name of the video provider ("vimeo" or "youtube")
     *
     * @return org_tubepress_api_template_Template The (possibly modified) template. Never null.
     *
     *
     * function alter_galleryTemplate(org_tubepress_api_template_Template $template, org_tubepress_api_provider_ProviderResult $providerResult, $page, $providerName);
     */
    const TEMPLATE_GALLERY = 'galleryTemplate';

    /**
     * Modify the template for the TubePress "player"
     *
     * To use this filter point, create a class that includes a function with the method signature defined below.
     * Then in your plugin file (tubepress-content/plugins/yourplugin/yourplugin.php), register the class with:
     *
     *     TubePress::registerFilter('playerTemplate', $yourClassInstance);
     *
     *
     * @param org_tubepress_api_template_Template $template          The template to modify.
     * @param org_tubepress_api_video_Video       $video             The video to modify.
     * @param string                              $videoProviderName The name of the video provider ("vimeo" or "youtube")
     * @param string                              $playerName        The TubePress "player" name (e.g. "shadowbox", "normal", "youtube", etc)
     *
     * @return org_tubepress_api_template_Template The (possibly modified) template. Never null.
     *
     * function alter_playerTemplate(org_tubepress_api_template_Template $template, org_tubepress_api_video_Video $video, $videoProviderName, $playerName);
     */
    const TEMPLATE_PLAYER = 'playerTemplate';

    /**
     * Modify the template for a single video embed.
     *
     * To use this filter point, create a class that includes a function with the method signature defined below.
     * Then in your plugin file (tubepress-content/plugins/yourplugin/yourplugin.php), register the class with:
     *
     *     TubePress::registerFilter('singleVideoTemplate', $yourClassInstance);
     *
     *
     * @param org_tubepress_api_template_Template $template          The template to modify.
     * @param org_tubepress_api_video_Video       $video             The video to modify.
     * @param string                              $videoProviderName The name of the video provider ("vimeo" or "youtube")
     *
     * @return org_tubepress_api_template_Template The (possibly modified) template. Never null.
     *
     *
     * function alter_singleVideoTemplate(org_tubepress_api_template_Template $template, org_tubepress_api_video_Video $video, $providerName);
     */
    const TEMPLATE_SINGLEVIDEO = 'singleVideoTemplate';

    /**
     * Modify the template for the interactive search input.
     *
     * To use this filter point, create a class that includes a function with the method signature defined below.
     * Then in your plugin file (tubepress-content/plugins/yourplugin/yourplugin.php), register the class with:
     *
     *     TubePress::registerFilter('searchInputTemplate', $yourClassInstance);
     *
     *
     * @param org_tubepress_api_template_Template $template The template to modify.
     *
     * @return org_tubepress_api_template_Template The (possibly modified) template. Never null.
     *
     *
     * function alter_searchInputTemplate(org_tubepress_api_template_Template $template);
     */
    const TEMPLATE_SEARCHINPUT = 'searchInputTemplate';

    /**
     * Applied to a single option name/value pair as it is read from external input.
     *
     * To use this filter point, create a class that includes a function with the method signature defined below.
     * Then in your plugin file (tubepress-content/plugins/yourplugin/yourplugin.php), register the class with:
     *
     *     TubePress::registerFilter('variableReadFromExternalInput', $yourClassInstance);
     *
     * @param string $value The option value being set.
     * @param string $name  The name of the option being set.
     *
     * @return unknown_type The (possibly modified) option value. May be null.
     *
     * function alter_variableReadFromExternalInput($value, $name);
     */
    const VARIABLE_READ_FROM_EXTERNAL_INPUT = 'variableReadFromExternalInput';

    /**
     * Modify an invididual TubePress video (YouTube or Vimeo).
     *
     * To use this filter point, create a class that includes a function with the method signature defined below.
     * Then in your plugin file (tubepress-content/plugins/yourplugin/yourplugin.php), register the class with:
     *
     *     TubePress::registerFilter('video', $yourClassInstance);
     *
     *
     * @param org_tubepress_api_video_Video $video             The video to modify.
     * @param string                        $videoProviderName The name of the video provider ("vimeo" or "youtube")
     *
     * @return org_tubepress_api_video_Video The (possibly modified) video. Never null.
     *
     * function alter_video(org_tubepress_api_video_Video $video, $videoProviderName);
     */
    const VIDEO = 'video';
}


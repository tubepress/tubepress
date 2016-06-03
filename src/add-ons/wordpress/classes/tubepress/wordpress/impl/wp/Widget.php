<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @deprecated
 */
class tubepress_wordpress_impl_wp_Widget
{
    const WIDGET_CONTROL_SHORTCODE = 'widgetControlShortcode';
    const WIDGET_CONTROL_TITLE     = 'widgetControlTitle';
    const WIDGET_SHORTCODE         = 'widgetShortcode';
    const WIDGET_TITLE             = 'widgetTitle';
    const WIDGET_SUBMIT_TAG        = 'tubepress-widget-submit';
    const WIDGET_NONCE_FIELD       = 'widgetNonceField';

    /**
     * @var tubepress_api_translation_TranslatorInterface
     */
    private $_translator;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_options_PersistenceInterface
     */
    private $_persistence;

    /**
     * @var tubepress_api_html_HtmlGeneratorInterface
     */
    private $_htmlGenerator;

    /**
     * @var tubepress_api_shortcode_ParserInterface
     */
    private $_shortcodeParser;

    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    /**
     * @var tubepress_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    /**
     * @var tubepress_api_http_RequestParametersInterface
     */
    private $_httpRequestParams;

    /**
     * @var tubepress_api_template_TemplatingInterface
     */
    private $_templating;

    public function __construct(tubepress_api_options_ContextInterface        $context,
                                tubepress_api_options_PersistenceInterface    $persistence,
                                tubepress_api_translation_TranslatorInterface $translator,
                                tubepress_api_html_HtmlGeneratorInterface     $htmlGenerator,
                                tubepress_api_shortcode_ParserInterface       $parser,
                                tubepress_wordpress_impl_wp_WpFunctions       $wpFunctions,
                                tubepress_api_util_StringUtilsInterface       $stringUtils,
                                tubepress_api_http_RequestParametersInterface $requestParameters,
                                tubepress_api_template_TemplatingInterface    $templating)
    {
        $this->_translator        = $translator;
        $this->_context           = $context;
        $this->_persistence       = $persistence;
        $this->_htmlGenerator     = $htmlGenerator;
        $this->_shortcodeParser   = $parser;
        $this->_wpFunctions       = $wpFunctions;
        $this->_stringUtils       = $stringUtils;
        $this->_httpRequestParams = $requestParameters;
        $this->_templating        = $templating;
    }

    /**
     * Registers all the styles and scripts for the front end.
     *
     * @param array $opts The options.
     *
     * @return void
     */
    public function printWidgetHtml(tubepress_api_event_EventInterface $event)
    {
        $opts = $event->getSubject();

        extract($opts);

        /* default widget options */
        $defaultWidgetOptions = array(
            tubepress_api_options_Names::FEED_RESULTS_PER_PAGE    => 3,
            tubepress_api_options_Names::META_DISPLAY_VIEWS       => false,
            tubepress_api_options_Names::META_DISPLAY_DESCRIPTION => true,
            tubepress_api_options_Names::META_DESC_LIMIT          => 50,
            tubepress_api_options_Names::PLAYER_LOCATION          => 'shadowbox',
            tubepress_api_options_Names::GALLERY_THUMB_HEIGHT     => 105,
            tubepress_api_options_Names::GALLERY_THUMB_WIDTH      => 135,
            tubepress_api_options_Names::GALLERY_PAGINATE_ABOVE   => false,
            tubepress_api_options_Names::GALLERY_PAGINATE_BELOW   => false,
            tubepress_api_options_Names::THEME                    => 'tubepress/default',
            tubepress_api_options_Names::GALLERY_FLUID_THUMBS     => false,
        );

        /* now apply the user's options */
        $rawTag    = $this->_context->get(tubepress_wordpress_api_Constants::OPTION_WIDGET_SHORTCODE);
        $widgetTag = $this->_stringUtils->removeNewLines($rawTag);
        $this->_shortcodeParser->parse($widgetTag);

        /* calculate the final options */
        $finalOptions = array_merge($defaultWidgetOptions, $this->_context->getEphemeralOptions());
        $this->_context->setEphemeralOptions($finalOptions);

        if ($this->_context->get(tubepress_api_options_Names::THEME) === '') {

            $this->_context->setEphemeralOption(tubepress_api_options_Names::THEME, 'tubepress/default');
        }

        $out = $this->_htmlGenerator->getHtml();

        /* do the standard WordPress widget dance */
        /* @noinspection PhpUndefinedVariableInspection */
        echo $before_widget . $before_title .
            $this->_context->get(tubepress_wordpress_api_Constants::OPTION_WIDGET_TITLE) .
            $after_title . $out . $after_widget;

        /* reset the context for the next shortcode */
        $this->_context->setEphemeralOptions(array());
    }

    /**
     * Filter the content (which may be empty).
     */
    public function printControlHtml()
    {
        /* are we saving? */
        if ($this->_httpRequestParams->hasParam(self::WIDGET_SUBMIT_TAG)) {

            $this->_wpFunctions->check_admin_referer('tubepress-widget-nonce-save', 'tubepress-widget-nonce');

            $this->_persistence->queueForSave(tubepress_wordpress_api_Constants::OPTION_WIDGET_SHORTCODE, $this->_httpRequestParams->getParamValue('tubepress-widget-tagstring'));
            $this->_persistence->queueForSave(tubepress_wordpress_api_Constants::OPTION_WIDGET_TITLE, $this->_httpRequestParams->getParamValue('tubepress-widget-title'));

            $this->_persistence->flushSaveQueue();
        }

        $templateVars = array(
            self::WIDGET_TITLE             => $this->_persistence->fetch(tubepress_wordpress_api_Constants::OPTION_WIDGET_TITLE),
            self::WIDGET_CONTROL_TITLE     => $this->_translator->trans('Title'),                                                             //>(translatable)<
            self::WIDGET_CONTROL_SHORTCODE => $this->_translator->trans(sprintf('TubePress shortcode for the widget. See the <a href="%s" target="_blank">documentation</a>.', "http://docs.tubepress.com/")), //>(translatable)<
            self::WIDGET_SHORTCODE         => $this->_persistence->fetch(tubepress_wordpress_api_Constants::OPTION_WIDGET_SHORTCODE),
            self::WIDGET_SUBMIT_TAG        => self::WIDGET_SUBMIT_TAG,
            self::WIDGET_NONCE_FIELD       => $this->_wpFunctions->wp_nonce_field('tubepress-widget-nonce-save', 'tubepress-widget-nonce', true, false),
        );

        echo $this->_templating->renderTemplate('wordpress/single-widget-controls', $templateVars);
    }
}

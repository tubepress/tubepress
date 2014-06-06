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

class tubepress_wordpress_impl_wp_Widget
{
    const WIDGET_CONTROL_SHORTCODE = 'widgetControlShortcode';
    const WIDGET_CONTROL_TITLE     = 'widgetControlTitle';
    const WIDGET_SHORTCODE         = 'widgetShortcode';
    const WIDGET_TITLE             = 'widgetTitle';
    const WIDGET_SUBMIT_TAG        = 'tubepress-widget-submit';

    /**
     * @var tubepress_core_translation_api_TranslatorInterface
     */
    private $_translator;

    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_options_api_PersistenceInterface
     */
    private $_persistence;

    /**
     * @var tubepress_core_html_api_HtmlGeneratorInterface
     */
    private $_htmlGenerator;

    /**
     * @var tubepress_core_shortcode_api_ParserInterface
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
     * @var tubepress_core_http_api_RequestParametersInterface
     */
    private $_httpRequestParams;

    /**
     * @var tubepress_core_template_api_TemplateFactoryInterface
     */
    private $_templateFactory;

    public function __construct(tubepress_core_options_api_ContextInterface          $context,
                                tubepress_core_options_api_PersistenceInterface      $persistence,
                                tubepress_core_translation_api_TranslatorInterface   $translator,
                                tubepress_core_html_api_HtmlGeneratorInterface       $htmlGenerator,
                                tubepress_core_shortcode_api_ParserInterface         $parser,
                                tubepress_wordpress_impl_wp_WpFunctions              $wpFunctions,
                                tubepress_api_util_StringUtilsInterface              $stringUtils,
                                tubepress_core_http_api_RequestParametersInterface   $requestParameters,
                                tubepress_core_template_api_TemplateFactoryInterface $templateFactory)
    {
        $this->_translator        = $translator;
        $this->_context           = $context;
        $this->_persistence       = $persistence;
        $this->_htmlGenerator     = $htmlGenerator;
        $this->_shortcodeParser   = $parser;
        $this->_wpFunctions       = $wpFunctions;
        $this->_stringUtils       = $stringUtils;
        $this->_httpRequestParams = $requestParameters;
        $this->_templateFactory   = $templateFactory;
    }

    /**
     * Registers all the styles and scripts for the front end.
     *
     * @param array $opts The options.
     *
     * @return void
     */
    public function printWidgetHtml($opts)
    {
        extract($opts);

        /* default widget options */
        $defaultWidgetOptions = array(
            tubepress_core_media_provider_api_Constants::OPTION_RESULTS_PER_PAGE     => 3,
            tubepress_core_media_item_api_Constants::OPTION_VIEWS            => false,
            tubepress_core_media_item_api_Constants::OPTION_DESCRIPTION      => true,
            tubepress_core_media_item_api_Constants::OPTION_DESC_LIMIT       => 50,
            tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION        => 'shadowbox',
            tubepress_core_html_gallery_api_Constants::OPTION_THUMB_HEIGHT    => 105,
            tubepress_core_html_gallery_api_Constants::OPTION_THUMB_WIDTH     => 135,
            tubepress_core_html_gallery_api_Constants::OPTION_PAGINATE_ABOVE  => false,
            tubepress_core_html_gallery_api_Constants::OPTION_PAGINATE_BELOW  => false,
            tubepress_core_theme_api_Constants::OPTION_THEME                   => 'tubepress/legacy-sidebar',
            tubepress_core_html_gallery_api_Constants::OPTION_FLUID_THUMBS     => false
        );

        /* now apply the user's options */
        $rawTag    = $this->_context->get(tubepress_wordpress_api_Constants::OPTION_WIDGET_SHORTCODE);
        $widgetTag = $this->_stringUtils->removeNewLines($rawTag);
        $this->_shortcodeParser->parse($widgetTag);

        /* calculate the final options */
        $finalOptions = array_merge($defaultWidgetOptions, $this->_context->getEphemeralOptions());
        $this->_context->setEphemeralOptions($finalOptions);

        if ($this->_context->get(tubepress_core_theme_api_Constants::OPTION_THEME) === '') {

            $this->_context->setEphemeralOption(tubepress_core_theme_api_Constants::OPTION_THEME, 'tubepress/legacy-sidebar');
        }

        $out = $this->_htmlGenerator->getHtmlForShortcode('');

        /* do the standard WordPress widget dance */
        /** @noinspection PhpUndefinedVariableInspection */
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

            self::_verifyNonce();

            $this->_persistence->queueForSave(tubepress_wordpress_api_Constants::OPTION_WIDGET_SHORTCODE, $this->_httpRequestParams->getParamValue('tubepress-widget-tagstring'));
            $this->_persistence->queueForSave(tubepress_wordpress_api_Constants::OPTION_WIDGET_TITLE, $this->_httpRequestParams->getParamValue('tubepress-widget-title'));

            $this->_persistence->flushSaveQueue();
        }

        /* load up the gallery template */
        $templatePath = TUBEPRESS_ROOT . '/src/core/wordpress/resources/templates/widget_controls.tpl.php';
        $tpl          = $this->_templateFactory->fromFilesystem(array($templatePath));

        /* set up the template */
        $tpl->setVariable(self::WIDGET_TITLE, $this->_persistence->fetch(tubepress_wordpress_api_Constants::OPTION_WIDGET_TITLE));
        $tpl->setVariable(self::WIDGET_CONTROL_TITLE, $this->_translator->_('Title'));                                                                                                            //>(translatable)<
        $tpl->setVariable(self::WIDGET_CONTROL_SHORTCODE, $this->_translator->_(sprintf('TubePress shortcode for the widget. See the <a href="%s" target="_blank">documentation</a>.', "http://docs.tubepress.com/"))); //>(translatable)<
        $tpl->setVariable(self::WIDGET_SHORTCODE, $this->_persistence->fetch(tubepress_wordpress_api_Constants::OPTION_WIDGET_SHORTCODE));
        $tpl->setVariable(self::WIDGET_SUBMIT_TAG, self::WIDGET_SUBMIT_TAG);

        /* get the template's output */
        echo $tpl->toString();
    }

    private function _verifyNonce() {

        $this->_wpFunctions->check_admin_referer('tubepress-widget-nonce-save', 'tubepress-widget-nonce');
    }
}
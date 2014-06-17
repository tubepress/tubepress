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

class tubepress_wordpress_impl_listeners_wp_PublicActionsAndFilters
{
    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    /**
     * @var tubepress_core_theme_api_ThemeLibraryInterface
     */
    private $_themeLibrary;

    /**
     * @var tubepress_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    /**
     * @var tubepress_core_html_api_HtmlGeneratorInterface
     */
    private $_htmlGenerator;

    /**
     * @var tubepress_core_http_api_AjaxCommandInterface
     */
    private $_ajaxHandler;

    /**
     * @var tubepress_core_http_api_RequestParametersInterface
     */
    private $_requestParameters;

    /**
     * @var tubepress_core_options_api_PersistenceInterface
     */
    private $_persistence;

    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_shortcode_api_ParserInterface
     */
    private $_shortcodeParser;

    /**
     * @var tubepress_core_translation_api_TranslatorInterface
     */
    private $_translator;

    /**
     * @var tubepress_core_event_api_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(tubepress_wordpress_impl_wp_WpFunctions            $wpFunctions,
                                tubepress_core_theme_api_ThemeLibraryInterface     $themeLibrary,
                                tubepress_api_util_StringUtilsInterface            $stringUtils,
                                tubepress_core_html_api_HtmlGeneratorInterface     $htmlGenerator,
                                tubepress_core_http_api_AjaxCommandInterface       $ajaxHandler,
                                tubepress_core_http_api_RequestParametersInterface $requestParams,
                                tubepress_core_options_api_ContextInterface        $context,
                                tubepress_core_options_api_PersistenceInterface    $persistence,
                                tubepress_core_shortcode_api_ParserInterface       $parser,
                                tubepress_core_translation_api_TranslatorInterface $translator,
                                tubepress_core_event_api_EventDispatcherInterface  $eventDispatcher)
    {
        $this->_wpFunctions       = $wpFunctions;
        $this->_themeLibrary      = $themeLibrary;
        $this->_stringUtils       = $stringUtils;
        $this->_htmlGenerator     = $htmlGenerator;
        $this->_ajaxHandler       = $ajaxHandler;
        $this->_requestParameters = $requestParams;
        $this->_context           = $context;
        $this->_persistence       = $persistence;
        $this->_shortcodeParser   = $parser;
        $this->_translator        = $translator;
        $this->_eventDispatcher   = $eventDispatcher;
    }

    public function onAction_widgets_init(tubepress_core_event_api_EventInterface $event)
    {
        $widgetOps = array('classname' => 'widget_tubepress', 'description' =>
            $this->_translator->_('Displays YouTube or Vimeo videos with TubePress'));  //>(translatable)<

        $this->_wpFunctions->wp_register_sidebar_widget('tubepress', 'TubePress', array($this, 'printWidgetHtml'), $widgetOps);
        $this->_wpFunctions->wp_register_widget_control('tubepress', 'TubePress', array($this, 'printControlHtml'));
    }

    public function __fireWidgetHtmlEvent($widgetOpts)
    {
        $event = $this->_eventDispatcher->newEventInstance($widgetOpts);

        $this->_eventDispatcher->dispatch(tubepress_wordpress_api_Constants::EVENT_WIDGET_PUBLIC_HTML, $event);
    }

    public function __fireWidgetControlEvent()
    {
        $this->_eventDispatcher->dispatch(tubepress_wordpress_api_Constants::EVENT_WIDGET_PRINT_CONTROLS);
    }

    public function onAction_init(tubepress_core_event_api_EventInterface $event)
    {
        /* no need to queue any of this stuff up in the admin section or login page */
        if ($this->_wpFunctions->is_admin() || __FILE__ === 'wp-login.php') {

            return;
        }

        $baseName = basename(TUBEPRESS_ROOT);

        $tubePressJsUrl = $this->_wpFunctions->plugins_url("$baseName/src/core/html/web/js/tubepress.js", $baseName);
        $ajaxUrl        = $this->_wpFunctions->plugins_url("$baseName/src/core/wordpress/web/js/ajax.js", $baseName);

        $this->_wpFunctions->wp_register_script('tubepress', $tubePressJsUrl, array('jquery'));
        $this->_wpFunctions->wp_register_script('tubepress_ajax', $ajaxUrl, array('tubepress'));

        $this->_wpFunctions->wp_enqueue_script('jquery', false, array(), false, false);
        $this->_wpFunctions->wp_enqueue_script('tubepress', false, array(), false, false);
        $this->_wpFunctions->wp_enqueue_script('tubepress_ajax', false, array(), false, false);


        $this->_enqueueThemeResources($this->_wpFunctions);
    }

    public function onAction_wp_head(tubepress_core_event_api_EventInterface $event)
    {
        /* no need to print anything in the head of the admin section */
        if ($this->_wpFunctions->is_admin()) {

            return;
        }

        /* this inline JS helps initialize TubePress */
        print $this->_htmlGenerator->getCssHtml();
        print $this->_htmlGenerator->getJsHtml();
    }

    public function onAction_ajax(tubepress_core_event_api_EventInterface $event)
    {
        $this->_ajaxHandler->handle();
        exit;
    }

    public function onReadActionFromExternalInput(tubepress_core_event_api_EventInterface $event)
    {
        if (!$this->_requestParameters->hasParam('tubepress_wp_action')) {

            return;
        }

        $action = $this->_requestParameters->getParamValue('tubepress_wp_action');
        $event->setSubject($action);
    }

    /**
     * Filter the content (which may be empty).
     */
    public function onFilter_the_content(tubepress_core_event_api_EventInterface $event)
    {
        $content = $event->getSubject();

        /* do as little work as possible here 'cause we might not even run */
        $trigger = $this->_persistence->fetch(tubepress_core_shortcode_api_Constants::OPTION_KEYWORD);

        /* no shortcode? get out */
        if (!$this->_shortcodeParser->somethingToParse($content, $trigger)) {

            return;
        }

        $event->setSubject($this->_getHtml($content, $trigger));
    }

    private function _getHtml($content, $trigger)
    {
        /* Parse each shortcode one at a time */
        while ($this->_shortcodeParser->somethingToParse($content, $trigger)) {

            /* Get the HTML for this particular shortcode. Could be a single video or a gallery. */
            $generatedHtml = $this->_htmlGenerator->getHtmlForShortcode($content);

            /* remove any leading/trailing <p> tags from the content */
            $pattern = '/(<[P|p]>\s*)(' . preg_quote($this->_shortcodeParser->getLastShortcodeUsed(), '/') . ')(\s*<\/[P|p]>)/';
            $content = preg_replace($pattern, '${2}', $content);

            /* replace the shortcode with our new content */
            $currentShortcode = $this->_shortcodeParser->getLastShortcodeUsed();
            $content          = $this->_stringUtils->replaceFirst($currentShortcode, $generatedHtml, $content);
            $content          = $this->_stringUtils->removeEmptyLines($content);

            /* reset the context for the next shortcode */
            $this->_context->setEphemeralOptions(array());
        }

        return $content;
    }

    private function _enqueueThemeResources(tubepress_wordpress_impl_wp_WpFunctions $wpFunctions)
    {
        $styles       = $this->_themeLibrary->getStylesUrls();
        $scripts      = $this->_themeLibrary->getScriptsUrls();
        $styleCount   = count($styles);
        $scriptCount  = count($scripts);

        for ($x = 0; $x < $styleCount; $x++) {

            $handle = 'tubepress-theme-' . $x;

            $wpFunctions->wp_register_style($handle, $styles[$x]->toString());
            $wpFunctions->wp_enqueue_style($handle);
        }

        for ($x = 0; $x < $scriptCount; $x++) {

            if ($this->_stringUtils->endsWith($scripts[$x]->toString(), '/src/core/html/web/js/tubepress.js')) {

                //we already loaded this above
                continue;
            }

            $handle = 'tubepress-theme-' . $x;

            $wpFunctions->wp_register_script($handle, $scripts[$x]->toString());
            $wpFunctions->wp_enqueue_script($handle, false, array(), false, false);
        }
    }
}

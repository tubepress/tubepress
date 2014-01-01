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

class tubepress_addons_wordpress_impl_DefaultWidgetHandler implements tubepress_addons_wordpress_spi_WidgetHandler
{
    const WIDGET_CONTROL_SHORTCODE = 'widgetControlShortcode';
    const WIDGET_CONTROL_TITLE     = 'widgetControlTitle';
    const WIDGET_SHORTCODE         = 'widgetShortcode';
    const WIDGET_TITLE             = 'widgetTitle';
    const WIDGET_SUBMIT_TAG        = 'tubepress-widget-submit';

    /**
     * Registers ourselves as an admin menu.
     *
     * @return void
     */
    public final function registerWidget()
    {
        $msg               = tubepress_impl_patterns_sl_ServiceLocator::getMessageService();
        $wpFunctionWrapper = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);

        $widgetOps = array('classname' => 'widget_tubepress', 'description' =>
        $msg->_('Displays YouTube or Vimeo videos with TubePress'));  //>(translatable)<

        $wpFunctionWrapper->wp_register_sidebar_widget('tubepress', 'TubePress', array($this, 'printWidgetHtml'), $widgetOps);
        $wpFunctionWrapper->wp_register_widget_control('tubepress', 'TubePress', array($this, 'printControlHtml'));
    }

    /**
     * Registers all the styles and scripts for the front end.
     *
     * @param array $opts The options.
     *
     * @return void
     */
    public final function printWidgetHtml($opts)
    {
        extract($opts);

        $context      = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $parser       = tubepress_impl_patterns_sl_ServiceLocator::getShortcodeParser();
        $gallery      = tubepress_impl_patterns_sl_ServiceLocator::getShortcodeHtmlGenerator();

        /* default widget options */
        $defaultWidgetOptions = array(
            tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE  => 3,
            tubepress_api_const_options_names_Meta::VIEWS               => false,
            tubepress_api_const_options_names_Meta::DESCRIPTION         => true,
            tubepress_api_const_options_names_Meta::DESC_LIMIT          => 50,
            tubepress_api_const_options_names_Embedded::PLAYER_LOCATION => 'popup',
            tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT      => 105,
            tubepress_api_const_options_names_Thumbs::THUMB_WIDTH       => 135,
            tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE    => false,
            tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW    => false,
            tubepress_api_const_options_names_Thumbs::THEME             => 'sidebar',
            tubepress_api_const_options_names_Thumbs::FLUID_THUMBS      => false
        );

        /* now apply the user's options */
        $rawTag    = $context->get(tubepress_addons_wordpress_api_const_options_names_WordPress::WIDGET_SHORTCODE);
        $widgetTag = tubepress_impl_util_StringUtils::removeNewLines($rawTag);
        $parser->parse($widgetTag);

        /* calculate the final options */
        $finalOptions = array_merge($defaultWidgetOptions, $context->getCustomOptions());
        $context->setCustomOptions($finalOptions);

        if ($context->get(tubepress_api_const_options_names_Thumbs::THEME) === '') {

            $context->set(tubepress_api_const_options_names_Thumbs::THEME, 'sidebar');
        }

        try {

            $out = $gallery->getHtmlForShortcode('');

        } catch (Exception $e) {

            $out = $this->_dispatchErrorAndGetMessage($e);
        }

        /* do the standard WordPress widget dance */
        /** @noinspection PhpUndefinedVariableInspection */
        echo $before_widget . $before_title .
            $context->get(tubepress_addons_wordpress_api_const_options_names_WordPress::WIDGET_TITLE) .
            $after_title . $out . $after_widget;

        /* reset the context for the next shortcode */
        $context->reset();
    }

    /**
     * Filter the content (which may be empty).
     */
    public final function printControlHtml()
    {
        $wpsm         = tubepress_impl_patterns_sl_ServiceLocator::getOptionStorageManager();
        $msg          = tubepress_impl_patterns_sl_ServiceLocator::getMessageService();
        $tplBuilder   = tubepress_impl_patterns_sl_ServiceLocator::getTemplateBuilder();
        $hrps         = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();

        /* are we saving? */
        if ($hrps->hasParam(self::WIDGET_SUBMIT_TAG)) {

            self::_verifyNonce();

            $wpsm->queueForSave(tubepress_addons_wordpress_api_const_options_names_WordPress::WIDGET_SHORTCODE, $hrps->getParamValue('tubepress-widget-tagstring'));
            $wpsm->queueForSave(tubepress_addons_wordpress_api_const_options_names_WordPress::WIDGET_TITLE, $hrps->getParamValue('tubepress-widget-title'));

            $wpsm->flushSaveQueue();
        }

        /* load up the gallery template */
        $templatePath = TUBEPRESS_ROOT . '/src/main/php/add-ons/wordpress/resources/templates/widget_controls.tpl.php';
        $tpl          = $tplBuilder->getNewTemplateInstance($templatePath);

        /* set up the template */
        $tpl->setVariable(self::WIDGET_TITLE, $wpsm->fetch(tubepress_addons_wordpress_api_const_options_names_WordPress::WIDGET_TITLE));
        $tpl->setVariable(self::WIDGET_CONTROL_TITLE, $msg->_('Title'));                                                                                                            //>(translatable)<
        $tpl->setVariable(self::WIDGET_CONTROL_SHORTCODE, $msg->_('TubePress shortcode for the widget. See the <a href="http://tubepress.com/documentation" target="_blank">documentation</a>.')); //>(translatable)<
        $tpl->setVariable(self::WIDGET_SHORTCODE, $wpsm->fetch(tubepress_addons_wordpress_api_const_options_names_WordPress::WIDGET_SHORTCODE));
        $tpl->setVariable(self::WIDGET_SUBMIT_TAG, self::WIDGET_SUBMIT_TAG);

        /* get the template's output */
        echo $tpl->toString();
    }

    private static function _verifyNonce() {

        $wpFunctionWrapper = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);

        $wpFunctionWrapper->check_admin_referer('tubepress-widget-nonce-save', 'tubepress-widget-nonce');
    }

    private function _dispatchErrorAndGetMessage(Exception $e)
    {
        $event = new tubepress_spi_event_EventBase($e, array(
            'message' => $e->getMessage()
        ));

        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();

        $eventDispatcher->dispatch(tubepress_api_const_event_EventNames::ERROR_EXCEPTION_CAUGHT, $event);

        return $event->getArgument('message');
    }
}
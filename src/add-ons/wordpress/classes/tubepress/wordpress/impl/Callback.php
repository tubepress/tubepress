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

class tubepress_wordpress_impl_Callback
{
    /**
     * @var tubepress_lib_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_wordpress_impl_wp_ActivationHook
     */
    private $_activationHook;

    /**
     * @var tubepress_app_api_html_HtmlGeneratorInterface
     */
    private $_htmlGenerator;

    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_app_api_options_ReferenceInterface
     */
    private $_optionsReference;

    /**
     * @var array
     */
    private $_optionMapCache;

    public function __construct(tubepress_lib_api_event_EventDispatcherInterface   $eventDispatcher,
                                tubepress_app_api_options_ContextInterface         $context,
                                tubepress_app_api_html_HtmlGeneratorInterface      $htmlGenerator,
                                tubepress_app_api_options_ReferenceInterface       $optionsReference,
                                tubepress_wordpress_impl_wp_ActivationHook         $activationHook)
    {
        $this->_eventDispatcher  = $eventDispatcher;
        $this->_activationHook   = $activationHook;
        $this->_context          = $context;
        $this->_htmlGenerator    = $htmlGenerator;
        $this->_optionsReference = $optionsReference;
    }

    public function onFilter($filterName, array $args)
    {
        $subject = $args[0];
        $args    = count($args) > 1 ? array_slice($args, 1) : array();
        $event   = $this->_eventDispatcher->newEventInstance(

            $subject,
            array('args' => $args)
        );

        $this->_eventDispatcher->dispatch("tubepress.wordpress.filter.$filterName", $event);

        return $event->getSubject();
    }

    public function onAction($actionName, array $args)
    {
        $event = $this->_eventDispatcher->newEventInstance($args);

        $this->_eventDispatcher->dispatch("tubepress.wordpress.action.$actionName", $event);
    }

    public function onPluginActivation()
    {
        $this->_activationHook->execute();
    }

    public function onShortcode($optionMap, $content)
    {
        if (!is_array($optionMap)) {

            $optionMap = array();
        }

        $normalizedOptions = $this->_normalizeIncomingShortcodeOptionMap($optionMap);

        $this->_context->setEphemeralOptions($normalizedOptions);

        $event = $this->_buildShortcodeEvent($normalizedOptions, $content);
        $this->_eventDispatcher->dispatch(tubepress_wordpress_api_Constants::SHORTCODE_PARSED, $event);

        /* Get the HTML for this particular shortcode. */
        $toReturn = $this->_htmlGenerator->getHtml();

        /* reset the context for the next shortcode */
        $this->_context->setEphemeralOptions(array());

        return $toReturn;
    }

    private function _buildShortcodeEvent(array $normalizedOptions, $innerContent)
    {
        if (!$innerContent) {

            $innerContent = null;
        }

        $name      = $this->_context->get(tubepress_app_api_options_Names::SHORTCODE_KEYWORD);
        $shortcode = new tubepress_lib_impl_shortcode_Shortcode($name, $normalizedOptions, $innerContent);

        return $this->_eventDispatcher->newEventInstance($shortcode);
    }

    private function _normalizeIncomingShortcodeOptionMap(array $optionMap)
    {
        if (!isset($this->_optionMapCache)) {

            $this->_optionMapCache = array();
            $allKnownOptionNames   = $this->_optionsReference->getAllOptionNames();

            foreach ($allKnownOptionNames as $camelCaseOptionName) {

                $asLowerCase                         = strtolower($camelCaseOptionName);
                $this->_optionMapCache[$asLowerCase] = $camelCaseOptionName;
            }
        }

        $toReturn = array();

        foreach ($optionMap as $lowerCaseCandidate => $value) {

            if (isset($this->_optionMapCache[$lowerCaseCandidate])) {

                $camelCaseOptionName            = $this->_optionMapCache[$lowerCaseCandidate];
                $toReturn[$camelCaseOptionName] = $value;
            }
        }

        return $toReturn;
    }
}
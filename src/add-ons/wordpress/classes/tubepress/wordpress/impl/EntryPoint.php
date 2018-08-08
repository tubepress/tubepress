<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_wordpress_impl_EntryPoint
{
    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    /**
     * @var tubepress_api_options_PersistenceInterface
     */
    private $_persistence;

    /**
     * @var tubepress_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_loggingEnabled;

    /**
     * @var string The base plugin directory.
     */
    private $_baseName;

    /**
     * @var string[]
     */
    private $_actions;

    /**
     * @var array[]
     */
    private $_filters;

    /**
     * @var bool
     */
    private $_testMode = false;

    public function __construct(tubepress_wordpress_impl_wp_WpFunctions      $wpFunctions,
                                tubepress_api_options_PersistenceInterface   $persistence,
                                tubepress_api_log_LoggerInterface            $logger,
                                tubepress_api_event_EventDispatcherInterface $eventDispatcher,
                                array $actions, array $filters)
    {
        $this->_baseName        = basename(TUBEPRESS_ROOT);
        $this->_wpFunctions     = $wpFunctions;
        $this->_persistence     = $persistence;
        $this->_actions         = $actions;
        $this->_filters         = $filters;
        $this->_logger          = $logger;
        $this->_eventDispatcher = $eventDispatcher;
        $this->_loggingEnabled  = $logger->isEnabled();
    }

    /**
     * Called when TubePress is loaded as a plugin. Adds the appropriate
     * filter and action callbacks.
     */
    public function start()
    {
        if ($this->_loggingEnabled) {

            $this->_logDebug('Hooking into WordPress');
        }

        $this->_loadPluginTextDomain();
        $this->_addFilterListener();
        $this->_addActionListener();
        $this->_addActivationListener();
        $this->_addShortcodeListener();
        $this->_addUpdateChecker();

        if ($this->_loggingEnabled) {

            $this->_logDebug('Done hooking into WordPress');
        }
    }

    public function callback_onShortcode()
    {
        try {

            $event = $this->_dispatch(

                tubepress_wordpress_api_Constants::EVENT_SHORTCODE_FOUND,
                func_get_args()
            );

            if (!$event->hasArgument('result') || !is_string($event->getArgument('result'))) {

                throw new \RuntimeException(sprintf(
                    '<code>%s</code> event did not return a string',
                    tubepress_wordpress_api_Constants::EVENT_SHORTCODE_FOUND
                ));
            }

            return $event->getArgument('result');

        } catch (Exception $e) {

            $this->_logger->error($e->getMessage());

            return '';
        }
    }

    public function callback_onFilter()
    {
        try {

            $currentFilter = $this->_wpFunctions->current_filter();
            $funcArgs      = func_get_args();
            $funcArgCount  = count($funcArgs);
            $eventName     = "tubepress.wordpress.filter.$currentFilter";

            if ($this->_loggingEnabled) {

                $this->_logDebug(sprintf(
                    'WordPress filter <code>%s</code> invoked with <code>%d</code> argument(s). We will re-dispatch as <code>%s</code>.',
                    $currentFilter, $funcArgCount, $eventName
                ));
            }

            $subject = $funcArgs[0];
            $args    = $funcArgCount > 1 ? array_slice($funcArgs, 1) : array();
            $event   = $this->_dispatch($eventName, $subject, array('args' => $args));

            return $event->getSubject();

        } catch (Exception $e) {

            $this->_logger->error($e->getMessage());

            return func_get_arg(0);
        }
    }

    public function callback_onAction()
    {
        try {

            $currentAction = $this->_wpFunctions->current_filter();
            $args          = func_get_args();
            $eventName     = "tubepress.wordpress.action.$currentAction";

            if ($this->_loggingEnabled) {

                $this->_logDebug(sprintf(
                    'WordPress action <code>%s</code> invoked with <code>%d</code> argument(s). We will re-dispatch as <code>%s</code>.',
                    $currentAction, count($args), $eventName
                ));
            }

            $this->_dispatch($eventName, $args);

        } catch (Exception $e) {

            $this->_logger->error($e->getMessage());
        }
    }

    public function callback_onActivation()
    {
        try {

            $this->_dispatch(
                tubepress_wordpress_api_Constants::EVENT_PLUGIN_ACTIVATION,
                func_get_args()
            );

        } catch (\Exception $e) {

            $this->_logger->error($e->getMessage());
        }
    }

    /**
     * DO NOT CALL THIS OUTSIDE OF TESTING.
     *
     * @internal
     */
    public function __enableTestMode()
    {
        $this->_testMode = true;
    }

    private function _loadPluginTextDomain()
    {
        $this->_wpFunctions->load_plugin_textdomain(
            'tubepress',
            false,
            $this->_baseName . '/src/translations'
        );
    }

    private function _addFilterListener()
    {
        $filterCallback = array($this, 'callback_onFilter');

        foreach ($this->_filters as $filterData) {

            if (!is_array($filterData)) {

                throw new \InvalidArgumentException('Filter data must be an array');
            }

            $this->_addFilterOrActionToWordPress($filterData, $filterCallback, 'add_filter');
        }
    }

    private function _addActionListener()
    {
        $actionCallback = array($this, 'callback_onAction');

        foreach ($this->_actions as $actionData) {

            $this->_addFilterOrActionToWordPress($actionData, $actionCallback, 'add_action');
        }
    }

    private function _addFilterOrActionToWordPress($incoming, $callback, $method)
    {
        $priority = 10;
        $argCount = 1;

        if (is_array($incoming)) {

            $dataCount = count($incoming);

            if ($dataCount < 1 || $dataCount > 3) {

                throw new InvalidArgumentException('Filter or action data must be an array of size 1 to 3');
            }

            if (!is_string($incoming[0])) {

                throw new \InvalidArgumentException('One of your requested filters or actions has a non-string name');
            }

            $name = $incoming[0];

            switch ($dataCount) {

                case 3:

                    $priority = intval($incoming[1]);
                    $argCount = intval($incoming[2]);
                    break;

                case 2:

                    $priority = intval($incoming[1]);
                    $argCount = 1;
                    break;

                default:

                    break;
            }

            $filterOrActionName = $name;

        } else {

            $filterOrActionName = "$incoming";
        }

        if ($this->_loggingEnabled) {

            $this->_logDebug(sprintf('
                <code>%s()</code> for <code>%s</code> with priority <code>%d</code> and <code>%d</code> argument(s)',
                $method, $filterOrActionName, $priority, $argCount
            ));
        }

        $this->_wpFunctions->$method($filterOrActionName, $callback, $priority, $argCount);
    }

    private function _addActivationListener()
    {
        $this->_wpFunctions->register_activation_hook(

            $this->_baseName . '/tubepress.php',
            array($this, 'callback_onActivation')
        );
    }

    private function _addShortcodeListener()
    {
        $keyword = $this->_persistence->fetch(tubepress_api_options_Names::SHORTCODE_KEYWORD);

        $this->_wpFunctions->add_shortcode(

            $keyword,
            array($this, 'callback_onShortcode')
        );
    }

    private function _addUpdateChecker()
    {
        require TUBEPRESS_ROOT . '/vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php';

        if (!$this->_testMode) {

            PucFactory::buildUpdateChecker(

                'http://snippets.wp.tubepress.com/update.php',
                TUBEPRESS_ROOT . '/tubepress.php',
                'tubepress'
            );
        }
    }

    /**
     * @param      $eventName
     * @param      $subject
     * @param null $args
     *
     * @return tubepress_api_event_EventInterface
     */
    private function _dispatch($eventName, $subject, $args = null)
    {
        $event = $this->_eventDispatcher->newEventInstance($subject);

        if ($args) {

            $event->setArguments($args);
        }

        if ($this->_loggingEnabled) {

            $this->_logDebug(sprintf('Start dispatch of event <code>%s</code>', $eventName));
        }

        $this->_eventDispatcher->dispatch($eventName, $event);

        if ($this->_loggingEnabled) {

            $this->_logDebug(sprintf('End dispatch of event <code>%s</code>', $eventName));
        }

        return $event;
    }

    private function _logDebug($msg)
    {
        $this->_logger->debug(sprintf('(WordPress Entry Point) %s', $msg));
    }
}

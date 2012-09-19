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
 * Primary bootstrapping for TubePress.
 */
function bootTubePress()
{
    /**
     * First, record the root path.
     */
    define('TUBEPRESS_ROOT', realpath(dirname(__FILE__) . '/../../../../'));

    /**
     * Second, we add our classloader.
     */
    require_once TUBEPRESS_ROOT . '/vendor/ehough/pulsar/src/main/php/ehough/pulsar/ComposerClassLoader.php';

    $loader = new ehough_pulsar_ComposerClassLoader(TUBEPRESS_ROOT . '/vendor/');
    $loader->registerFallbackDirectory(TUBEPRESS_ROOT . '/src/main/php/classes');
    $loader->register();

    /**
     * Next, set up logging.
     */
    if (isset($_GET['tubepress_debug']) && strcasecmp($_GET['tubepress_debug'], 'true') === 0) {

        $handler = new ehough_epilog_impl_handler_PrintHandler();
        $handler->setLevel(ehough_epilog_api_ILogger::DEBUG);

        ehough_epilog_api_LoggerFactory::setHandlerStack(array($handler));
    }

    /*
     * Now build the core IOC container. This is the same for everyone.
     */
    $coreIocContainer = new tubepress_impl_patterns_ioc_CoreIocContainer();

    tubepress_impl_patterns_ioc_KernelServiceLocator::setBootstrapper(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_BOOTSTRAPPER)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setCacheService(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_CACHE)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setEnvironmentDetector(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_ENVIRONMENT_DETECTOR)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setEventDispatcher(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_EVENT_DISPATCHER)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_EXECUTIION_CONTEXT)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setFeedFetcher(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_FEED_FETCHER)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setFeedInspector(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_FEED_INSPECTOR)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setFileSystem(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_FILESYSTEM)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setFileSystemFinderFactory(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_FILESYSTEM_FINDER_FACTORY)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpClient(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_HTTP_CLIENT)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpResponseHandler(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_HTTP_RESPONSE_HANDLER)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpRequestParameterService(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_HTTP_REQUEST_PARAMS)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setMessageService(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_MESSAGE)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionsUiFieldBuilder(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_OPTIONS_UI_FIELDBUILDER)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionsUiFormHandler(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_OPTIONS_UI_FORMHANDLER)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionDescriptorReference(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_OPTION_DESCRIPTOR_REFERENCE)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionStorageManager(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_OPTION_STORAGE_MANAGER)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionValidator(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_OPTION_VALIDATOR)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setPlayerHtmlGenerator(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_PLAYER_HTML_GENERATOR)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setShortcodeHtmlGenerator(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_SHORTCODE_HTML_GENERATOR)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setShortcodeHtmlParser(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_SHORTCODE_PARSER)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setThemeHandler(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_THEME_HANDLER)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setUrlBuilder(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_URL_BUILDER)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setVideoFactory(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_VIDEO_FACTORY)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setVideoProvider(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_VIDEO_PROVIDER)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setVideoProviderCalculator(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_VIDEO_PROVIDER_CALCULATOR)
    );
    tubepress_impl_patterns_ioc_KernelServiceLocator::setWordPressFunctionWrapper(
        $coreIocContainer->get(tubepress_impl_patterns_ioc_CoreIocContainer::SERVICE_WORDPRESS_FUNCTION_WRAPPER)
    );

    /*
     * Finally, hand off control to the TubePress bootstrapper.
     */
    tubepress_impl_patterns_ioc_KernelServiceLocator::getBootStrapper()->boot();
}

/*
 * Don't boot twice.
 */
if (!defined('TUBEPRESS_BOOTED')) {

    bootTubePress();

    define('TUBEPRESS_BOOTED', true);
}

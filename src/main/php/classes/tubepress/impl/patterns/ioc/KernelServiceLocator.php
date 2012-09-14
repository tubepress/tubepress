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
 * A service locator for kernel services.
 */
class tubepress_impl_patterns_ioc_KernelServiceLocator
{
    /**
     * @var tubepress_spi_environment_EnvironmentDetector
     */
    private static $_environmentDetector;

    /**
     * @var ehough_tickertape_api_IEventDispatcher
     */
    private static $_eventDispatcher;

    /**
     * @var tubepress_spi_context_ExecutionContext
     */
    private static $_executionContext;

    /**
     * @var ehough_fimble_api_FileSystem
     */
    private static $_fileSystem;

    /**
     * @var ehough_fimble_api_FinderFactory
     */
    private static $_fileSystemFinderFactory;

    /**
     * @var tubepress_spi_http_HttpRequestParameterService
     */
    private static $_httpRequestParameterService;

    /**
     * @var tubepress_spi_message_MessageService
     */
    private static $_messageService;

    /**
     * @var tubepress_spi_options_ui_FieldBuilder
     */
    private static $_optionsUiFieldBuilder;

    /**
     * @var tubepress_spi_options_ui_FormHandler
     */
    private static $_optionsUiFormHandler;

    /**
     * @var tubepress_spi_options_OptionDescriptorReference
     */
    private static $_optionDescriptorReference;

    /**
     * @var tubepress_spi_options_StorageManager
     */
    private static $_optionStorageManager;

    /**
     * @var tubepress_spi_options_OptionValidator
     */
    private static $_optionValidator;

    /**
     * @var tubepress_spi_player_PlayerHtmlGenerator
     */
    private static $_playerHtmlGenerator;

    /**
     * @var tubepress_spi_shortcode_ShortcodeHtmlGenerator
     */
    private static $_shortcodeHtmlGenerator;

    /**
     * @var tubepress_spi_shortcode_ShortcodeParser
     */
    private static $_shortcodeParser;

    /**
     * @var tubepress_spi_theme_ThemeHandler
     */
    private static $_themeHandler;

    /**
     * @var tubepress_spi_provider_Provider
     */
    private static $_videoProvider;

    /**
     * @var tubepress_spi_provider_ProviderCalculator
     */
    private static $_videoProviderCalculator;

    /**
     * @var tubepress_spi_wordpress_WordPressFunctionWrapper
     */
    private static $_wordPressFunctionWrapper;

    /**
     * @return tubepress_spi_environment_EnvironmentDetector The environment detector.
     */
    public static function getEnvironmentDetector()
    {
        return self::$_environmentDetector;
    }

    /**
     * @return ehough_tickertape_api_IEventDispatcher The event dispatcher.
     */
    public static function getEventDispatcher()
    {
        return self::$_eventDispatcher;
    }

    /**
     * @return tubepress_spi_context_ExecutionContext The execution context.
     */
    public static function getExecutionContext()
    {
        return self::$_executionContext;
    }

    /**
     * @return ehough_fimble_api_FileSystem The filesystem service.
     */
    public static function getFileSystem()
    {
        return self::$_fileSystem;
    }

    /**
     * @return ehough_fimble_api_FinderFactory The finder factory.
     */
    public static function getFileSystemFinderFactory()
    {
        return self::$_fileSystemFinderFactory;
    }

    /**
     * @return tubepress_spi_http_HttpRequestParameterService The HTTP request parameter service.
     */
    public static function getHttpRequestParameterService()
    {
        return self::$_httpRequestParameterService;
    }

    /**
     * @return tubepress_spi_message_MessageService The message service.
     */
    public static function getMessageService()
    {
        return self::$_messageService;
    }

    /**
     * @return tubepress_spi_options_ui_FieldBuilder The options UI field builder.
     */
    public static function getOptionsUiFieldBuilder()
    {
        return self::$_optionsUiFieldBuilder;
    }

    /**
     * @return tubepress_spi_options_ui_FormHandler
     */
    public static function getOptionsUiFormHandler()
    {
        return self::$_optionsUiFormHandler;
    }

    /**
     * @return tubepress_spi_options_OptionDescriptorReference The option descriptor reference.
     */
    public static function getOptionDescriptorReference()
    {
        return self::$_optionDescriptorReference;
    }

    /**
     * @return tubepress_spi_options_StorageManager The option storage manager.
     */
    public static function getOptionStorageManager()
    {
        return self::$_optionStorageManager;
    }

    /**
     * @return tubepress_spi_options_OptionValidator The option validator.
     */
    public static function getOptionValidator()
    {
        return self::$_optionValidator;
    }

    /**
     * @return tubepress_spi_player_PlayerHtmlGenerator The player HTML generator.
     */
    public static function getPlayerHtmlGenerator()
    {
        return self::$_playerHtmlGenerator;
    }

    /**
     * @return tubepress_spi_shortcode_ShortcodeHtmlGenerator The shortcode HTML generator.
     */
    public static function getShortcodeHtmlGenerator()
    {
        return self::$_shortcodeHtmlGenerator;
    }

    /**
     * @return tubepress_spi_shortcode_ShortcodeParser The shortcode parser.
     */
    public static function getShortcodeParser()
    {
        return self::$_shortcodeParser;
    }

    /**
     * @return tubepress_spi_theme_ThemeHandler The theme handler.
     */
    public static function getThemeHandler()
    {
        return self::$_themeHandler;
    }

    /**
     * @return tubepress_spi_provider_Provider The video provider.
     */
    public static function getVideoProvider()
    {
        return self::$_videoProvider;
    }

    /**
     * @return tubepress_spi_provider_ProviderCalculator The video provider calculator.
     */
    public static function getVideoProviderCalculator()
    {
        return self::$_videoProviderCalculator;
    }

    /**
     * @return tubepress_spi_wordpress_WordPressFunctionWrapper The WP function wrapper.
     */
    public static function getWordPressFunctionWrapper()
    {
        return self::$_wordPressFunctionWrapper;
    }

    /**
     * @param tubepress_spi_environment_EnvironmentDetector $environmentDetector The environment detector.
     */
    public static function setEnvironmentDetector(tubepress_spi_environment_EnvironmentDetector $environmentDetector)
    {
        self::$_environmentDetector = $environmentDetector;
    }

    /**
     * @param ehough_tickertape_api_IEventDispatcher $eventDispatcher The event dispatcher.
     */
    public static function setEventDispatcher(ehough_tickertape_api_IEventDispatcher $eventDispatcher)
    {
        self::$_eventDispatcher = $eventDispatcher;
    }

    /**
     * @param tubepress_spi_context_ExecutionContext $executionContext The execution context.
     */
    public static function setExecutionContext(tubepress_spi_context_ExecutionContext $executionContext)
    {
        self::$_executionContext = $executionContext;
    }

    /**
     * @param ehough_fimble_api_Filesystem $fileSystem The filesystem.
     */
    public static function setFileSystem(ehough_fimble_api_Filesystem $fileSystem)
    {
        self::$_fileSystem = $fileSystem;
    }

    /**
     * @param ehough_fimble_api_FinderFactory $finderFactory The finder factory.
     */
    public static function setFileSystemFinderFactory(ehough_fimble_api_FinderFactory $finderFactory)
    {
        self::$_fileSystemFinderFactory = $finderFactory;
    }

    /**
     * @param tubepress_spi_http_HttpRequestParameterService $httpRequestParameterService The HTTP request parameter service.
     */
    public static function setHttpRequestParameterService(tubepress_spi_http_HttpRequestParameterService $httpRequestParameterService)
    {
        self::$_httpRequestParameterService = $httpRequestParameterService;
    }

    /**
     * @param tubepress_spi_message_MessageService $messageService The message service.
     */
    public static function setMessageService(tubepress_spi_message_MessageService $messageService)
    {
        self::$_messageService = $messageService;
    }

    /**
     * @param tubepress_spi_options_ui_FieldBuilder $fieldBuilder The options UI field builder.
     */
    public static function setOptionsUiFieldBuilder(tubepress_spi_options_ui_FieldBuilder $fieldBuilder)
    {
        self::$_optionsUiFieldBuilder = $fieldBuilder;
    }

    /**
     * @param tubepress_spi_options_ui_FormHandler $formHandler The options UI form handler.
     */
    public static function setOptionsUiFormHandler(tubepress_spi_options_ui_FormHandler $formHandler)
    {
        self::$_optionsUiFormHandler = $formHandler;
    }

    /**
     * @param tubepress_spi_options_OptionDescriptorReference $optionDescriptorReference The option descriptor reference.
     */
    public static function setOptionDescriptorReference(tubepress_spi_options_OptionDescriptorReference $optionDescriptorReference)
    {
        self::$_optionDescriptorReference = $optionDescriptorReference;
    }

    /**
     * @param tubepress_spi_options_StorageManager $optionStorageManager The option storage manager.
     */
    public static function setOptionStorageManager(tubepress_spi_options_StorageManager $optionStorageManager)
    {
        self::$_optionStorageManager = $optionStorageManager;
    }

    /**
     * @param tubepress_spi_options_OptionValidator $optionValidator The option validator.
     */
    public static function setOptionValidator(tubepress_spi_options_OptionValidator $optionValidator)
    {
        self::$_optionValidator = $optionValidator;
    }

    /**
     * @param tubepress_spi_player_PlayerHtmlGenerator $playerHtmlGenerator The player HTML generator.
     */
    public static function setPlayerHtmlGenerator(tubepress_spi_player_PlayerHtmlGenerator $playerHtmlGenerator)
    {
        self::$_playerHtmlGenerator = $playerHtmlGenerator;
    }

    /**
     * @param tubepress_spi_shortcode_ShortcodeHtmlGenerator $shortcodeHtmlGenerator The shortcode HTML generator.
     */
    public static function setShortcodeHtmlGenerator(tubepress_spi_shortcode_ShortcodeHtmlGenerator $shortcodeHtmlGenerator)
    {
        self::$_shortcodeHtmlGenerator = $shortcodeHtmlGenerator;
    }

    /**
     * @param tubepress_spi_shortcode_ShortcodeParser $shortcodeParser The shortcode parser.
     */
    public static function setShortcodeHtmlParser(tubepress_spi_shortcode_ShortcodeParser $shortcodeParser)
    {
        self::$_shortcodeParser = $shortcodeParser;
    }

    /**
     * @param tubepress_spi_theme_ThemeHandler $themeHandler The theme handler.
     */
    public static function setThemeHandler(tubepress_spi_theme_ThemeHandler $themeHandler)
    {
        self::$_themeHandler = $themeHandler;
    }

    /**
     * @param tubepress_spi_provider_Provider $videoProvider The video provider.
     */
    public static function setVideoProvider(tubepress_spi_provider_Provider $videoProvider)
    {
        self::$_videoProvider = $videoProvider;
    }

    /**
     * @param tubepress_spi_provider_ProviderCalculator $videoProviderCalculator The video provider calculator.
     */
    public static function setVideoProviderCalculator(tubepress_spi_provider_ProviderCalculator $videoProviderCalculator)
    {
        self::$_videoProviderCalculator = $videoProviderCalculator;
    }

    /**
     * @param tubepress_spi_wordpress_WordPressFunctionWrapper $wordPressFunctionWrapper The WP function wrapper.
     */
    public static function setWordPressFunctionWrapper(tubepress_spi_wordpress_WordPressFunctionWrapper $wordPressFunctionWrapper)
    {
        self::$_wordPressFunctionWrapper = $wordPressFunctionWrapper;
    }
}

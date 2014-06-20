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

if (!defined('TUBEPRESS_ROOT')) {

    define('TUBEPRESS_ROOT', realpath(__DIR__ . '/../../'));
    require TUBEPRESS_ROOT . '/vendor/autoload.php';
}

class tubepress_build_ClassCollectionBuilder
{
    private static $loaded;
    private static $seen;

    public static function build()
    {
        @unlink(TUBEPRESS_ROOT . "/src/platform/scripts/classloading/commonly-used-classes.php");

        self::load(
            self::$CLASSES,
            TUBEPRESS_ROOT . '/src/platform/scripts/classloading',
            'commonly-used-classes',
            false
        );
    }

    /**
     * Loads a list of classes and caches them in one big file.
     *
     * @param array   $classes    An array of classes to load
     * @param string  $cacheDir   A cache directory
     * @param string  $name       The cache name prefix
     * @param bool    $autoReload Whether to flush the cache when the cache is stale or not
     * @param bool    $adaptive   Whether to remove already declared classes or not
     * @param string  $extension  File extension of the resulting file
     *
     * @throws InvalidArgumentException When class can't be loaded
     */
    public static function load($classes, $cacheDir, $name, $autoReload, $adaptive = false, $extension = '.php')
    {
        // each $name can only be loaded once per PHP process
        if (isset(self::$loaded[$name])) {
            return;
        }

        self::$loaded[$name] = true;

        $declared = array_merge(get_declared_classes(), get_declared_interfaces());
        if (function_exists('get_declared_traits')) {
            $declared = array_merge($declared, get_declared_traits());
        }

        if ($adaptive) {
            // don't include already declared classes
            $classes = array_diff($classes, $declared);

            // the cache is different depending on which classes are already declared
            $name = $name.'-'.substr(hash('sha256', implode('|', $classes)), 0, 5);
        }

        $classes = array_unique($classes);

        $cache = $cacheDir.'/'.$name.$extension;

        // auto-reload
        $reload = false;
        if ($autoReload) {
            $metadata = $cache.'.meta';
            if (!is_file($metadata) || !is_file($cache)) {
                $reload = true;
            } else {
                $time = filemtime($cache);
                $meta = unserialize(file_get_contents($metadata));

                sort($meta[1]);
                sort($classes);

                if ($meta[1] != $classes) {
                    $reload = true;
                } else {
                    foreach ($meta[0] as $resource) {
                        if (!is_file($resource) || filemtime($resource) > $time) {
                            $reload = true;

                            break;
                        }
                    }
                }
            }
        }

        if (!$reload && is_file($cache)) {
            require_once $cache;

            return;
        }

        $files = array();
        $orderedClasses = self::getOrderedClasses($classes);
        $classCount     = count($orderedClasses);
        $content = <<<EOT

/**
 * For performance purposes, this is a concatenation of the following $classCount classes:
 *

EOT;
        foreach ($orderedClasses as $class) {
            $content .= " *\t\t" . $class->getName() . "\n";
        }
        $content .= " */\n";
        foreach ($orderedClasses as $class) {
            if (in_array($class->getName(), $declared)) {
                continue;
            }

            $files[] = $class->getFileName();

            $c = preg_replace(array('/^\s*<\?php/', '/\?>\s*$/'), '', file_get_contents($class->getFileName()));

            $c = self::fixNamespaceDeclarations('<?php '.$c);
            $c = preg_replace('/^\s*<\?php/', '', $c);

            $content .= $c;
        }

        // cache the core classes
        if (!is_dir(dirname($cache))) {
            mkdir(dirname($cache), 0777, true);
        }
        self::writeCacheFile($cache, '<?php '.$content);

        if ($autoReload) {
            // save the resources
            self::writeCacheFile($metadata, serialize(array($files, $classes)));
        }
    }

    /**
     * Adds brackets around each namespace if it's not already the case.
     *
     * @param string $source Namespace string
     *
     * @return string Namespaces with brackets
     */
    public static function fixNamespaceDeclarations($source)
    {
        $rawChunk = '';
        $output = '';
        $inNamespace = false;
        $tokens = token_get_all($source);

        for (reset($tokens); false !== $token = current($tokens); next($tokens)) {
            if (is_string($token)) {
                $rawChunk .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                // strip comments
                continue;
            } elseif (T_START_HEREDOC === $token[0]) {
                $output .= self::compressCode($rawChunk).$token[1];
                do {
                    $token = next($tokens);
                    $output .= is_string($token) ? $token : $token[1];
                } while ($token[0] !== T_END_HEREDOC);
                $output .= "\n";
                $rawChunk = '';
            } elseif (T_CONSTANT_ENCAPSED_STRING === $token[0]) {
                $output .= self::compressCode($rawChunk).$token[1];
                $rawChunk = '';
            } else {
                $rawChunk .= $token[1];
            }
        }

        if ($inNamespace) {
            $rawChunk .= "}\n";
        }

        return $output.self::compressCode($rawChunk);
    }

    /**
     * Strips leading & trailing ws, multiple EOL, multiple ws.
     *
     * @param string $code Original PHP code
     *
     * @return string compressed code
     */
    private static function compressCode($code)
    {
        return preg_replace(
            array('/^\s+/m', '/\s+$/m', '/([\n\r]+ *[\n\r]+)+/', '/[ \t]+/'),
            array('', '', "\n", ' '),
            $code
        );
    }

    /**
     * Writes a cache file.
     *
     * @param string $file    Filename
     * @param string $content Temporary file content
     *
     * @throws RuntimeException when a cache file cannot be written
     */
    private static function writeCacheFile($file, $content)
    {
        $tmpFile = tempnam(dirname($file), basename($file));
        if (false !== @file_put_contents($tmpFile, $content) && @rename($tmpFile, $file)) {
            @chmod($file, 0666 & ~umask());

            return;
        }

        throw new RuntimeException(sprintf('Failed to write cache file "%s".', $file));
    }

    /**
     * Gets an ordered array of passed classes including all their dependencies.
     *
     * @param array $classes
     *
     * @return ReflectionClass[] An array of sorted ReflectionClass instances (dependencies added if needed)
     *
     * @throws InvalidArgumentException When a class can't be loaded
     */
    private static function getOrderedClasses(array $classes)
    {
        $map = array();
        self::$seen = array();
        foreach ($classes as $class) {
            try {
                $reflectionClass = new ReflectionClass($class);
            } catch (ReflectionException $e) {
                throw new InvalidArgumentException(sprintf('Unable to load class "%s"', $class));
            }

            $map = array_merge($map, self::getClassHierarchy($reflectionClass));
        }

        return $map;
    }

    private static function getClassHierarchy(ReflectionClass $class)
    {
        if (isset(self::$seen[$class->getName()])) {
            return array();
        }

        self::$seen[$class->getName()] = true;

        $classes = array($class);
        $parent = $class;
        while (($parent = $parent->getParentClass()) && $parent->isUserDefined() && !isset(self::$seen[$parent->getName()])) {
            self::$seen[$parent->getName()] = true;

            array_unshift($classes, $parent);
        }

        $traits = array();

        if (function_exists('get_declared_traits')) {
            foreach ($classes as $c) {
                foreach (self::resolveDependencies(self::computeTraitDeps($c), $c) as $trait) {
                    if ($trait !== $c) {
                        $traits[] = $trait;
                    }
                }
            }
        }

        return array_merge(self::getInterfaces($class), $traits, $classes);
    }

    private static function getInterfaces(ReflectionClass $class)
    {
        $classes = array();

        foreach ($class->getInterfaces() as $interface) {
            $classes = array_merge($classes, self::getInterfaces($interface));
        }

        if ($class->isUserDefined() && $class->isInterface() && !isset(self::$seen[$class->getName()])) {
            self::$seen[$class->getName()] = true;

            $classes[] = $class;
        }

        return $classes;
    }

    private static function computeTraitDeps(ReflectionClass $class)
    {
        $traits = $class->getTraits();
        $deps = array($class->getName() => $traits);
        while ($trait = array_pop($traits)) {
            if ($trait->isUserDefined() && !isset(self::$seen[$trait->getName()])) {
                self::$seen[$trait->getName()] = true;
                $traitDeps = $trait->getTraits();
                $deps[$trait->getName()] = $traitDeps;
                $traits = array_merge($traits, $traitDeps);
            }
        }

        return $deps;
    }

    /**
     * Dependencies resolution.
     *
     * This function does not check for circular dependencies as it should never
     * occur with PHP traits.
     *
     * @param array             $tree       The dependency tree
     * @param ReflectionClass  $node       The node
     * @param ArrayObject      $resolved   An array of already resolved dependencies
     * @param ArrayObject      $unresolved An array of dependencies to be resolved
     *
     * @return ArrayObject The dependencies for the given node
     *
     * @throws RuntimeException if a circular dependency is detected
     */
    private static function resolveDependencies(array $tree, $node, ArrayObject $resolved = null, ArrayObject $unresolved = null)
    {
        if (null === $resolved) {
            $resolved = new ArrayObject();
        }
        if (null === $unresolved) {
            $unresolved = new ArrayObject();
        }
        $nodeName = $node->getName();
        $unresolved[$nodeName] = $node;
        foreach ($tree[$nodeName] as $dependency) {
            if (!$resolved->offsetExists($dependency->getName())) {
                self::resolveDependencies($tree, $dependency, $resolved, $unresolved);
            }
        }
        $resolved[$nodeName] = $node;
        unset($unresolved[$nodeName]);

        return $resolved;
    }

    public static $CLASSES = array(

        'ehough_contemplate_api_Template',
        'ehough_contemplate_impl_SimpleTemplate',
        'ehough_filesystem_Filesystem',
        'ehough_filesystem_FilesystemInterface',
        'ehough_iconic_Container',
        'ehough_iconic_ContainerInterface',
        'ehough_iconic_IntrospectableContainerInterface',
        'ehough_stash_Item',
        'ehough_stash_Pool',
        'ehough_stash_Utilities',
        'ehough_stash_driver_FileSystem',
        'ehough_stash_interfaces_DriverInterface',
        'ehough_stash_interfaces_ItemInterface',
        'ehough_stash_interfaces_PoolInterface',
        'ehough_tickertape_ContainerAwareEventDispatcher',
        'ehough_tickertape_Event',
        'ehough_tickertape_EventDispatcher',
        'ehough_tickertape_EventDispatcherInterface',
        'ehough_tickertape_GenericEvent',
        'puzzle_AbstractHasData',
        'puzzle_Client',
        'puzzle_ClientInterface',
        'puzzle_Collection',
        'puzzle_Query',
        'puzzle_ToArrayInterface',
        'puzzle_Url',
        'puzzle_adapter_AdapterInterface',
        'puzzle_adapter_ParallelAdapterInterface',
        'puzzle_adapter_StreamAdapter',
        'puzzle_adapter_StreamingProxyAdapter',
        'puzzle_adapter_curl_CurlFactory',
        'puzzle_adapter_curl_MultiAdapter',
        'puzzle_event_Emitter',
        'puzzle_event_EmitterInterface',
        'puzzle_event_HasEmitterInterface',
        'puzzle_event_RequestEvents',
        'puzzle_event_SubscriberInterface',
        'puzzle_message_AbstractMessage',
        'puzzle_message_MessageFactory',
        'puzzle_message_MessageFactoryInterface',
        'puzzle_message_MessageInterface',
        'puzzle_message_Request',
        'puzzle_message_RequestInterface',
        'puzzle_message_Response',
        'puzzle_message_ResponseInterface',
        'puzzle_stream_MetadataStreamInterface',
        'puzzle_stream_Stream',
        'puzzle_stream_StreamInterface',
        'puzzle_subscriber_HttpError',
        'puzzle_subscriber_Prepare',
        'puzzle_subscriber_Redirect',
        'tubepress_addons_vimeo_api_const_options_names_Meta',
        'tubepress_addons_youtube_api_const_options_names_Meta',
        'tubepress_api_boot_BootSettingsInterface',
        'tubepress_api_const_options_names_Meta',
        'tubepress_api_const_template_Variable',
        'tubepress_api_ioc_CompilerPassInterface',
        'tubepress_api_ioc_ContainerInterface',
        'tubepress_api_log_LoggerInterface',
        'tubepress_api_util_LangUtilsInterface',
        'tubepress_api_util_StringUtilsInterface',
        'tubepress_core_cache_api_Constants',
        'tubepress_core_cache_impl_listeners_http_ApiCacheListener',
        'tubepress_core_cache_impl_stash_FilesystemCacheBuilder',
        'tubepress_core_contrib_api_ContributableValidatorInterface',
        'tubepress_core_deprecated_impl_listeners_LegacyMetadataTemplateListener',
        'tubepress_core_embedded_api_Constants',
        'tubepress_core_embedded_api_EmbeddedHtmlInterface',
        'tubepress_core_embedded_api_EmbeddedProviderInterface',
        'tubepress_core_embedded_impl_EmbeddedHtml',
        'tubepress_core_embedded_impl_listeners_template_Core',
        'tubepress_core_environment_api_EnvironmentInterface',
        'tubepress_core_environment_impl_Environment',
        'tubepress_core_event_api_EventDispatcherInterface',
        'tubepress_core_event_api_EventInterface',
        'tubepress_core_event_impl_tickertape_EventBase',
        'tubepress_core_event_impl_tickertape_EventDispatcher',
        'tubepress_core_html_api_Constants',
        'tubepress_core_html_api_HtmlGeneratorInterface',
        'tubepress_core_html_gallery_api_Constants',
        'tubepress_core_html_gallery_impl_listeners_AbstractGalleryListener',
        'tubepress_core_html_gallery_impl_listeners_CoreGalleryHtmlListener',
        'tubepress_core_html_gallery_impl_listeners_CoreGalleryTemplateListener',
        'tubepress_core_html_gallery_impl_listeners_PaginationTemplateListener',
        'tubepress_core_html_impl_HtmlGenerator',
        'tubepress_core_html_impl_listeners_CoreHtmlListener',
        'tubepress_core_html_search_api_Constants',
        'tubepress_core_html_search_impl_listeners_html_SearchInputListener',
        'tubepress_core_html_search_impl_listeners_html_SearchOutputListener',
        'tubepress_core_html_single_api_Constants',
        'tubepress_core_html_single_impl_listeners_html_SingleVideoListener',
        'tubepress_core_http_api_AjaxCommandInterface',
        'tubepress_core_http_api_Constants',
        'tubepress_core_http_api_HttpClientInterface',
        'tubepress_core_http_api_RequestParametersInterface',
        'tubepress_core_http_api_ResponseCodeInterface',
        'tubepress_core_http_api_message_MessageInterface',
        'tubepress_core_http_api_message_RequestInterface',
        'tubepress_core_http_api_message_ResponseInterface',
        'tubepress_core_http_api_oauth_v1_ClientInterface',
        'tubepress_core_http_impl_AbstractHttpClient',
        'tubepress_core_http_impl_PlayerAjaxCommand',
        'tubepress_core_http_impl_PrimaryAjaxHandler',
        'tubepress_core_http_impl_RequestParameters',
        'tubepress_core_http_impl_ResponseCode',
        'tubepress_core_http_impl_oauth_v1_Client',
        'tubepress_core_http_impl_puzzle_AbstractMessage',
        'tubepress_core_http_impl_puzzle_PuzzleBasedRequest',
        'tubepress_core_http_impl_puzzle_PuzzleBasedResponse',
        'tubepress_core_http_impl_puzzle_PuzzleHttpClient',
        'tubepress_core_ioc_api_Constants',
        'tubepress_core_log_api_Constants',
        'tubepress_core_log_impl_HtmlLogger',
        'tubepress_core_media_item_api_Constants',
        'tubepress_core_media_item_api_MediaItem',
        'tubepress_core_media_item_impl_easy_EasyAttributeFormatter',
        'tubepress_core_media_item_impl_listeners_MetadataTemplateListener',
        'tubepress_core_media_provider_api_CollectorInterface',
        'tubepress_core_media_provider_api_Constants',
        'tubepress_core_media_provider_api_HttpProviderInterface',
        'tubepress_core_media_provider_api_ItemSorterInterface',
        'tubepress_core_media_provider_api_MediaProviderInterface',
        'tubepress_core_media_provider_api_Page',
        'tubepress_core_media_provider_impl_Collector',
        'tubepress_core_media_provider_impl_HttpMediaProvider',
        'tubepress_core_media_provider_impl_ItemSorter',
        'tubepress_core_media_provider_impl_listeners_page_CorePageListener',
        'tubepress_core_options_api_AcceptableValuesInterface',
        'tubepress_core_options_api_Constants',
        'tubepress_core_options_api_ContextInterface',
        'tubepress_core_options_api_PersistenceBackendInterface',
        'tubepress_core_options_api_PersistenceInterface',
        'tubepress_core_options_api_ReferenceInterface',
        'tubepress_core_options_impl_AcceptableValues',
        'tubepress_core_options_impl_Context',
        'tubepress_core_options_impl_Persistence',
        'tubepress_core_options_impl_Reference',
        'tubepress_core_options_impl_easy_EasyValidator',
        'tubepress_core_options_impl_internal_AbstractOptionReader',
        'tubepress_core_options_impl_listeners_BasicOptionValidity',
        'tubepress_core_options_impl_listeners_Logger',
        'tubepress_core_options_impl_listeners_StringMagic',
        'tubepress_core_player_api_Constants',
        'tubepress_core_player_api_PlayerHtmlInterface',
        'tubepress_core_player_api_PlayerLocationInterface',
        'tubepress_core_player_impl_BasePlayerLocation',
        'tubepress_core_player_impl_PlayerHtml',
        'tubepress_core_player_impl_listeners_html_SoloPlayerListener',
        'tubepress_core_player_impl_listeners_template_PlayerLocationCoreVariables',
        'tubepress_core_shortcode_api_Constants',
        'tubepress_core_shortcode_api_ParserInterface',
        'tubepress_core_shortcode_impl_Parser',
        'tubepress_core_stream_api_StreamInterface',
        'tubepress_core_stream_impl_puzzle_FlexibleStream',
        'tubepress_core_template_api_TemplateFactoryInterface',
        'tubepress_core_template_api_TemplateInterface',
        'tubepress_core_template_impl_contemplate_Template',
        'tubepress_core_template_impl_contemplate_TemplateFactory',
        'tubepress_core_theme_api_Constants',
        'tubepress_core_theme_api_ThemeInterface',
        'tubepress_core_theme_api_ThemeLibraryInterface',
        'tubepress_core_theme_impl_ThemeBase',
        'tubepress_core_theme_impl_ThemeLibrary',
        'tubepress_core_theme_ioc_compiler_ThemesPrimerPass',
        'tubepress_core_translation_api_TranslatorInterface',
        'tubepress_core_url_api_QueryInterface',
        'tubepress_core_url_api_UrlFactoryInterface',
        'tubepress_core_url_api_UrlInterface',
        'tubepress_core_url_impl_puzzle_PuzzleBasedQuery',
        'tubepress_core_url_impl_puzzle_PuzzleBasedUrl',
        'tubepress_core_url_impl_puzzle_UrlFactory',
        'tubepress_core_util_api_TimeUtilsInterface',
        'tubepress_core_util_api_UrlUtilsInterface',
        'tubepress_core_util_impl_TimeUtils',
        'tubepress_core_util_impl_UrlUtils',
        'tubepress_core_version_api_Version',
        'tubepress_embedplus_impl_embedded_EmbedPlusProvider',
        'tubepress_impl_boot_BootSettings',
        'tubepress_impl_boot_helper_ContainerSupplier',
        'tubepress_impl_contrib_ContributableBase',
        'tubepress_impl_ioc_Container',
        'tubepress_impl_log_BootLogger',
        'tubepress_impl_util_LangUtils',
        'tubepress_impl_util_StringUtils',
        'tubepress_jwplayer_impl_embedded_JwPlayerEmbeddedProvider',
        'tubepress_jwplayer_impl_listeners_template_JwPlayerTemplateVars',
        'tubepress_vimeo_api_Constants',
        'tubepress_vimeo_impl_embedded_VimeoEmbeddedProvider',
        'tubepress_vimeo_impl_listeners_http_VimeoOauthRequestListener',
        'tubepress_vimeo_impl_listeners_video_VimeoVideoConstructionListener',
        'tubepress_vimeo_impl_provider_VimeoVideoProvider',
        'tubepress_wordpress_impl_Callback',
        'tubepress_wordpress_impl_listeners_html_WpHtmlListener',
        'tubepress_wordpress_impl_listeners_wp_PublicActionsAndFilters',
        'tubepress_wordpress_impl_message_WordPressMessageService',
        'tubepress_wordpress_impl_options_PersistenceBackend',
        'tubepress_wordpress_impl_wp_ActivationHook',
        'tubepress_wordpress_impl_wp_WpFunctions',
        'tubepress_youtube_api_Constants',
        'tubepress_youtube_impl_embedded_YouTubeEmbeddedProvider',
        'tubepress_youtube_impl_listeners_video_YouTubeVideoConstructionListener',
        'tubepress_youtube_impl_provider_YouTubeVideoProvider'
    );
}

tubepress_build_ClassCollectionBuilder::build();
<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class_exists('tubepress_impl_addon_AbstractManifestValidityTest') ||
    require dirname(__FILE__) . '/../../classes/tubepress/impl/addon/AbstractManifestValidityTest.php';

class tubepress_addons_core_CoreManifestValidityTest extends tubepress_impl_addon_AbstractManifestValidityTest
{
    public function testManifest()
    {
        /**
         * @var $addon tubepress_spi_addon_Addon
         */
        $addon = $this->getAddonFromManifest(dirname(__FILE__) . '/../../../../main/php/addons/core/core.json');

        $this->assertEquals('tubepress-core-addon', $addon->getName());
        $this->assertEquals('1.0.0', $addon->getVersion());
        $this->assertEquals('TubePress Core', $addon->getTitle());
        $this->assertEquals(array('name' => 'TubePress LLC', 'url' => 'http://tubepress.org'), $addon->getAuthor());
        $this->assertEquals(array(array('type' => 'MPL-2.0', 'url' => 'http://www.mozilla.org/MPL/2.0/')), $addon->getLicenses());
        $this->assertEquals('TubePress core functionality', $addon->getDescription());
        $this->assertEquals(TUBEPRESS_ROOT . '/src/main/php/addons/core/scripts/bootstrap.php', $addon->getBootstrap());
        $this->assertEquals(array('tubepress_addons_core' => TUBEPRESS_ROOT . '/src/main/php/addons/core/classes'), $addon->getPsr0ClassPathRoots());
        $this->assertEquals(array('tubepress_addons_core_impl_patterns_ioc_IocContainerExtension'), $addon->getIocContainerExtensions());
        $this->validateClassMap($this->_getExpectedClassMap(), $addon->getClassMap());
    }
    
    private function _getExpectedClassMap()
    {
        return array(

            'tubepress_addons_core_impl_Bootstrap' => 'classes/tubepress/addons/core/impl/Bootstrap.php',
            'tubepress_addons_core_impl_http_PlayerPluggableAjaxCommandService' => 'classes/tubepress/addons/core/impl/http/PlayerPluggableAjaxCommandService.php',
            'tubepress_addons_core_impl_listeners_AbstractStringMagicFilter' => 'classes/tubepress/addons/core/impl/listeners/AbstractStringMagicFilter.php',
            'tubepress_addons_core_impl_listeners_boot_CoreOptionsRegistrar' => 'classes/tubepress/addons/core/impl/listeners/boot/CoreOptionsRegistrar.php',
            'tubepress_addons_core_impl_listeners_cssjs_DefaultPathsListener' => 'classes/tubepress/addons/core/impl/listeners/cssjs/DefaultPathsListener.php',
            'tubepress_addons_core_impl_listeners_embeddedhtml_PlayerJavaScriptApi' => 'classes/tubepress/addons/core/impl/listeners/embeddedhtml/PlayerJavaScriptApi.php',
            'tubepress_addons_core_impl_listeners_embeddedtemplate_CoreVariables' => 'classes/tubepress/addons/core/impl/listeners/embeddedtemplate/CoreVariables.php',
            'tubepress_addons_core_impl_listeners_galleryhtml_GalleryJs' => 'classes/tubepress/addons/core/impl/listeners/galleryhtml/GalleryJs.php',
            'tubepress_addons_core_impl_listeners_galleryinitjs_GalleryInitJsBaseParams' => 'classes/tubepress/addons/core/impl/listeners/galleryinitjs/GalleryInitJsBaseParams.php',
            'tubepress_addons_core_impl_listeners_gallerytemplate_CoreVariables' => 'classes/tubepress/addons/core/impl/listeners/gallerytemplate/CoreVariables.php',
            'tubepress_addons_core_impl_listeners_gallerytemplate_EmbeddedPlayerName' => 'classes/tubepress/addons/core/impl/listeners/gallerytemplate/EmbeddedPlayerName.php',
            'tubepress_addons_core_impl_listeners_gallerytemplate_Pagination' => 'classes/tubepress/addons/core/impl/listeners/gallerytemplate/Pagination.php',
            'tubepress_addons_core_impl_listeners_gallerytemplate_Player' => 'classes/tubepress/addons/core/impl/listeners/gallerytemplate/Player.php',
            'tubepress_addons_core_impl_listeners_gallerytemplate_VideoMeta' => 'classes/tubepress/addons/core/impl/listeners/gallerytemplate/VideoMeta.php',
            'tubepress_addons_core_impl_listeners_playertemplate_CoreVariables' => 'classes/tubepress/addons/core/impl/listeners/playertemplate/CoreVariables.php',
            'tubepress_addons_core_impl_listeners_prevalidationoptionset_StringMagic' => 'classes/tubepress/addons/core/impl/listeners/prevalidationoptionset/StringMagic.php',
            'tubepress_addons_core_impl_listeners_prevalidationoptionset_YouTubePlaylistPlPrefixRemover' => 'classes/tubepress/addons/core/impl/listeners/prevalidationoptionset/YouTubePlaylistPlPrefixRemover.php',
            'tubepress_addons_core_impl_listeners_searchinputtemplate_CoreVariables' => 'classes/tubepress/addons/core/impl/listeners/searchinputtemplate/CoreVariables.php',
            'tubepress_addons_core_impl_listeners_singlevideotemplate_CoreVariables' => 'classes/tubepress/addons/core/impl/listeners/singlevideotemplate/CoreVariables.php',
            'tubepress_addons_core_impl_listeners_singlevideotemplate_VideoMeta' => 'classes/tubepress/addons/core/impl/listeners/singlevideotemplate/VideoMeta.php',
            'tubepress_addons_core_impl_listeners_variablereadfromexternalinput_StringMagic' => 'classes/tubepress/addons/core/impl/listeners/variablereadfromexternalinput/StringMagic.php',
            'tubepress_addons_core_impl_listeners_videogallerypage_PerPageSorter' => 'classes/tubepress/addons/core/impl/listeners/videogallerypage/PerPageSorter.php',
            'tubepress_addons_core_impl_listeners_videogallerypage_ResultCountCapper' => 'classes/tubepress/addons/core/impl/listeners/videogallerypage/ResultCountCapper.php',
            'tubepress_addons_core_impl_listeners_videogallerypage_VideoBlacklist' => 'classes/tubepress/addons/core/impl/listeners/videogallerypage/VideoBlacklist.php',
            'tubepress_addons_core_impl_listeners_videogallerypage_VideoPrepender' => 'classes/tubepress/addons/core/impl/listeners/videogallerypage/VideoPrepender.php',
            'tubepress_addons_core_impl_options_ui_CoreOptionsPageParticipant' => 'classes/tubepress/addons/core/impl/options/ui/CoreOptionsPageParticipant.php',
            'tubepress_addons_core_impl_options_ui_CorePluggableFieldBuilder' => 'classes/tubepress/addons/core/impl/options/ui/CorePluggableFieldBuilder.php',
            'tubepress_addons_core_impl_patterns_ioc_FilesystemCacheBuilder' => 'classes/tubepress/addons/core/impl/patterns/ioc/FilesystemCacheBuilder.php',
            'tubepress_addons_core_impl_patterns_ioc_IocContainerExtension' => 'classes/tubepress/addons/core/impl/patterns/ioc/IocContainerExtension.php',
            'tubepress_addons_core_impl_player_JqModalPluggablePlayerLocationService' => 'classes/tubepress/addons/core/impl/player/JqModalPluggablePlayerLocationService.php',
            'tubepress_addons_core_impl_player_NormalPluggablePlayerLocationService' => 'classes/tubepress/addons/core/impl/player/NormalPluggablePlayerLocationService.php',
            'tubepress_addons_core_impl_player_PopupPluggablePlayerLocationService' => 'classes/tubepress/addons/core/impl/player/PopupPluggablePlayerLocationService.php',
            'tubepress_addons_core_impl_player_ShadowboxPluggablePlayerLocationService' => 'classes/tubepress/addons/core/impl/player/ShadowboxPluggablePlayerLocationService.php',
            'tubepress_addons_core_impl_player_SoloPluggablePlayerLocationService' => 'classes/tubepress/addons/core/impl/player/SoloPluggablePlayerLocationService.php',
            'tubepress_addons_core_impl_player_StaticPluggablePlayerLocationService' => 'classes/tubepress/addons/core/impl/player/StaticPluggablePlayerLocationService.php',
            'tubepress_addons_core_impl_player_VimeoPluggablePlayerLocationService' => 'classes/tubepress/addons/core/impl/player/VimeoPluggablePlayerLocationService.php',
            'tubepress_addons_core_impl_player_YouTubePluggablePlayerLocationService' => 'classes/tubepress/addons/core/impl/player/YouTubePluggablePlayerLocationService.php',
            'tubepress_addons_core_impl_shortcode_SearchInputPluggableShortcodeHandlerService' => 'classes/tubepress/addons/core/impl/shortcode/SearchInputPluggableShortcodeHandlerService.php',
            'tubepress_addons_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerService' => 'classes/tubepress/addons/core/impl/shortcode/SearchOutputPluggableShortcodeHandlerService.php',
            'tubepress_addons_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService' => 'classes/tubepress/addons/core/impl/shortcode/SingleVideoPluggableShortcodeHandlerService.php',
            'tubepress_addons_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService' => 'classes/tubepress/addons/core/impl/shortcode/SoloPlayerPluggableShortcodeHandlerService.php',
            'tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService' => 'classes/tubepress/addons/core/impl/shortcode/ThumbGalleryPluggableShortcodeHandlerService.php'
        );
    }
}
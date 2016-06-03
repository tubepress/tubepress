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

class tubepress_deprecated_impl_listeners_LegacyTemplateListener
{
    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_theme_impl_CurrentThemeService
     */
    private $_currentThemeService;

    /**
     * @var tubepress_api_translation_TranslatorInterface
     */
    private $_translator;

    private static $_playerJsMap = array(
        tubepress_api_options_AcceptableValues::PLAYER_LOC_JQMODAL   => 'web/js/players/jqmodal-player.js',
        tubepress_api_options_AcceptableValues::PLAYER_LOC_NORMAL    => 'web/js/players/normal-player.js',
        tubepress_api_options_AcceptableValues::PLAYER_LOC_POPUP     => 'web/js/players/popup-player.js',
        tubepress_api_options_AcceptableValues::PLAYER_LOC_SHADOWBOX => 'web/js/players/shadowbox-player.js',
        tubepress_api_options_AcceptableValues::PLAYER_LOC_TINYBOX   => 'web/js/players/tinybox-player.js',
        tubepress_api_options_AcceptableValues::PLAYER_LOC_FANCYBOX  => 'web/js/players/fancybox-player.js',
    );

    public function __construct(tubepress_api_options_ContextInterface        $context,
                                tubepress_theme_impl_CurrentThemeService      $currentThemeService,
                                tubepress_api_translation_TranslatorInterface $translator)
    {
        $this->_context             = $context;
        $this->_currentThemeService = $currentThemeService;
        $this->_translator          = $translator;
    }

    public function onGalleryTemplate(tubepress_api_event_EventInterface $event)
    {
        if (!$this->_legacyThemeInUse()) {

            return;
        }

        $existingTemplateVars = $event->getSubject();

        if (isset($existingTemplateVars[tubepress_api_template_VariableNames::MEDIA_PAGE])) {

            /**
             * @var tubepress_api_media_MediaPage
             */
            $page = $existingTemplateVars[tubepress_api_template_VariableNames::MEDIA_PAGE];

            if ($page->getTotalResultCount() === 0) {

                throw new RuntimeException($this->_translator->trans('No matching videos'));
            }

            $existingTemplateVars[tubepress_api_const_template_Variable::VIDEO_ARRAY] = $page->getItems();
        }

        $existingTemplateVars[tubepress_api_const_template_Variable::EMBEDDED_IMPL_NAME] = 'x';
        $existingTemplateVars[tubepress_api_const_template_Variable::PLAYER_NAME]        = $this->_context->get(tubepress_api_options_Names::PLAYER_LOCATION);
        $existingTemplateVars[tubepress_api_const_template_Variable::GALLERY_ID]         = $this->_context->get(tubepress_api_options_Names::HTML_GALLERY_ID);

        $this->_adjustPagination($existingTemplateVars);
        $this->_adjustPlayerHtml($existingTemplateVars);

        $event->setSubject($existingTemplateVars);
    }

    public function onPlayerTemplate(tubepress_api_event_EventInterface $event)
    {
        if (!$this->_legacyThemeInUse()) {

            return;
        }

        $existingTemplateVars = $event->getSubject();

        if (isset($existingTemplateVars[tubepress_api_template_VariableNames::MEDIA_ITEM])) {

            $existingTemplateVars[tubepress_api_const_template_Variable::VIDEO] =
                $existingTemplateVars[tubepress_api_template_VariableNames::MEDIA_ITEM];
        }

        $existingTemplateVars[tubepress_api_const_template_Variable::EMBEDDED_WIDTH] = $this->_context->get(tubepress_api_options_Names::EMBEDDED_WIDTH);
        $existingTemplateVars[tubepress_api_const_template_Variable::GALLERY_ID]     = $this->_context->get(tubepress_api_options_Names::HTML_GALLERY_ID);
        $event->setSubject($existingTemplateVars);
    }

    public function onSearchInputTemplate(tubepress_api_event_EventInterface $event)
    {
        if (!$this->_legacyThemeInUse()) {

            return;
        }

        $existingTemplateVars                                                       = $event->getSubject();
        $existingTemplateVars[tubepress_api_const_template_Variable::SEARCH_BUTTON] = $this->_translator->trans('Search');
        $event->setSubject($existingTemplateVars);
    }

    public function onSingleItemTemplate(tubepress_api_event_EventInterface $event)
    {
        if (!$this->_legacyThemeInUse()) {

            return;
        }

        $existingTemplateVars = $event->getSubject();

        $itemId = $existingTemplateVars[tubepress_api_template_VariableNames::MEDIA_ITEM_ID];

        if (!isset($existingTemplateVars[tubepress_api_template_VariableNames::MEDIA_ITEM])) {

            throw new RuntimeException(sprintf($this->_translator->trans('Video %s not found'), $itemId));   //>(translatable)<
        }

        $item = $existingTemplateVars[tubepress_api_template_VariableNames::MEDIA_ITEM];

        $existingTemplateVars[tubepress_api_const_template_Variable::VIDEO]          = $item;
        $existingTemplateVars[tubepress_api_const_template_Variable::EMBEDDED_WIDTH] = $this->_context->get(tubepress_api_options_Names::EMBEDDED_WIDTH);

        $event->setSubject($existingTemplateVars);
    }

    private function _legacyThemeInUse()
    {
        $currentTheme     = $this->_currentThemeService->getCurrentTheme();
        $currentThemeName = $currentTheme->getName();

        return strpos($currentThemeName, '/legacy-') !== false ||
            strpos($currentTheme->getParentThemeName(), '/legacy-') !== false;
    }

    private function _adjustPlayerHtml(array &$templateVars)
    {
        if (!isset($templateVars[tubepress_api_const_template_Variable::PLAYER_HTML])) {

            $templateVars[tubepress_api_const_template_Variable::PLAYER_HTML] = '';

            return;
        }

        $playerLocationName = $templateVars[tubepress_api_const_template_Variable::PLAYER_NAME];

        if (!isset(self::$_playerJsMap[$playerLocationName])) {

            return;
        }

        $jsPath  = self::$_playerJsMap[$playerLocationName];
        $append  = $playerLocationName === tubepress_api_options_AcceptableValues::PLAYER_LOC_NORMAL;
        $asyncJs = <<<EOT
<script type="text/javascript">
    var tubePressDomInjector = tubePressDomInjector || [];
    tubePressDomInjector.push(['loadJs', '$jsPath']);
</script>
EOT;

        if ($append) {

            $newPlayerHtml = $templateVars[tubepress_api_const_template_Variable::PLAYER_HTML] . $asyncJs;

        } else {

            $newPlayerHtml = $asyncJs;
        }

        $templateVars[tubepress_api_const_template_Variable::PLAYER_HTML] = $newPlayerHtml;
    }

    private function _adjustPagination(array &$templateVars)
    {
        if (!isset($templateVars[tubepress_api_template_VariableNames::GALLERY_PAGINATION_HTML])) {

            return;
        }

        $paginationHtml   = $templateVars[tubepress_api_template_VariableNames::GALLERY_PAGINATION_HTML];
        $paginationTop    = $this->_context->get(tubepress_api_options_Names::GALLERY_PAGINATE_ABOVE);
        $paginationBottom = $this->_context->get(tubepress_api_options_Names::GALLERY_PAGINATE_BELOW);

        if ($paginationTop) {

            $templateVars[tubepress_api_const_template_Variable::PAGINATION_TOP] = $paginationHtml;
        }

        if ($paginationBottom) {

            $templateVars[tubepress_api_const_template_Variable::PAGINATION_BOTTOM] = $paginationHtml;
        }
    }
}

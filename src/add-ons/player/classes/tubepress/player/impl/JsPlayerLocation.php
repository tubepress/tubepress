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
class tubepress_player_impl_JsPlayerLocation implements tubepress_spi_player_PlayerLocationInterface
{
    /**
     * @var string
     */
    private $_name;

    /**
     * @var string
     */
    private $_untranslatedDisplayName;

    /**
     * @var string
     */
    private $_staticTemplateName = null;

    /**
     * @var string
     */
    private $_ajaxTemplateName = null;

    public function __construct($name, $untranslatedDisplayName, $staticTemplateName = null, $ajaxTemplateName = null)
    {
        $this->_name                    = $name;
        $this->_untranslatedDisplayName = $untranslatedDisplayName;
        $this->_staticTemplateName      = $staticTemplateName;
        $this->_ajaxTemplateName        = $ajaxTemplateName;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * {@inheritdoc}
     */
    public function getUntranslatedDisplayName()
    {
        return $this->_untranslatedDisplayName;
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticTemplateName()
    {
        return $this->_staticTemplateName;
    }

    /**
     * {@inheritdoc}
     */
    public function getAjaxTemplateName()
    {
        return $this->_ajaxTemplateName;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributesForInvocationAnchor(tubepress_api_media_MediaItem $mediaItem)
    {
        return array();
    }
}

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

/**
 * Message service that uses gettext (via WordPress).
 */
class tubepress_wordpress_impl_translation_WpTranslator extends tubepress_internal_translation_AbstractTranslator
{
    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    public function __construct(tubepress_wordpress_impl_wp_WpFunctions $wpFunctions)
    {
        $this->_wpFunctions = $wpFunctions;
    }

    /**
     * {@inheritdoc}
     */
    protected function translate($id, $domain = null, $locale = null)
    {
        $domain = $domain ? $domain : 'tubepress';

        return $id == '' ? '' : $this->_wpFunctions->__($id, $domain);
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        throw new LogicException('Use WPLANG to set WordPress locale');
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->_wpFunctions->get_locale();
    }
}

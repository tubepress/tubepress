<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_test_addons_embedplus_impl_ioc_EmbedPlusIocContainerExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{
    protected function buildSut()
    {
        return new tubepress_addons_embedplus_impl_ioc_EmbedPlusIocContainerExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration('tubepress_addons_embedplus_impl_embedded_EmbedPlusPluggableEmbeddedPlayerService',
            'tubepress_addons_embedplus_impl_embedded_EmbedPlusPluggableEmbeddedPlayerService')
            ->withTag(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);
    }
}
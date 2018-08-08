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
class tubepress_test_app_impl_options_ui_fields_templated_single_MultiSourceSingleOptionFieldTest extends tubepress_test_app_impl_options_ui_fields_templated_single_AbstractSingleOptionFieldTest
{
    protected function getId()
    {
        return 'name';
    }

    protected function getMultiSourcePrefix()
    {
        return 'abc-456-';
    }

    /**
     * @return string
     */
    protected function getExpectedTemplateName()
    {
        return 'template-name';
    }

    /**
     * @return tubepress_options_ui_impl_fields_templated_AbstractTemplatedField
     */
    protected function getSut()
    {
        return new tubepress_options_ui_impl_fields_templated_single_MultiSourceSingleOptionField(

            'name',
            'template-name',
            $this->getMockPersistence(),
            $this->getMockHttpRequestParams(),
            $this->getMockTemplating(),
            $this->getMockOptionsReference(),
            'abc-456-'
        );
    }
}

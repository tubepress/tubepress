<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_meta_ioc_MetaExtension
 */
class tubepress_test_meta_ioc_MetaExtensionTest extends tubepress_api_test_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_meta_ioc_MetaExtension
     */
    protected function buildSut()
    {
        return  new tubepress_meta_ioc_MetaExtension();
    }

    protected function prepareForLoad()
    {
        $this->_registerListeners();
        $this->_registerOptions();
        $this->_registerOptionsUi();
    }

    private function _registerListeners()
    {
        $this->expectRegistration(
            'tubepress_meta_impl_listeners_MetaDisplayListener',
            'tubepress_meta_impl_listeners_MetaDisplayListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_api_media_MediaProviderInterface::__,
                'method' => 'setMediaProviders'))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    =>  tubepress_api_event_Events::TEMPLATE_PRE_RENDER . '.single/main',
                'priority' => 98000,
                'method'   => 'onPreTemplate'))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    =>  tubepress_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/main',
                'priority' => 98000,
                'method'   => 'onPreTemplate'));
    }

    private function _registerOptions()
    {
        $this->expectRegistration(
            'tubepress_api_options_Reference__meta',
            'tubepress_api_options_Reference'
        )->withTag(tubepress_api_options_ReferenceInterface::_)
            ->withArgument(array(

                tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                    tubepress_api_options_Names::META_DATEFORMAT          => 'M j, Y',
                    tubepress_api_options_Names::META_DESC_LIMIT          => 80,
                    tubepress_api_options_Names::META_DISPLAY_AUTHOR      => false,
                    tubepress_api_options_Names::META_DISPLAY_CATEGORY    => false,
                    tubepress_api_options_Names::META_DISPLAY_DESCRIPTION => false,
                    tubepress_api_options_Names::META_DISPLAY_ID          => false,
                    tubepress_api_options_Names::META_DISPLAY_KEYWORDS    => false,
                    tubepress_api_options_Names::META_DISPLAY_LENGTH      => true,
                    tubepress_api_options_Names::META_DISPLAY_TITLE       => true,
                    tubepress_api_options_Names::META_DISPLAY_UPLOADED    => false,
                    tubepress_api_options_Names::META_DISPLAY_URL         => false,
                    tubepress_api_options_Names::META_DISPLAY_VIEWS       => true,
                    tubepress_api_options_Names::META_RELATIVE_DATES      => false,
                ),

                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                    tubepress_api_options_Names::META_DATEFORMAT          => 'Date format',                //>(translatable)<
                    tubepress_api_options_Names::META_DESC_LIMIT          => 'Maximum description length', //>(translatable)<
                    tubepress_api_options_Names::META_DISPLAY_AUTHOR      => 'Author',           //>(translatable)<
                    tubepress_api_options_Names::META_DISPLAY_CATEGORY    => 'Category',         //>(translatable)<
                    tubepress_api_options_Names::META_DISPLAY_DESCRIPTION => 'Description',      //>(translatable)<
                    tubepress_api_options_Names::META_DISPLAY_ID          => 'ID',               //>(translatable)<
                    tubepress_api_options_Names::META_DISPLAY_KEYWORDS    => 'Keywords',         //>(translatable)<
                    tubepress_api_options_Names::META_DISPLAY_LENGTH      => 'Runtime',          //>(translatable)<
                    tubepress_api_options_Names::META_DISPLAY_TITLE       => 'Title',            //>(translatable)<
                    tubepress_api_options_Names::META_DISPLAY_UPLOADED    => 'Date posted',      //>(translatable)<
                    tubepress_api_options_Names::META_DISPLAY_URL         => 'URL',              //>(translatable)<
                    tubepress_api_options_Names::META_DISPLAY_VIEWS       => 'View count',       //>(translatable)<
                    tubepress_api_options_Names::META_RELATIVE_DATES      => 'Use relative dates',         //>(translatable)<

                ),

                tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                    tubepress_api_options_Names::META_DATEFORMAT     => sprintf('Set the textual formatting of date information for videos. See <a href="%s" target="_blank">date</a> for examples.', "http://php.net/date"),    //>(translatable)<
                    tubepress_api_options_Names::META_DESC_LIMIT     => 'Maximum number of characters to display in video descriptions. Set to 0 for no limit.', //>(translatable)<
                    tubepress_api_options_Names::META_RELATIVE_DATES => 'e.g. "yesterday" instead of "November 3, 1980".',  //>(translatable)<
                ),
            ))->withArgument(array());

        $toValidate = array(
            tubepress_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_NONNEGATIVE => array(
                tubepress_api_options_Names::META_DESC_LIMIT,
            ),
        );

        foreach ($toValidate as $type => $optionNames) {
            foreach ($optionNames as $optionName) {
                $this->expectRegistration(
                    'regex_validator.' . $optionName,
                    'tubepress_api_listeners_options_RegexValidatingListener'
                )->withArgument($type)
                    ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
                    ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
                    ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                        'event'    => tubepress_api_event_Events::OPTION_SET . ".$optionName",
                        'priority' => 100000,
                        'method'   => 'onOption',
                    ));
            }
        }
    }

    private function _registerOptionsUi()
    {
        $fieldReferences = array();
        $fieldMap = array(
            'boolean' => array(
                tubepress_api_options_Names::META_RELATIVE_DATES,
            ),
            'fieldProviderFilter' => array(
                tubepress_options_ui_impl_fields_templated_multi_FieldProviderFilterField::FIELD_ID
            ),
            'text' => array(
                tubepress_api_options_Names::META_DATEFORMAT,
                tubepress_api_options_Names::META_DESC_LIMIT,
            ),
            'metaMultiSelect' => array(
                'does not matter'
            ),
        );

        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {

                $serviceId = 'meta_field_' . $id;

                $this->expectRegistration(
                    $serviceId,
                    'tubepress_api_options_ui_FieldInterface'
                )->withFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
                    ->withFactoryMethod('newInstance')
                    ->withArgument($id)
                    ->withArgument($type);

                $fieldReferences[] = new tubepress_api_ioc_Reference($serviceId);
            }
        }

        $categoryReferences = array();
        $categories = array(
            array(tubepress_api_options_ui_CategoryNames::META, 'Meta'),          //>(translatable)<
        );
        foreach ($categories as $categoryIdAndLabel) {

            $serviceId = 'meta_category_' . $categoryIdAndLabel[0];
            $this->expectRegistration(
                $serviceId,
                'tubepress_options_ui_impl_BaseElement'
            )->withArgument($categoryIdAndLabel[0])
                ->withArgument($categoryIdAndLabel[1]);

            $categoryReferences[] = new tubepress_api_ioc_Reference($serviceId);
        }

        $fieldMap = array(
            tubepress_api_options_ui_CategoryNames::META => array(
                tubepress_options_ui_impl_fields_templated_multi_MetaMultiSelectField::FIELD_ID,
                tubepress_api_options_Names::META_DATEFORMAT,
                tubepress_api_options_Names::META_RELATIVE_DATES,
                tubepress_api_options_Names::META_DESC_LIMIT,
            ),
        );

        $this->expectRegistration(
            'tubepress_api_options_ui_BaseFieldProvider__meta',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->withArgument('field-provider-meta')
            ->withArgument('Meta')
            ->withArgument(false)
            ->withArgument(false)
            ->withArgument($categoryReferences)
            ->withArgument($fieldReferences)
            ->withArgument($fieldMap)
            ->withTag('tubepress_api_options_ui_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $fieldBuilder = $this->mock(tubepress_api_options_ui_FieldBuilderInterface::_);
        $mockField    = $this->mock('tubepress_api_options_ui_FieldInterface');
        $fieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        return array(
            tubepress_api_options_ContextInterface::_         => tubepress_api_options_ContextInterface::_,
            tubepress_api_options_ui_FieldBuilderInterface::_ => $fieldBuilder,
            tubepress_api_options_ReferenceInterface::_       => tubepress_api_options_ReferenceInterface::_,
            tubepress_api_translation_TranslatorInterface::_  => tubepress_api_translation_TranslatorInterface::_,
        );
    }
}

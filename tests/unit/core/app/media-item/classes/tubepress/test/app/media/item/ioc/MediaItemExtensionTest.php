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

/**
 * @covers tubepress_app_media_item_ioc_MediaItemExtension
 */
class tubepress_test_app_media_item_ioc_MediaItemExtensionTest extends tubepress_test_ioc_AbstractIocContainerExtensionTest
{
    /**
     * @return tubepress_app_media_item_ioc_MediaItemExtension
     */
    protected function buildSut()
    {
        return new tubepress_app_media_item_ioc_MediaItemExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            'tubepress_app_media_item_impl_listeners_template_ToShowAndLabelsMapSetter',
            'tubepress_app_media_item_impl_listeners_template_ToShowAndLabelsMapSetter'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ReferenceInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_translation_api_TranslatorInterface::_))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_app_media_provider_api_MediaProviderInterface::_,
                'method' => 'setMediaProviders'))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_feature_gallery_api_Constants::EVENT_TEMPLATE_THUMBNAIL_GALLERY,
                'method'   => 'onTemplate',
                'priority' => 10400))
            ->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_app_feature_single_api_Constants::EVENT_SINGLE_ITEM_TEMPLATE,
                'method'   => 'onTemplate',
                'priority' => 10100
            ));
        
        $this->expectParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_media_item', array(

            'defaultValues' => array(
                tubepress_app_media_item_api_Constants::OPTION_AUTHOR         => false,
                tubepress_app_media_item_api_Constants::OPTION_CATEGORY       => false,
                tubepress_app_media_item_api_Constants::OPTION_DATEFORMAT     => 'M j, Y',
                tubepress_app_media_item_api_Constants::OPTION_DESC_LIMIT     => 80,
                tubepress_app_media_item_api_Constants::OPTION_DESCRIPTION    => false,
                tubepress_app_media_item_api_Constants::OPTION_ID             => false,
                tubepress_app_media_item_api_Constants::OPTION_KEYWORDS       => false,
                tubepress_app_media_item_api_Constants::OPTION_LENGTH         => true,
                tubepress_app_media_item_api_Constants::OPTION_RELATIVE_DATES => false,
                tubepress_app_media_item_api_Constants::OPTION_TITLE          => true,
                tubepress_app_media_item_api_Constants::OPTION_UPLOADED       => false,
                tubepress_app_media_item_api_Constants::OPTION_URL            => false,
                tubepress_app_media_item_api_Constants::OPTION_VIEWS          => true,
            ),

            'labels' => array(
                tubepress_app_media_item_api_Constants::OPTION_AUTHOR         => 'Author',           //>(translatable)<
                tubepress_app_media_item_api_Constants::OPTION_CATEGORY       => 'Category',         //>(translatable)<
                tubepress_app_media_item_api_Constants::OPTION_DATEFORMAT     => 'Date format',                //>(translatable)<
                tubepress_app_media_item_api_Constants::OPTION_DESC_LIMIT     => 'Maximum description length', //>(translatable)<
                tubepress_app_media_item_api_Constants::OPTION_DESCRIPTION    => 'Description',      //>(translatable)<
                tubepress_app_media_item_api_Constants::OPTION_ID             => 'ID',               //>(translatable)<
                tubepress_app_media_item_api_Constants::OPTION_KEYWORDS       => 'Keywords',         //>(translatable)<
                tubepress_app_media_item_api_Constants::OPTION_LENGTH         => 'Runtime',          //>(translatable)<
                tubepress_app_media_item_api_Constants::OPTION_RELATIVE_DATES => 'Use relative dates',         //>(translatable)<
                tubepress_app_media_item_api_Constants::OPTION_TITLE          => 'Title',            //>(translatable)<
                tubepress_app_media_item_api_Constants::OPTION_UPLOADED       => 'Date posted',      //>(translatable)<
                tubepress_app_media_item_api_Constants::OPTION_URL            => 'URL',              //>(translatable)<
                tubepress_app_media_item_api_Constants::OPTION_VIEWS          => 'View count',       //>(translatable)<
            ),

            'descriptions' => array(
                tubepress_app_media_item_api_Constants::OPTION_DATEFORMAT     => sprintf('Set the textual formatting of date information for videos. See <a href="%s" target="_blank">date</a> for examples.', "http://php.net/date"),    //>(translatable)<
                tubepress_app_media_item_api_Constants::OPTION_DESC_LIMIT     => 'Maximum number of characters to display in video descriptions. Set to 0 for no limit.', //>(translatable)<
                tubepress_app_media_item_api_Constants::OPTION_RELATIVE_DATES => 'e.g. "yesterday" instead of "November 3, 1980".',  //>(translatable)<
            )
        ));

        $this->expectParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_VALIDATION . '_media_item', array(

            'priority' => 30000,
            'map'      => array(
                'nonNegativeInteger' => array(
                    tubepress_app_media_item_api_Constants::OPTION_DESC_LIMIT,
                )
            )
        ));

        $this->expectRegistration(
            'meta_category',
            'tubepress_app_options_ui_api_ElementInterface'
        )->withFactoryService(tubepress_app_options_ui_api_ElementBuilderInterface::_)
            ->withFactoryMethod('newInstance')
            ->withArgument(tubepress_app_media_item_api_Constants::OPTIONS_UI_CATEGORY_META)
            ->withArgument('Meta');  //>(translatable)<

        $fieldIndex = 0;

        $fieldMap = array(
            'text' => array(
                tubepress_app_media_item_api_Constants::OPTION_DATEFORMAT,
                tubepress_app_media_item_api_Constants::OPTION_DESC_LIMIT,
            ),
            'boolean' => array(
                tubepress_app_media_item_api_Constants::OPTION_RELATIVE_DATES,
            ),
            'metaMultiSelect' => array(
                'does not matter'
            )
        );
        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {
                $this->expectRegistration(
                    'media_item_field_' . $fieldIndex++,
                    'tubepress_app_options_ui_api_FieldInterface'
                )->withFactoryService(tubepress_app_options_ui_api_FieldBuilderInterface::_)
                    ->withFactoryMethod('newInstance')
                    ->withArgument($id)
                    ->withArgument($type);
            }
        }

        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; $x++) {
            $fieldReferences[] = new tubepress_platform_api_ioc_Reference('media_item_field_' . $x);
        }
        $fieldMap = array(
            tubepress_app_media_item_api_Constants::OPTIONS_UI_CATEGORY_META => array(
                tubepress_app_options_ui_impl_fields_MetaMultiSelectField::FIELD_ID,
                tubepress_app_media_item_api_Constants::OPTION_DATEFORMAT,
                tubepress_app_media_item_api_Constants::OPTION_RELATIVE_DATES,
                tubepress_app_media_item_api_Constants::OPTION_DESC_LIMIT,
            )
        );

        $this->expectRegistration(
            'tubepress_app_media_item_impl_options_ui_FieldProvider',
            'tubepress_app_media_item_impl_options_ui_FieldProvider'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_translation_api_TranslatorInterface::_))
            ->withArgument(array(new tubepress_platform_api_ioc_Reference('meta_category')))
            ->withArgument($fieldReferences)
            ->withArgument($fieldMap)
            ->withTag('tubepress_app_options_ui_api_FieldProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $mockField = $this->mock('tubepress_app_options_ui_api_FieldInterface');
        $fieldBuilder = $this->mock(tubepress_app_options_ui_api_FieldBuilderInterface::_);
        $fieldBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockField);

        $mockCategory = $this->mock('tubepress_app_options_ui_api_ElementInterface');
        $elementBuilder = $this->mock(tubepress_app_options_ui_api_ElementBuilderInterface::_);
        $elementBuilder->shouldReceive('newInstance')->atLeast(1)->andReturn($mockCategory);

        return array(
            tubepress_lib_translation_api_TranslatorInterface::_ => tubepress_lib_translation_api_TranslatorInterface::_,
            tubepress_app_options_ui_api_ElementBuilderInterface::_ => $elementBuilder,
            tubepress_app_options_ui_api_FieldBuilderInterface::_ => $fieldBuilder,
            tubepress_app_options_api_ContextInterface::_ => tubepress_app_options_api_ContextInterface::_,
            tubepress_app_options_api_ReferenceInterface::_ => tubepress_app_options_api_ReferenceInterface::_,
            tubepress_lib_event_api_EventDispatcherInterface::_ => tubepress_lib_event_api_EventDispatcherInterface::_
        );
    }
}
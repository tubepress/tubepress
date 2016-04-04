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

class tubepress_meta_ioc_MetaExtension implements tubepress_spi_ioc_ContainerExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerListeners($containerBuilder);
        $this->_registerOptions($containerBuilder);
        $this->_registerOptionsUi($containerBuilder);
    }

    private function _registerListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_meta_impl_listeners_MetaDisplayListener',
            'tubepress_meta_impl_listeners_MetaDisplayListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
         ->addTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_spi_media_MediaProviderInterface::__,
            'method' => 'setMediaProviders',
         ))->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::TEMPLATE_PRE_RENDER . '.single/main',
            'priority' => 98000,
            'method'   => 'onPreTemplate',
         ))->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::TEMPLATE_PRE_RENDER . '.gallery/main',
            'priority' => 98000,
            'method'   => 'onPreTemplate',
         ));
    }

    private function _registerOptions(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_api_options_Reference__meta',
            'tubepress_api_options_Reference'
        )->addTag(tubepress_api_options_ReferenceInterface::_)
         ->addArgument(array(

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
                tubepress_api_options_Names::META_DISPLAY_AUTHOR      => 'Author',                     //>(translatable)<
                tubepress_api_options_Names::META_DISPLAY_CATEGORY    => 'Category',                   //>(translatable)<
                tubepress_api_options_Names::META_DISPLAY_DESCRIPTION => 'Description',                //>(translatable)<
                tubepress_api_options_Names::META_DISPLAY_ID          => 'ID',                         //>(translatable)<
                tubepress_api_options_Names::META_DISPLAY_KEYWORDS    => 'Keywords',                   //>(translatable)<
                tubepress_api_options_Names::META_DISPLAY_LENGTH      => 'Runtime',                    //>(translatable)<
                tubepress_api_options_Names::META_DISPLAY_TITLE       => 'Title',                      //>(translatable)<
                tubepress_api_options_Names::META_DISPLAY_UPLOADED    => 'Date posted',                //>(translatable)<
                tubepress_api_options_Names::META_DISPLAY_URL         => 'URL',                        //>(translatable)<
                tubepress_api_options_Names::META_DISPLAY_VIEWS       => 'View count',                 //>(translatable)<
                tubepress_api_options_Names::META_RELATIVE_DATES      => 'Use relative dates',         //>(translatable)<

            ),

            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                tubepress_api_options_Names::META_DATEFORMAT     => sprintf('Set the textual formatting of date information for videos. See <a href="%s" target="_blank">date</a> for examples.', "http://php.net/date"),    //>(translatable)<
                tubepress_api_options_Names::META_DESC_LIMIT     => 'Maximum number of characters to display in video descriptions. Set to 0 for no limit.', //>(translatable)<
                tubepress_api_options_Names::META_RELATIVE_DATES => 'e.g. "yesterday" instead of "November 3, 1980".',  //>(translatable)<
            ),
        ))->addArgument(array());

        $toValidate = array(
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_INTEGER_NONNEGATIVE => array(
                tubepress_api_options_Names::META_DESC_LIMIT,
            ),
        );

        foreach ($toValidate as $type => $optionNames) {
            foreach ($optionNames as $optionName) {
                $containerBuilder->register(
                    'regex_validator.' . $optionName,
                    'tubepress_api_options_listeners_RegexValidatingListener'
                )->addArgument($type)
                 ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
                 ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
                 ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                    'event'    => tubepress_api_event_Events::OPTION_SET . ".$optionName",
                    'priority' => 100000,
                    'method'   => 'onOption',
                ));
            }
        }
    }

    private function _registerOptionsUi(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $fieldReferences = array();
        $fieldMap        = array(
            'boolean' => array(
                tubepress_api_options_Names::META_RELATIVE_DATES,
            ),
            'fieldProviderFilter' => array(
                tubepress_options_ui_impl_fields_templated_multi_FieldProviderFilterField::FIELD_ID,
            ),
            'text' => array(
                tubepress_api_options_Names::META_DATEFORMAT,
                tubepress_api_options_Names::META_DESC_LIMIT,
            ),
            'metaMultiSelect' => array(
                'does not matter',
            ),
        );

        foreach ($fieldMap as $type => $ids) {
            foreach ($ids as $id) {

                $serviceId = 'meta_field_' . $id;

                $containerBuilder->register(
                    $serviceId,
                    'tubepress_api_options_ui_FieldInterface'
                )->setFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
                 ->setFactoryMethod('newInstance')
                 ->addArgument($id)
                 ->addArgument($type);

                $fieldReferences[] = new tubepress_api_ioc_Reference($serviceId);
            }
        }

        $categoryReferences = array();
        $categories         = array(
            array(tubepress_api_options_ui_CategoryNames::META, 'Meta'),          //>(translatable)<
        );
        foreach ($categories as $categoryIdAndLabel) {

            $serviceId = 'meta_category_' . $categoryIdAndLabel[0];
            $containerBuilder->register(
                $serviceId,
                'tubepress_options_ui_impl_BaseElement'
            )->addArgument($categoryIdAndLabel[0])
             ->addArgument($categoryIdAndLabel[1]);

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

        $containerBuilder->register(
            'tubepress_api_options_ui_BaseFieldProvider__meta',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->addArgument('field-provider-meta')
         ->addArgument('Meta')
         ->addArgument(false)
         ->addArgument(false)
         ->addArgument($categoryReferences)
         ->addArgument($fieldReferences)
         ->addArgument($fieldMap)
         ->addTag('tubepress_spi_options_ui_FieldProviderInterface');
    }
}

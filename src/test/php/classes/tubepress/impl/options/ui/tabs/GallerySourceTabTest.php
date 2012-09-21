<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class tubepress_impl_options_ui_tabs_GallerySourceTabTest extends TubePressUnitTest
{
    private $_mockFieldBuilder;

    private $_mockTemplateBuilder;

    private $_mockEnvironmentDetector;

    private $_mockExecutionContext;

    public function setup()
    {
        $ms                             = Mockery::mock(tubepress_spi_message_MessageService::_);
        $this->_mockEnvironmentDetector = Mockery::mock(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockTemplateBuilder     = Mockery::mock('ehough_contemplate_api_TemplateBuilder');
        $this->_mockFieldBuilder        = Mockery::mock(tubepress_spi_options_ui_FieldBuilder::_);
        $this->_mockExecutionContext    = Mockery::mock(tubepress_spi_context_ExecutionContext::_);

        $ms->shouldReceive('_')->andReturnUsing( function ($key) {

            return "<<message: $key>>";
        });

        tubepress_impl_patterns_ioc_KernelServiceLocator::setMessageService($ms);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEnvironmentDetector($this->_mockEnvironmentDetector);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setTemplateBuilder($this->_mockTemplateBuilder);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionsUiFieldBuilder($this->_mockFieldBuilder);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);

        $this->_sut = new tubepress_impl_options_ui_tabs_GallerySourceTab();
    }

    public function testGetName()
        {
            $this->assertEquals('<<message: Which videos?>>', $this->_sut->getTitle());
    }
    
    public function testGetHtml()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE)->andReturn('some mode');

        $expected = $this->getFieldArray();
        $expectedFieldArray = array();
        
        foreach ($expected as $key => $arr) {

            foreach ($arr as $name => $type) {

                $this->_mockFieldBuilder->shouldReceive('build')->once()->with($name, $type)->andReturn("$name-$type");
                $expectedFieldArray[$key] = "$name-$type";
            }
        }
         
        $template = \Mockery::mock('ehough_contemplate_api_Template');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_tabs_GallerySourceTab::TEMPLATE_VAR_CURRENT_MODE, 'some mode');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_tabs_AbstractTab::TEMPLATE_VAR_WIDGETARRAY, $expectedFieldArray);
        $template->shouldReceive('toString')->once()->andReturn('final result');
    
        $this->_mockEnvironmentDetector->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('<<basepath!>>');
        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with('<<basepath!>>/src/main/resources/system-templates/options_page/gallery_source_tab.tpl.php')->andReturn($template);
         
        $this->assertEquals('final result', $this->_sut->getHtml());
    }
    
    private function getFieldArray()
    {
        return array(

            tubepress_api_const_options_names_Output::GALLERY_SOURCE =>
                array(tubepress_api_const_options_names_Output::GALLERY_SOURCE => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
            
            tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES =>
                array(tubepress_api_const_options_names_GallerySource::YOUTUBE_FAVORITES_VALUE => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
        
            tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED =>
                array(tubepress_api_const_options_names_GallerySource::YOUTUBE_FEATURED => tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),
            
            tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_USER =>
                array(tubepress_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
                
            tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST =>
                array(tubepress_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
                
            tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH =>
                array(tubepress_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
                
            tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_VIEWED =>
                array(tubepress_api_const_options_names_GallerySource::YOUTUBE_MOST_VIEWED_VALUE => tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),
                
            tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED =>
                array(tubepress_api_const_options_names_GallerySource::YOUTUBE_TOP_RATED_VALUE => tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES =>
                array(tubepress_api_const_options_names_GallerySource::YOUTUBE_TOP_FAVORITES_VALUE => tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),
                
            tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED =>
                array(tubepress_api_const_options_names_GallerySource::YOUTUBE_MOST_DISCUSSED_VALUE => tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),
            
            tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT =>
                array(tubepress_api_const_options_names_GallerySource::YOUTUBE_MOST_RECENT_VALUE => tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),
            
            tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED =>
                array(tubepress_api_const_options_names_GallerySource::YOUTUBE_MOST_RESPONDED_VALUE => tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),
                
            tubepress_api_const_options_values_GallerySourceValue::VIMEO_ALBUM =>
                array(tubepress_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
                
            tubepress_api_const_options_values_GallerySourceValue::VIMEO_UPLOADEDBY =>
                array(tubepress_api_const_options_names_GallerySource::VIMEO_UPLOADEDBY_VALUE => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
                
            tubepress_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL =>
                array(tubepress_api_const_options_names_GallerySource::VIMEO_CHANNEL_VALUE => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
                
            tubepress_api_const_options_values_GallerySourceValue::VIMEO_GROUP =>
                array(tubepress_api_const_options_names_GallerySource::VIMEO_GROUP_VALUE => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
                
            tubepress_api_const_options_values_GallerySourceValue::VIMEO_SEARCH =>
                array(tubepress_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            tubepress_api_const_options_values_GallerySourceValue::VIMEO_LIKES =>
                array(tubepress_api_const_options_names_GallerySource::VIMEO_LIKES_VALUE => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
                
            tubepress_api_const_options_values_GallerySourceValue::VIMEO_APPEARS_IN =>
                array(tubepress_api_const_options_names_GallerySource::VIMEO_APPEARS_IN_VALUE => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
                
            tubepress_api_const_options_values_GallerySourceValue::VIMEO_CREDITED =>
                array(tubepress_api_const_options_names_GallerySource::VIMEO_CREDITED_VALUE => tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
        );
    }
}
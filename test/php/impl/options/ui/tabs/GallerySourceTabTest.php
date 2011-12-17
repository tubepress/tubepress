<?php

class org_tubepress_impl_options_ui_tabs_GallerySourceTabTest extends TubePressUnitTest {

    public function setup()
    {
        parent::setUp();
    
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $ms = $ioc->get(org_tubepress_api_message_MessageService::_);
    
        $ms->shouldReceive('_')->andReturnUsing( function ($key) {
            return "<<message: $key>>";
        });
    
        $this->_sut = new org_tubepress_impl_options_ui_tabs_GallerySourceTab();
    }
    
    public function testGetName()
        {
            $this->assertEquals('<<message: Which videos?>>', $this->_sut->getTitle());
    }
    
    public function testGetHtml()
        {
        $ioc           = org_tubepress_impl_ioc_IocContainer::getInstance();
        $templateBldr  = $ioc->get(org_tubepress_api_template_TemplateBuilder::_);
        $fse           = $ioc->get(org_tubepress_api_filesystem_Explorer::_);
        $fieldBuilder = $ioc->get(org_tubepress_spi_options_ui_FieldBuilder::_);
    
        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Output::GALLERY_SOURCE)->andReturn('some mode');
        
        
        
        $expected = $this->getFieldArray();
        $expectedFieldArray = array();
        
        foreach ($expected as $key => $arr) {

            foreach ($arr as $name => $type) {
                $fieldBuilder->shouldReceive('build')->once()->with($name, $type)->andReturn("$name-$type");
                $expectedFieldArray[$key] = "$name-$type";
            }
        }
         
        $template = \Mockery::mock(org_tubepress_api_template_Template::_);
        $template->shouldReceive('setVariable')->once()->with(org_tubepress_impl_options_ui_tabs_GallerySourceTab::TEMPLATE_VAR_CURRENT_MODE, 'some mode');
        $template->shouldReceive('setVariable')->once()->with(org_tubepress_impl_options_ui_tabs_AbstractTab::TEMPLATE_VAR_WIDGETARRAY, $expectedFieldArray);
        $template->shouldReceive('toString')->once()->andReturn('final result');
    
        $fse->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('<<basepath!>>');
        $templateBldr->shouldReceive('getNewTemplateInstance')->once()->with('<<basepath!>>/sys/ui/templates/options_page/gallery_source_tab.tpl.php')->andReturn($template);
         
        $this->assertEquals('final result', $this->_sut->getHtml());
    }
    
    private function getFieldArray()
    {
        return array(
            org_tubepress_api_const_options_names_Output::GALLERY_SOURCE =>
                array(org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_impl_options_ui_fields_TextField::__),
            
            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES =>
                array(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_FAVORITES_VALUE => org_tubepress_impl_options_ui_fields_TextField::__),
        
            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED =>
                array(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_FEATURED => org_tubepress_impl_options_ui_fields_DropdownField::_),
            
            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_USER =>
                array(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE => org_tubepress_impl_options_ui_fields_TextField::__),
                
            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST =>
                array(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => org_tubepress_impl_options_ui_fields_TextField::__),
                
            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH =>
                array(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE => org_tubepress_impl_options_ui_fields_TextField::__),
                
            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_VIEWED =>
                array(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_MOST_VIEWED_VALUE => org_tubepress_impl_options_ui_fields_DropdownField::_),
                
            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED =>
                array(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_TOP_RATED_VALUE => org_tubepress_impl_options_ui_fields_DropdownField::_),

            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES =>
                array(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_TOP_FAVORITES_VALUE => org_tubepress_impl_options_ui_fields_DropdownField::_),
                
            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED =>
                array(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_MOST_DISCUSSED_VALUE => org_tubepress_impl_options_ui_fields_DropdownField::_),
            
            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT =>
                array(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_MOST_RECENT_VALUE => org_tubepress_impl_options_ui_fields_DropdownField::_),
            
            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED =>
                array(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_MOST_RESPONDED_VALUE => org_tubepress_impl_options_ui_fields_DropdownField::_),
                
            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_ALBUM =>
                array(org_tubepress_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE => org_tubepress_impl_options_ui_fields_TextField::__),
                
            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_UPLOADEDBY =>
                array(org_tubepress_api_const_options_names_GallerySource::VIMEO_UPLOADEDBY_VALUE => org_tubepress_impl_options_ui_fields_TextField::__),
                
            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL =>
                array(org_tubepress_api_const_options_names_GallerySource::VIMEO_CHANNEL_VALUE => org_tubepress_impl_options_ui_fields_TextField::__),
                
            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_GROUP =>
                array(org_tubepress_api_const_options_names_GallerySource::VIMEO_GROUP_VALUE => org_tubepress_impl_options_ui_fields_TextField::__),
                
            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_SEARCH =>
                array(org_tubepress_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE => org_tubepress_impl_options_ui_fields_TextField::__),

            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_LIKES =>
                array(org_tubepress_api_const_options_names_GallerySource::VIMEO_LIKES_VALUE => org_tubepress_impl_options_ui_fields_TextField::__),
                
            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_APPEARS_IN =>
                array(org_tubepress_api_const_options_names_GallerySource::VIMEO_APPEARS_IN_VALUE => org_tubepress_impl_options_ui_fields_TextField::__),
                
            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_CREDITED =>
                array(org_tubepress_api_const_options_names_GallerySource::VIMEO_CREDITED_VALUE => org_tubepress_impl_options_ui_fields_TextField::__),
        );
    }
}
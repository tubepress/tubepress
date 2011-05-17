<?php

require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/single/SimpleSingleVideo.class.php';
class_exists('org_tubepress_api_video_Video') || require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/api/video/Video.class.php';

class org_tubepress_single_VideoTest extends TubePressUnitTest
{
    private $_sut;
    private $_video;

    function setup()
    {
        $this->initFakeIoc();
        $this->_sut = new org_tubepress_impl_single_SimpleSingleVideo();
        org_tubepress_impl_log_Log::setEnabled(false, array());
        $this->_video =  new org_tubepress_api_video_Video();
        $this->_video->setTitle('fake title');
    }
    
    public function getMock($className)
    {
        $mock = parent::getMock($className);

        switch ($className) {
            case 'org_tubepress_api_provider_Provider':
                $mock->expects($this->any())
                    ->method('getSingleVideo')
                    ->will($this->returnValue($this->_video));
                break;
            case 'org_tubepress_api_plugin_PluginManager':
                $mock->expects($this->exactly(2))
                     ->method('runFilters')
                     ->will($this->returnCallback(array($this, 'callback')));
        }

        return $mock;
    }

    function testGetHtml()
    {
        $result = $this->_sut->getSingleVideoHtml('someid');
        
        $this->assertEquals($this->expected(), $result);
    }
    
    function expected()
    {
        return <<<EOT

<div class="tubepress_single_video">
        <div class="tubepress_embedded_title">fake title</div>
    embedded-source
    <dl class="tubepress_meta_group" style="width: embedded-widthpx">
    <dt class="tubepress_meta tubepress_meta_runtime">length-label</dt><dd class="tubepress_meta tubepress_meta_runtime"></dd>
    <dt class="tubepress_meta tubepress_meta_author">author-label</dt><dd class="tubepress_meta tubepress_meta_author"></dd>
    <dt class="tubepress_meta tubepress_meta_keywords">tags-label</dt><dd class="tubepress_meta tubepress_meta_keywords"></dd>
    <dt class="tubepress_meta tubepress_meta_url">url-label</dt><dd class="tubepress_meta tubepress_meta_url"><a rel="external nofollow" href="">url-label</a></dd>
    <dt class="tubepress_meta tubepress_meta_id">id-label</dt><dd class="tubepress_meta tubepress_meta_id"></dd>
    <dt class="tubepress_meta tubepress_meta_views">views-label</dt><dd class="tubepress_meta tubepress_meta_views"></dd>
    <dt class="tubepress_meta tubepress_meta_uploaddate">uploaded-label</dt><dd class="tubepress_meta tubepress_meta_uploaddate"></dd>
    <dt class="tubepress_meta tubepress_meta_description">description-label</dt><dd class="tubepress_meta tubepress_meta_description"></dd>
</dl>
</div>

EOT;
    }
    
    function callback()
    {
        $args = func_get_args();
        $this->_applyFakeTemplateVariables($args[1]);
        return $args[1];
    }
    
    function _applyFakeTemplateVariables($template)
    {
        if (is_a($template, 'org_tubepress_api_template_Template')) {
            $shouldShow = array();
            $labels = array();
            $names = org_tubepress_impl_options_OptionsReference::getOptionNamesForCategory(org_tubepress_api_const_options_CategoryName::META);
            foreach ($names as $name) {
                $shouldShow[$name] = true;
                $labels[$name] = "$name-label";
            }
            
            $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_SOURCE, 'embedded-source');
            $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_WIDTH, 'embedded-width');
            $template->setVariable(org_tubepress_api_const_template_Variable::META_SHOULD_SHOW, $shouldShow);
            $template->setVariable(org_tubepress_api_const_template_Variable::META_LABELS, $labels);
        }
    }  
}


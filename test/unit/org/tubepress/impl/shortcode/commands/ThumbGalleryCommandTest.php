<?php

require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/impl/shortcode/commands/ThumbGalleryCommand.class.php';
require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/api/const/options/CategoryName.class.php';
require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/api/video/Video.class.php';

class org_tubepress_impl_shortcode_commands_ThumbGalleryCommandTest extends TubePressUnitTest
{
	private $_sut;
	private $_galleryId;
	private $_feedResult;

	function setup()
	{
		parent::setUp();
		$this->_feedResult = new org_tubepress_api_provider_ProviderResult();
		$this->_feedResult->setVideoArray(array(new org_tubepress_api_video_Video()));
		$this->_sut = new org_tubepress_impl_shortcode_commands_ThumbGalleryCommand();
	}

	function getMock($className)
	{
	    $mock = parent::getMock($className);
	    
	    if ($className === 'org_tubepress_api_querystring_QueryStringService') {
	        $mock->expects($this->any())
	             ->method('getGalleryId')
	             ->will($this->returnCallback(array($this, 'galleryIdCallback')));
	    }
	    if ($className === 'org_tubepress_api_provider_Provider') {
	        $mock->expects($this->any())
	             ->method('getMultipleVideos')
	             ->will($this->returnValue($this->_feedResult));
	    }
	    if ($className === 'org_tubepress_api_plugin_PluginManager') {
                $mock->expects($this->exactly(3))
                     ->method('runFilters')
                     ->will($this->returnCallback(array($this, 'callback')));
	    }
	    
	    return $mock;
	}
	
	function galleryIdCallback()
	{
	    return $this->_galleryId;
	}
	
	function testExecute()
	{
	    
	    $this->_galleryId = '390298742';
	    $result = $this->_sut->execute(new org_tubepress_impl_shortcode_ShortcodeHtmlGenerationChainContext());
	    
	    $this->assertEquals($this->expected(), $result);
	    
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
            
            $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_IMPL_NAME, 'embedded-impl-name');
            $template->setVariable(org_tubepress_api_const_template_Variable::PLAYER_HTML, 'player-html');
            $template->setVariable(org_tubepress_api_const_template_Variable::PLAYER_NAME, 'player-name');
            $template->setVariable(org_tubepress_api_const_template_Variable::META_SHOULD_SHOW, $shouldShow);
            $template->setVariable(org_tubepress_api_const_template_Variable::META_LABELS, $labels);
            $template->setVariable(org_tubepress_api_const_template_Variable::GALLERY_ID, '390298742');
            $template->setVariable(org_tubepress_api_const_template_Variable::VIDEO_ARRAY, array(new org_tubepress_api_video_Video()));
            $template->setVariable(org_tubepress_api_const_template_Variable::THUMBNAIL_HEIGHT, 90);
            $template->setVariable(org_tubepress_api_const_template_Variable::THUMBNAIL_WIDTH, 120);
            
        }
    }    
    
    function expected()
    {
        return <<<EOT

<div class="tubepress_container" id="tubepress_gallery_390298742">
  player-html  
  <div id="tubepress_gallery_390298742_thumbnail_area" class="tubepress_thumbnail_area">
    <div class="tubepress_thumbs">
      <div class="tubepress_thumb">
        <a id="tubepress_image__390298742" rel="tubepress_embedded-impl-name_player-name_390298742"> 
          <img alt="" src="" width="120" height="90" />
        </a>
        <dl class="tubepress_meta_group">
          <dt class="tubepress_meta tubepress_meta_title">title-label</dt><dd class="tubepress_meta tubepress_meta_title"><a id="tubepress_title__390298742" rel="tubepress_embedded-impl-name_player-name_390298742"></a></dd>
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
    </div>
      </div>
</div>

EOT;
    }
    

}

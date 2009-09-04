<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/thumbnail/SimpleThumbnailService.class.php';

class org_tubepress_thumbnail_SimpleThumbnailServiceTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	private $_tpom;
	private $_messageService;
	private $_template;
	
	function setUp()
	{
		$this->_tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$this->_messageService = $this->getMock("org_tubepress_message_MessageService");
		$this->_template = $this->getMock('org_tubepress_template_Template');
		$this->_sut = new org_tubepress_thumbnail_SimpleThumbnailService();
	}
	
	function testGetHtml()
	{
		
		$this->_tpom->expects($this->any())
					->method("get")
					->will($this->returnCallback("stptsCallback"));			  

		$this->_template->expects($this->once())
		                ->method('getHtml')
		                ->will($this->returnValue('stuff'));			
					
		$this->_sut->setMessageService($this->_messageService);
		$this->_sut->setOptionsManager($this->_tpom);
		$this->_sut->setTemplate($this->_template);
		
		$vid = new org_tubepress_video_Video();
		$vid->setId("fakeid");
		$vid->setTitle("Fake title");
		$vid->setLength("1:50");
		$vid->setAuthor("3hough");
		$vid->setDescription("Fake description.");
		$vid->setTags(array("foo", "bar"));
		$vid->setYouTubeUrl("youtube url");
		$vid->setRating("4.5");
		$vid->setRatings("1000");
		
        $this->assertEquals('stuff', $this->_sut->getHtml($vid, 500));		
	}
}

function stptsCallback()
{
	$args = func_get_args();
   	$vals = array(
		org_tubepress_options_category_Advanced::RANDOM_THUMBS => false,
		org_tubepress_options_category_Display::THUMB_WIDTH => 40,
		org_tubepress_options_category_Display::THUMB_HEIGHT => 100,
		org_tubepress_options_category_Display::DESC_LIMIT => 20,
		org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT => 500,
		org_tubepress_options_category_Embedded::EMBEDDED_WIDTH => 600,
		org_tubepress_options_category_Advanced::NOFOLLOW_LINKS => false,
		org_tubepress_options_category_Meta::TITLE => true,
		org_tubepress_options_category_Meta::LENGTH => true,
		org_tubepress_options_category_Meta::DESCRIPTION => true,
		org_tubepress_options_category_Meta::AUTHOR => true,
		org_tubepress_options_category_Meta::TAGS => true,
		org_tubepress_options_category_Meta::URL => true,
		org_tubepress_options_category_Meta::VIEWS => true,
		org_tubepress_options_category_Meta::ID => true,
		org_tubepress_options_category_Meta::RATING => true,
		org_tubepress_options_category_Meta::RATINGS => true,
		org_tubepress_options_category_Meta::UPLOADED => true,
		org_tubepress_options_category_Display::RELATIVE_DATES => false,
		org_tubepress_options_category_Advanced::DATEFORMAT => "m, d",
		org_tubepress_options_category_Meta::CATEGORY => true,
		org_tubepress_options_category_Display::CURRENT_PLAYER_NAME => 'normal',
		org_tubepress_options_category_Embedded::PLAYER_IMPL => 'youtube'
	);
	return $vals[$args[0]];
}
?>
<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/thumbnail/SimpleThumbnailService.class.php';

class org_tubepress_thumbnail_SimpleThumbnailServiceTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	private $_tpom;
	private $_messageService;
	private $_player;
	
	function setUp()
	{
		$this->_tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$this->_messageService = $this->getMock("org_tubepress_message_MessageService");
		$this->_player = $this->getMock("org_tubepress_player_Player");
		$this->_sut = new org_tubepress_thumbnail_SimpleThumbnailService();
	}
	
	function testGetHtml()
	{
		
		$this->_tpom->expects($this->any())
					->method("get")
					->will($this->returnCallback("stptsCallback"));			  
					  
		$this->_sut->setMessageService($this->_messageService);
		$this->_sut->setOptionsManager($this->_tpom);
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
	
		
		$this->assertEquals(<<<EOT
<div class="tubepress_thumb">
	<div class="tubepress_thumb_inner" style="width: 40px">
		<a id="tubepress_image_fakeid_Object id #209" rel="tubepress_youtube_normal_Object id #209"> <img alt="Fake title" src="http://img.youtube.com/vi/fakeid/default.jpg" width="40" height="100" /></a>
		<div class="tubepress_meta_group">
			<div class="tubepress_meta_title"><a id="tubepress_title_fakeid_Object id #209" rel="tubepress_youtube_normal_Object id #209">Fake title</a><br /></div>
			<span class="tubepress_meta_runtime"> 1:50 </span><br />
			<span class="tubepress_meta">: </span>
			<a  href="http://www.youtube.com/profile?user=3hough">3hough</a><br />
			<span class="tubepress_meta">: </span>
			<a  href="http://youtube.com/results?search_query=foo%20bar&amp;search=Search">foo bar</a><br />
			<a  href="youtube url"></a><br />
			<span class="tubepress_meta">: </span>fakeid<br />
			<span class="tubepress_meta">: </span>4.5<br />
			<span class="tubepress_meta">: </span>1000<br />
			<span class="tubepress_meta">: </span>12, 31<br />
			Fake description. 
		</div>
	</div>
</div>

EOT
		, $this->_sut->getHtml(dirname(__FILE__) . "/../../../../../ui/gallery/html_templates", $vid, $this->_player));
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
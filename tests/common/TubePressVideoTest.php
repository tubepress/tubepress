<?php

require_once(dirname(__FILE__) . '/../../common/class/TubePressVideo.php');
require_once(dirname(__FILE__) . '/../../common/defines.php');

class TubePressVideoTest extends PHPUnit_Framework_TestCase {
	var $v;
	
	function testNonArrayConst() {
		$this->assertTrue(PEAR::isError(new TubePressVideo("sldkf")));
	}
	function testInvalidArrayConst() {
		$this->assertTrue(PEAR::isError(new TubePressVideo($this->v)));
	}
	function testCorrectConst() {
		$this->v['author'] = "3hough";
		$this->assertFalse(PEAR::isError(new TubePressVideo($this->v)));
	}
	
	function setUp() {
		$this->v['id'] = "oU-qqkWOKJk";
		$this->v['title'] = "Scuba in the Bahamas";
		$this->v['length_seconds'] = "13";
		$this->v['rating_avg'] = "0.00";
		$this->v['rating_count'] = "0";
		$this->v['description'] = "Katie filmed me jumpin in";
		$this->v['view_count'] = "31";
		$this->v['upload_time'] = "1177894154";
		$this->v['comment_count'] = "0";
		$this->v['tags'] = "scuba bahamas";
		$this->v['url'] = "http://www.youtube.com/?v=oU-qqkWOKJk";
		$this->v['thumbnail_url'] = "http://img.youtube.com/vi/oU-qqkWOKJk/2.jpg";
		$this->v['embed_status'] = "ok";
	}
}
?>

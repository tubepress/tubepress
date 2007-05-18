<?php

require(dirname(__FILE__) . '/../../common/TubePressXML.php');
require_once(dirname(__FILE__) . '/../../common/TubePressOptionsPackage.php');
require_once(dirname(__FILE__) . '/../../lib/PEAR/Networking/Net_URL/URL.php');

require(dirname(__FILE__) . '/../../tp_strings.php');

require("PEAR.php");

class TubePressXMLTest extends UnitTestCase {
	var $normalXML;
	var $errorXML;
	var $malFormedXML;
	var $fakeOpts;
	
	function testGenRequestPlaylist() {
		
		/* test playlist mode */
		$result = $this->fakeOpts->setValue(TP_OPT_SEARCHBY, TP_SRCH_PLST);
		$this->assertFalse(PEAR::isError($result));
		$result = TubePressXML::generateRequest($this->fakeOpts);
		$this->assertFalse(PEAR::isError($result), $result->message);
		$url = new Net_Url($result);
		TubePressXMLTest::_testBasics($url);
		$this->assertTrue(count($url->querystring) == 3);
		$this->assertEqual($url->querystring["method"], "youtube.videos.list_by_playlist");
		$this->assertEqual($url->querystring["id"], "D2B04665B213AE35");
		$this->assertEqual($url->querystring["dev_id"], "qh7CQ9xJIIc");
	}
	function testGenRequestUser() {
		$result = $this->fakeOpts->setValue(TP_OPT_SEARCHBY, TP_SRCH_USER);
		$this->assertFalse(PEAR::isError($result));
		$result = TubePressXML::generateRequest($this->fakeOpts);
		$this->assertFalse(PEAR::isError($result));
		$url = new Net_Url($result);
		TubePressXMLTest::_testBasics($url);
		$this->assertTrue(count($url->querystring) == 5);
		$this->assertEqual($url->querystring["method"], "youtube.videos.list_by_user");
		$this->assertEqual($url->querystring["user"], $this->fakeOpts->getValue(TP_SRCH_USERVAL));
		$this->assertEqual($url->querystring["dev_id"], "qh7CQ9xJIIc");
		$this->assertEqual($url->querystring["page"], "1");
		$this->assertEqual($url->querystring["per_page"], "20");
	}
	
	function testGenRequestFavorites() {	
		/* test favorites mode */
		$result = $this->fakeOpts->setValue(TP_OPT_SEARCHBY, TP_SRCH_FAV);
		$this->assertFalse(PEAR::isError($result));
		$result = TubePressXML::generateRequest($this->fakeOpts);
		$this->assertFalse(PEAR::isError($result));
		$url = new Net_Url($result);
		TubePressXMLTest::_testBasics($url);
		$this->assertTrue(count($url->querystring) == 3);
		$this->assertEqual($url->querystring["method"], "youtube.users.list_favorite_videos");
		$this->assertEqual($url->querystring["user"], $this->fakeOpts->getValue(TP_SRCH_FAVVAL));
		$this->assertEqual($url->querystring["dev_id"], "qh7CQ9xJIIc");
	}

	function testGenRequestTag() {		
		/* test tag mode */
		$result = $this->fakeOpts->setValue(TP_OPT_SEARCHBY, TP_SRCH_TAG);
		$this->assertFalse(PEAR::isError($result));
		$result = TubePressXML::generateRequest($this->fakeOpts);
		$this->assertFalse(PEAR::isError($result));
		$url = new Net_Url($result);
		TubePressXMLTest::_testBasics($url);
		$this->assertTrue(count($url->querystring) == 5);
		$this->assertEqual($url->querystring["method"], "youtube.videos.list_by_tag");
		$this->assertEqual($url->querystring["tag"], urlencode($this->fakeOpts->getValue(TP_SRCH_TAGVAL)));
		$this->assertEqual($url->querystring["dev_id"], "qh7CQ9xJIIc");
		$this->assertEqual($url->querystring["page"], "1");
		$this->assertEqual($url->querystring["per_page"], "20");
	}
	
	function testGenRequestRelated() {		
		/* test related mode */
		$result = $this->fakeOpts->setValue(TP_OPT_SEARCHBY, TP_SRCH_REL);
		$this->assertFalse(PEAR::isError($result));
		$result = TubePressXML::generateRequest($this->fakeOpts);
		$this->assertFalse(PEAR::isError($result));
		$url = new Net_Url($result);
		TubePressXMLTest::_testBasics($url);
		$this->assertTrue(count($url->querystring) == 5);
		$this->assertEqual($url->querystring["method"], "youtube.videos.list_by_related");
		$this->assertEqual($url->querystring["tag"], urlencode($this->fakeOpts->getValue(TP_SRCH_RELVAL)));
		$this->assertEqual($url->querystring["dev_id"], "qh7CQ9xJIIc");
		$this->assertEqual($url->querystring["page"], "1");
		$this->assertEqual($url->querystring["per_page"], "20");
	}

	function testGenRequestPopular() {		
		/* test popular mode */
		$result = $this->fakeOpts->setValue(TP_OPT_SEARCHBY, TP_SRCH_POPULAR);
		$this->assertFalse(PEAR::isError($result));
		$result = TubePressXML::generateRequest($this->fakeOpts);
		$this->assertFalse(PEAR::isError($result));
		$url = new Net_Url($result);
		TubePressXMLTest::_testBasics($url);
		$this->assertTrue(count($url->querystring) == 3);
		$this->assertEqual($url->querystring["method"], "youtube.videos.list_popular");
		$this->assertEqual($url->querystring["time_range"], $this->fakeOpts->getValue(TP_SRCH_POPVAL));
		$this->assertEqual($url->querystring["dev_id"], "qh7CQ9xJIIc");
	}

	function testGenRequestCat() {		
		/* test category mode */
		$result = $this->fakeOpts->setValue(TP_OPT_SEARCHBY, TP_SRCH_CATEGORY);
		$this->assertFalse(PEAR::isError($result));
		$result = TubePressXML::generateRequest($this->fakeOpts);
		$this->assertFalse(PEAR::isError($result));
		$url = new Net_Url($result);
		TubePressXMLTest::_testBasics($url);
		$this->assertTrue(count($url->querystring) == 5);
		$this->assertEqual($url->querystring["method"], "youtube.videos.list_by_category");
		$this->assertEqual($url->querystring["page"], "1");
		$this->assertEqual($url->querystring["per_page"], $this->fakeOpts->getValue(TP_OPT_VIDSPERPAGE));
		$this->assertEqual($url->querystring["category_id"], $this->fakeOpts->getValue(TP_SRCH_CATVAL));
		$this->assertEqual($url->querystring["dev_id"], "qh7CQ9xJIIc");
	}

	function testGenRequestFeatured() {		
		/* test featured mode */
		$result = $this->fakeOpts->setValue(TP_OPT_SEARCHBY, TP_SRCH_FEATURED);
		$this->assertFalse(PEAR::isError($result));
		$result = TubePressXML::generateRequest($this->fakeOpts);
		$this->assertFalse(PEAR::isError($result));
		$url = new Net_Url($result);
		TubePressXMLTest::_testBasics($url);
		$this->assertTrue(count($url->querystring) == 2);
		$this->assertEqual($url->querystring["method"], "youtube.videos.list_featured");
		$this->assertEqual($url->querystring["dev_id"], "qh7CQ9xJIIc");
	}
	
	function _testBasics(&$url) {
		$this->assertEqual($url->host, "www.youtube.com");
		$this->assertEqual($url->port, "80");
		$this->assertEqual($url->path, "/api2_rest");
	}
	
	function testErrorXML() {
		$result = TubePressXML::parseRawXML($this->errorXML);
		$this->assertTrue(PEAR::isError($result));
	}
	
	function testMalformedXML() {
		$result = TubePressXML::parseRawXML($this->malFormedXML);
		$this->assertTrue(PEAR::isError($result));
	}
	
	function testNormalXML() {
		$result = TubePressXML::parseRawXML($this->normalXML);
		$this->assertFalse(PEAR::isError($result));
		$this->assertTrue(is_array($result));
		$this->assertTrue(count($result) == 2);
		$this->assertTrue(array_key_exists('total', $result));
		$this->assertTrue(array_key_exists('video', $result));
		$this->assertTrue(is_array($result['video']));
		$this->assertTrue($result['total'] == "2");
		$this->assertTrue($result['total'] == count($result['video']));
		
		$v = $result['video'][0];
		
		$this->assertTrue(count($v) == 14);
		$this->assertTrue(array_key_exists('author', $v));
		$this->assertTrue(array_key_exists('id', $v));
		$this->assertTrue(array_key_exists('title', $v));
		$this->assertTrue(array_key_exists('length_seconds', $v));
		$this->assertTrue(array_key_exists('rating_avg', $v));
		$this->assertTrue(array_key_exists('rating_count', $v));
		$this->assertTrue(array_key_exists('description', $v));
		$this->assertTrue(array_key_exists('view_count', $v));
		$this->assertTrue(array_key_exists('upload_time', $v));
		$this->assertTrue(array_key_exists('comment_count', $v));
		$this->assertTrue(array_key_exists('tags', $v));
		$this->assertTrue(array_key_exists('url', $v));
		$this->assertTrue(array_key_exists('thumbnail_url', $v));
		$this->assertTrue(array_key_exists('embed_status', $v));
		
		$this->assertEqual($v['author'], "3hough");
		$this->assertEqual($v['id'], "oU-qqkWOKJk");
		$this->assertEqual($v['title'], "Scuba in the Bahamas");
		$this->assertEqual($v['length_seconds'], "13");
		$this->assertEqual($v['rating_avg'], "0.00");
		$this->assertEqual($v['rating_count'], "0");
		$this->assertEqual($v['description'], "Katie filmed me jumpin in");
		$this->assertEqual($v['view_count'], "31");
		$this->assertEqual($v['upload_time'], "1177894154");
		$this->assertEqual($v['comment_count'], "0");
		$this->assertEqual($v['tags'], "scuba bahamas");
		$this->assertEqual($v['url'], "http://www.youtube.com/?v=oU-qqkWOKJk");
		$this->assertEqual($v['thumbnail_url'], "http://img.youtube.com/vi/oU-qqkWOKJk/2.jpg");
		$this->assertEqual($v['embed_status'], "ok");
	}
	
	function setUp() {
		$opts = TubePressOptionsPackage::getDefaultPackage();
		$this->fakeOpts = new TubePressOptionsPackage();
		$this->fakeOpts->_allOptions = $opts;
		
				$this->malformedXML = '<?xml version="1.0" encoding="utf-8"?>' .
				'<ut_response status="ok">' .
				'<video_list><total>2</total><video><author>3hough</author>' .
				'<id>oU-qqkWOKJk</id>' .
				'<title>Scuba in the Bahamas</title>' .
				'<length_seconds>13</length_seconds>' .
				'<rating_avg>0.00</rating_avg>' .
				'<rating_count0</rating_count>' .
				'<description>Katie filmed me jumpin in</description>' .
				'<view_count>31</view_count>' .
				'<upload_time>1177894154</upload_time>' .
				'<comment_count>0</comment_count>' .
				'<tags>scuba bahamas</tags>' .
				'<url>http://www.youtube.com/?v=oU-qqkWOKJk</url>' .
				'<thumbnail_url>http://img.youtube.com/vi/oU-qqkWOKJk/2.jpg</thumbnail_url>' .
				'<embed_status>ok</embed_status></video><video><author>3hough</author><id>65PFepXO9sM</id><title>Speeding in the orange Mustang</title><length_seconds>38</length_seconds><rating_avg>0.00</rating_avg><rating_count>0</rating_count><description>I get some air towards the end of the video. Probably not the safest thing I\'ve done.</description><view_count>95</view_count><upload_time>1156808174</upload_time><comment_count>0</comment_count><tags>driving</tags><url>http://www.youtube.com/?v=65PFepXO9sM</url><thumbnail_url>http://img.youtube.com/vi/65PFepXO9sM/2.jpg</thumbnail_url><embed_status>ok</embed_status></video></video_list></ut_response>';
				
		$this->normalXML = '<?xml version="1.0" encoding="utf-8"?>' .
				'<ut_response status="ok">' .
				'<video_list><total>2</total><video><author>3hough</author>' .
				'<id>oU-qqkWOKJk</id>' .
				'<title>Scuba in the Bahamas</title>' .
				'<length_seconds>13</length_seconds>' .
				'<rating_avg>0.00</rating_avg>' .
				'<rating_count>0</rating_count>' .
				'<description>Katie filmed me jumpin in</description>' .
				'<view_count>31</view_count>' .
				'<upload_time>1177894154</upload_time>' .
				'<comment_count>0</comment_count>' .
				'<tags>scuba bahamas</tags>' .
				'<url>http://www.youtube.com/?v=oU-qqkWOKJk</url>' .
				'<thumbnail_url>http://img.youtube.com/vi/oU-qqkWOKJk/2.jpg</thumbnail_url>' .
				'<embed_status>ok</embed_status></video><video><author>3hough</author><id>65PFepXO9sM</id><title>Speeding in the orange Mustang</title><length_seconds>38</length_seconds><rating_avg>0.00</rating_avg><rating_count>0</rating_count><description>I get some air towards the end of the video. Probably not the safest thing I\'ve done.</description><view_count>95</view_count><upload_time>1156808174</upload_time><comment_count>0</comment_count><tags>driving</tags><url>http://www.youtube.com/?v=65PFepXO9sM</url><thumbnail_url>http://img.youtube.com/vi/65PFepXO9sM/2.jpg</thumbnail_url><embed_status>ok</embed_status></video></video_list></ut_response>';
				
		$this->errorXML = '<?xml version="1.0" encoding="utf-8"?>' .
				'<ut_response status="fail">' .
				'<error><code>6</code>' .
				'<description>Unknown method specified.</description>' .
				'</error></ut_response>';
	}
}
?>

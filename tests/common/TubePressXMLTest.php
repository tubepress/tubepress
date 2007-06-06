<?php

require(dirname(__FILE__) . '/../../common/class/TubePressXML.php');
require_once(dirname(__FILE__) . '/../../common/class/TubePressOptionsPackage.php');
require_once(dirname(__FILE__) . '/../../lib/PEAR/Networking/Net_URL/URL.php');

require_once(dirname(__FILE__) . '/../../common/defines.php');

class TubePressXMLTest extends PHPUnit_Framework_TestCase {
	var $normalXML;
	var $errorXML;
	var $malFormedXML;
	var $fakeOpts;
	
	function testGenRequestPlaylist() {
		
		/* test playlist mode */
		$result = $this->fakeOpts->setValue(TP_OPT_MODE, TP_MODE_PLST);
		$this->assertFalse(PEAR::isError($result));
		$result = TubePressXML::generateRequest($this->fakeOpts);
		$this->assertFalse(PEAR::isError($result), $result->message);
		$url = new Net_Url($result);
		TubePressXMLTest::_testBasics($url);
		$this->assertTrue(count($url->querystring) == 3);
		$this->assertEquals    ($url->querystring["method"], "youtube.videos.list_by_playlist");
		$this->assertEquals    ($url->querystring["id"], "D2B04665B213AE35");
		$this->assertEquals    ($url->querystring["dev_id"], "qh7CQ9xJIIc");
	}
	function testGenRequestUser() {
		$result = $this->fakeOpts->setValue(TP_OPT_MODE, TP_MODE_USER);
		$this->assertFalse(PEAR::isError($result));
		$result = TubePressXML::generateRequest($this->fakeOpts);
		$this->assertFalse(PEAR::isError($result));
		$url = new Net_Url($result);
		TubePressXMLTest::_testBasics($url);
		$this->assertTrue(count($url->querystring) == 5);
		$this->assertEquals    ($url->querystring["method"], "youtube.videos.list_by_user");
		$this->assertEquals    ($url->querystring["user"], $this->fakeOpts->getValue(TP_OPT_USERVAL));
		$this->assertEquals    ($url->querystring["dev_id"], "qh7CQ9xJIIc");
		$this->assertEquals    ($url->querystring["page"], "1");
		$this->assertEquals    ($url->querystring["per_page"], "20");
	}
	
	function testGenRequestFavorites() {	
		/* test favorites mode */
		$result = $this->fakeOpts->setValue(TP_OPT_MODE, TP_MODE_FAV);
		$this->assertFalse(PEAR::isError($result));
		$result = TubePressXML::generateRequest($this->fakeOpts);
		$this->assertFalse(PEAR::isError($result));
		$url = new Net_Url($result);
		TubePressXMLTest::_testBasics($url);
		$this->assertTrue(count($url->querystring) == 3);
		$this->assertEquals    ($url->querystring["method"], "youtube.users.list_favorite_videos");
		$this->assertEquals    ($url->querystring["user"], $this->fakeOpts->getValue(TP_OPT_FAVVAL));
		$this->assertEquals    ($url->querystring["dev_id"], "qh7CQ9xJIIc");
	}

	function testGenRequestTag() {		
		/* test tag mode */
		$result = $this->fakeOpts->setValue(TP_OPT_MODE, TP_MODE_TAG);
		$this->assertFalse(PEAR::isError($result));
		$result = TubePressXML::generateRequest($this->fakeOpts);
		$this->assertFalse(PEAR::isError($result));
		$url = new Net_Url($result);
		TubePressXMLTest::_testBasics($url);
		$this->assertTrue(count($url->querystring) == 5);
		$this->assertEquals    ($url->querystring["method"], "youtube.videos.list_by_tag");
		$this->assertEquals    ($url->querystring["tag"], urlencode($this->fakeOpts->getValue(TP_OPT_TAGVAL)));
		$this->assertEquals    ($url->querystring["dev_id"], "qh7CQ9xJIIc");
		$this->assertEquals    ($url->querystring["page"], "1");
		$this->assertEquals    ($url->querystring["per_page"], "20");
	}
	
	function testGenRequestRelated() {		
		/* test related mode */
		$result = $this->fakeOpts->setValue(TP_OPT_MODE, TP_MODE_REL);
		$this->assertFalse(PEAR::isError($result));
		$result = TubePressXML::generateRequest($this->fakeOpts);
		$this->assertFalse(PEAR::isError($result));
		$url = new Net_Url($result);
		TubePressXMLTest::_testBasics($url);
		$this->assertTrue(count($url->querystring) == 5);
		$this->assertEquals    ($url->querystring["method"], "youtube.videos.list_by_related");
		$this->assertEquals    ($url->querystring["tag"], urlencode($this->fakeOpts->getValue(TP_OPT_RELVAL)));
		$this->assertEquals    ($url->querystring["dev_id"], "qh7CQ9xJIIc");
		$this->assertEquals    ($url->querystring["page"], "1");
		$this->assertEquals    ($url->querystring["per_page"], "20");
	}

	function testGenRequestPopular() {		
		/* test popular mode */
		$result = $this->fakeOpts->setValue(TP_OPT_MODE, TP_MODE_POPULAR);
		$this->assertFalse(PEAR::isError($result));
		$result = TubePressXML::generateRequest($this->fakeOpts);
		$this->assertFalse(PEAR::isError($result));
		$url = new Net_Url($result);
		TubePressXMLTest::_testBasics($url);
		$this->assertTrue(count($url->querystring) == 3);
		$this->assertEquals    ($url->querystring["method"], "youtube.videos.list_popular");
		$this->assertEquals    ($url->querystring["time_range"], $this->fakeOpts->getValue(TP_OPT_POPVAL));
		$this->assertEquals    ($url->querystring["dev_id"], "qh7CQ9xJIIc");
	}

	function testGenRequestCat() {		
		/* test category mode */
		$result = $this->fakeOpts->setValue(TP_OPT_MODE, TP_MODE_CATEGORY);
		$this->assertFalse(PEAR::isError($result));
		$result = TubePressXML::generateRequest($this->fakeOpts);
		$this->assertFalse(PEAR::isError($result));
		$url = new Net_Url($result);
		TubePressXMLTest::_testBasics($url);
		$this->assertTrue(count($url->querystring) == 5);
		$this->assertEquals    ($url->querystring["method"], "youtube.videos.list_by_category");
		$this->assertEquals    ($url->querystring["page"], "1");
		$this->assertEquals    ($url->querystring["per_page"], $this->fakeOpts->getValue(TP_OPT_VIDSPERPAGE));
		$this->assertEquals    ($url->querystring["category_id"], $this->fakeOpts->getValue(TP_OPT_CATVAL));
		$this->assertEquals    ($url->querystring["dev_id"], "qh7CQ9xJIIc");
	}

	function testGenRequestFeatured() {		
		/* test featured mode */
		$result = $this->fakeOpts->setValue(TP_OPT_MODE, TP_MODE_FEATURED);
		$this->assertFalse(PEAR::isError($result));
		$result = TubePressXML::generateRequest($this->fakeOpts);
		$this->assertFalse(PEAR::isError($result));
		$url = new Net_Url($result);
		TubePressXMLTest::_testBasics($url);
		$this->assertTrue(count($url->querystring) == 2);
		$this->assertEquals    ($url->querystring["method"], "youtube.videos.list_featured");
		$this->assertEquals    ($url->querystring["dev_id"], "qh7CQ9xJIIc");
	}
	
	function _testBasics(&$url) {
		$this->assertEquals    ($url->host, "www.youtube.com");
		$this->assertEquals    ($url->port, "80");
		$this->assertEquals    ($url->path, "/api2_rest");
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
		
		$this->assertEquals    ($v['author'], "3hough");
		$this->assertEquals    ($v['id'], "oU-qqkWOKJk");
		$this->assertEquals    ($v['title'], "Scuba in the Bahamas");
		$this->assertEquals    ($v['length_seconds'], "13");
		$this->assertEquals    ($v['rating_avg'], "0.00");
		$this->assertEquals    ($v['rating_count'], "0");
		$this->assertEquals    ($v['description'], "Katie filmed me jumpin in");
		$this->assertEquals    ($v['view_count'], "31");
		$this->assertEquals    ($v['upload_time'], "1177894154");
		$this->assertEquals    ($v['comment_count'], "0");
		$this->assertEquals    ($v['tags'], "scuba bahamas");
		$this->assertEquals    ($v['url'], "http://www.youtube.com/?v=oU-qqkWOKJk");
		$this->assertEquals    ($v['thumbnail_url'], "http://img.youtube.com/vi/oU-qqkWOKJk/2.jpg");
		$this->assertEquals    ($v['embed_status'], "ok");
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

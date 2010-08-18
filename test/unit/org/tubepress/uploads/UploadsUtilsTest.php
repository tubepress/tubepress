<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/uploads/UploadsUtils.class.php';
require_once dirname(__FILE__) . '/../../../../../test/unit/TubePressUnitTest.php';

class org_tubepress_uploads_UploadsUtilsTest extends TubePressUnitTest
{
	public function testGetGalleryIdFromVideoIdNoSlash()
	{
		$result = org_tubepress_uploads_UploadsUtils::getGalleryNameFromVideoId('noslash');
		$this->assertEquals('', $result);
	}

	public function testGetGalleryIdFromVideoIdSlash()
	{
		$result = org_tubepress_uploads_UploadsUtils::getGalleryNameFromVideoId('noslash/something.mov');
		$this->assertEquals('noslash', $result);
	}

	public function testCorrectlyIdentifiesNonVideosNotExist()
	{
		$result = org_tubepress_uploads_UploadsUtils::isPossibleVideo('nosuchfile', 'something');
		$this->assertFalse($result);
	}

	public function testCorrectlyIdentifiesNonVideosTooSmall()
	{
		$file = $temp_file = tempnam(sys_get_temp_dir(), 'tubepress');
		$result = org_tubepress_uploads_UploadsUtils::isPossibleVideo($file, 'something');
		unlink($file);
		$this->assertFalse($result);
	}

	public function testCorrectlyIdentifiesPossibleVideos()
	{
		$file = $temp_file = tempnam(sys_get_temp_dir(), 'tubepress');
		$hugeString = $this->rand_str(12000);
		file_put_contents($file, $hugeString);
		$result = org_tubepress_uploads_UploadsUtils::isPossibleVideo($file, 'something');
		unlink($file);
		$this->assertTrue($result);
	}

	public function testListVideosInDir()
	{
		$dir = self::getDirectoryOfFakeVideos();
		$result = org_tubepress_uploads_UploadsUtils::findVideos($dir, 'prefix');
		$this->assertTrue(is_array($result));
		$this->assertTrue(sizeof($result) === 1);
	}

	private function getDirectoryOfFakeVideos()
	{
		$dir = sys_get_temp_dir() . '/tubepress-test-' . time();
		mkdir($dir);
		$vid = tempnam($dir, 'tubepress');
		file_put_contents($vid, $this->rand_str(12000));
		return $dir;
	}

	private function rand_str($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
	{
	    // Length of character list
	    $chars_length = (strlen($chars) - 1);

	    // Start our string
	    $string = $chars{rand(0, $chars_length)};
	    
	    // Generate random string
	    for ($i = 1; $i < $length; $i = strlen($string))
	    {
		// Grab a random character from our list
		$r = $chars{rand(0, $chars_length)};
		
		// Make sure the same two characters don't appear next to each other
		if ($r != $string{$i - 1}) $string .=  $r;
	    }
	    
	    // Return the string
	    return $string;
	}
    
}
?>

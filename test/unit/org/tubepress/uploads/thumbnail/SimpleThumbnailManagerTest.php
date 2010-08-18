<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/uploads/thumbnail/SimpleThumbnailManager.class.php';
require_once dirname(__FILE__) . '/../../../../../../test/unit/TubePressUnitTest.php';

class org_tubepress_uploads_thumbnail_SimpleThumbnailManagerTest extends TubePressUnitTest
{
    private $_sut;
    
    public function setup()
    {
	$this->initFakeIoc();
        $this->_sut = new org_tubepress_uploads_thumbnail_SimpleThumbnailManager();
    }
    
	public function testGetExistingThumbs()
	{
		$result = $this->_sut->getExistingThumbnails('sdfdf');
		$this->assertTrue(is_array($result));
                $this->assertTrue(empty($result));
	}

    
}
?>

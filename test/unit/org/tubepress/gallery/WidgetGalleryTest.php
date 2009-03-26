<?php
require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/gallery/WidgetGallery.class.php';

require_once 'AbstractGalleryTest.php';

class org_tubepress_gallery_WidgetGalleryTest extends org_tubepress_gallery_AbstractGalleryTest {
    
	function setUp()
	{
		$this->_sut = new org_tubepress_gallery_WidgetGallery();
		parent::finishSetUp(<<<EOT
pregallerystuff
stuffstuffstuff
EOT
);
	}
	
}
?>
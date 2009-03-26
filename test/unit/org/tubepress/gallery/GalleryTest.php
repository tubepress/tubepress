<?php
require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/gallery/Gallery.class.php';
require_once 'AbstractGalleryTest.php';

class org_tubepress_gallery_GalleryTest extends org_tubepress_gallery_AbstractGalleryTest {
    
	function setUp()
	{
		$this->_sut = new org_tubepress_gallery_Gallery();
		parent::finishSetUp(<<<EOT
<div class="tubepress_container">
    <a name="tubepress_gallery_911090766" id="tubepress_gallery_911090766" style="visibility:hidden"> </a>
	pregallerystuff
	<div class="pagination">
	    Fakepagination
	</div>
	<div class="tubepress_thumbs">
		stuffstuffstuff
	</div>
	<div class="pagination">
	    Fakepagination
	</div>
</div>

EOT
);
	}
	
}
?>

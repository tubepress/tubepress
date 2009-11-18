<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/embedded/impl/JwFlvEmbeddedPlayerService.class.php';
require_once 'AbstractEmbeddedPlayerServiceTest.php';

class org_tubepress_embedded_impl_JwFlvEmbeddedPlayerServiceTest extends org_tubepress_embedded_impl_AbstractEmbeddedPlayerServiceTest {
    
	function setUp()
	{
	    parent::parentSetUp(new org_tubepress_embedded_impl_JwFlvEmbeddedPlayerService(), 3);
	}
}
?>
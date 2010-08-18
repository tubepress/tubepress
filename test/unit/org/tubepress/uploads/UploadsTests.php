<?php
require dirname(__FILE__) . '/../../../PhpUnitLoader.php';
require_once 'thumbnail/SimpleThumbnailManagerTest.php';
require_once 'UploadsUtilsTest.php';

class UploadsTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Tubepress Upload Tests');
        $suite->addTestSuite('org_tubepress_uploads_thumbnail_SimpleThumbnailManagerTest');
	$suite->addTestSuite('org_tubepress_uploads_UploadsUtilsTest');
        return $suite;
    }
}
?>

<?php
class TubePressTests extends GroupTest {
    function tubepressTests() {

        include dirname(__FILE__) . "/../tubepress_classloader.php";
        
        parent::GroupTest('');
        $this->addTestFile(dirname(__FILE__) . "/common/class/TubePressVideoTest.php");
    }
}
?>

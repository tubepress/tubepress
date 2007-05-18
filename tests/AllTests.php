<?php

class TubePressTest extends GroupTest {
  function TubePressTest() {
    parent::GroupTest('');
    $this->addTestFile(dirname(__FILE__).'/common/TubePressOptionsPackageTest.php');
    $this->addTestFile(dirname(__FILE__).'/common/TubePressCSSTest.php');
    $this->addTestFile(dirname(__FILE__).'/common/TubePressXMLTest.php');
    $this->addTestFile(dirname(__FILE__).'/common/TubePressOptionTest.php');
    $this->addTestFile(dirname(__FILE__).'/common/TubePressVideoTest.php');
    
  }
}
?>


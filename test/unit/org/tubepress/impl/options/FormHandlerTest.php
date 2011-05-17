<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/options/FormHandler.class.php';

class org_tubepress_impl_options_FormHandlerTest extends TubePressUnitTest {
    
    private $_stpom;
    
    public function setup()
    {
        parent::setUp();
        $this->_stpom = new org_tubepress_impl_options_FormHandler();
    }
    
    public function testDisplay()
    {
            $this->_stpom->getHtml();
    }
    
    public function testCollect()
    {
        $fakePostVars = array('test', 'two', 'poo');
        $this->assertNull($this->_stpom->collect($fakePostVars));
    }

    public function getMock($class)
    {
        $mock = parent::getMock($class);
        
        switch ($class) {
            case ('org_tubepress_api_filesystem_Explorer'):
                $mock->expects($this->any())
                     ->method('getDirectoriesInDirectory')
                     ->will($this->returnValue(array('poo')));
                break;
        }
        
        return $mock;
    }
    
    private function expected()
    {
        return file_get_contents(dirname(__FILE__) . '/expected.txt');
    }
}


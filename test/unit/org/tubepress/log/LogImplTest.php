<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/log/LogImpl.class.php';

class org_tubepress_log_LogImplTest extends PHPUnit_Framework_TestCase {

    private $_sut;
    
    public function setUp()
    {
        $this->_sut = new org_tubepress_log_LogImpl();
    }
    
    public function testSetEnabled()
    {
        $this->_sut->setEnabled(true, array('tubepress_debug' => 'true'));
        $this->_sut->log('prefix', 'message');
    }
    
    public function testLog()
    {
        $this->_sut->setEnabled(true, array('tubepress_debug' => 'true'));
    }
}
?>
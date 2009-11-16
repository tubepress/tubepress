<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/template/SimpleTemplate.class.php';

class org_tubepress_template_SimpleTemplateTest extends PHPUnit_Framework_TestCase
{
    private $_sut;
    
    public function setUp()
    {
        $this->_sut = new org_tubepress_template_SimpleTemplate();
    }
    
    /**
     * @expectedException Exception
     */    
    public function testSetPathNoSuchFile()
    {
        $this->_sut->setPath(dirname(__FILE__) . '/nosuchfile.php');
    }       
    
    /**
     * @expectedException Exception
     */    
    public function testMissingVariable()
    {
        $this->_sut->setPath(dirname(__FILE__) . '/fake_template.php');
        $this->_sut->toString();
    }  
    
    public function testSetVariable()
    {
        $this->_sut->setPath(dirname(__FILE__) . '/fake_template.php');
        $this->_sut->setVariable('world', 'World!');
        $this->assertEquals('Hello World!', $this->_sut->toString());
    }

    public function testSetVariableNoFile()
    {
        $this->_sut->setVariable('name', 'value');
    }
    
    /**
     * @expectedException Exception
     */
    public function testToStringNoFile()
    {
        $this->_sut->toString();
    }
    
}
?>
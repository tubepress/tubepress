<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/template/TemplateImpl.class.php';

class org_tubepress_template_TemplateImplTest extends PHPUnit_Framework_TestCase
{
    private $_sut;
    
    public function setUp()
    {
        $this->_sut = new org_tubepress_template_TemplateImpl();
    }

    public function testGetHtml()
    {
        $this->_sut->setFile(dirname(__FILE__) . '/faketemplate.txt');
        $this->_sut->setVariable('MYVAR', 'value');
        $this->assertEquals('
stuff-value--stuff
', $this->_sut->toString());
    }
    
    public function testGetHtmlNothingSet()
    {
        $this->_sut->setFile(dirname(__FILE__) . '/faketemplate.txt');
        $this->assertEquals('', $this->_sut->toString());
    }
    
    /**
     * @expectedException Exception
     */    
    public function testSetFileNoSuchFile()
    {
        $this->_sut->setFile(dirname(__FILE__) . '/nosuchfile.txt');
    }       
    
    /**
     * @expectedException Exception
     */    
    public function testParseNoSuchBlock()
    {
        $this->_sut->setFile(dirname(__FILE__) . '/faketemplate.txt');
        $this->_sut->parse('fakeblock');
    }    
    
    public function testParse()
    {
        $this->_sut->setFile(dirname(__FILE__) . '/faketemplate.txt');
        $this->_sut->parse('title');
    }
    
    public function testSetVariable()
    {
        $this->_sut->setFile(dirname(__FILE__) . '/faketemplate.txt');
        $this->_sut->setVariable('MYVAR', 'value');
    }
    
    /**
     * @expectedException Exception
     */
    public function testParseNoFile()
    {
        $this->_sut->parse('fakeblock');
    }

    /**
     * @expectedException Exception
     */
    public function testSetVariableNoFile()
    {
        $this->_sut->setVariable('name', 'value');
    }
    
    /**
     * @expectedException Exception
     */
    public function testtoStringNoFile()
    {
        $this->_sut->toString();
    }
    
}
?>
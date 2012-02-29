<?php

require_once BASE . '/sys/classes/org/tubepress/impl/template/SimpleTemplate.class.php';

class org_tubepress_impl_template_SimpleTemplateTest extends TubePressUnitTest
{
    private $_sut;
    
    public function setUp()
    {
        $this->_sut = new org_tubepress_impl_template_SimpleTemplate();
        
        ob_start();
    }

    public function tearDown()
    {
        ob_end_clean();
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

    /**
    * @expectedException Exception
    */
    public function testReset()
    {
        $this->_sut->setPath(dirname(__FILE__) . '/fake_template.php');
        $this->_sut->setVariable('world', 'World!');
        $this->assertEquals('Hello World!', $this->_sut->toString());

        $this->_sut->reset();
        $this->_sut->toString();
    }
}
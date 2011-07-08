<?php

require_once BASE . '/sys/classes/org/tubepress/impl/template/SimpleTemplate.class.php';

class org_tubepress_impl_template_SimpleTemplateTest extends TubePressUnitTest
{
    public function setUp()
    {
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
        new org_tubepress_impl_template_SimpleTemplate(dirname(__FILE__) . '/nosuchfile.php');
    }

    /**
     * @expectedException Exception
     */
    public function testMissingVariable()
    {
        $template = new org_tubepress_impl_template_SimpleTemplate(dirname(__FILE__) . '/fake_template.php');
        $template->toString();
    }

    public function testSetVariable()
    {
        $template = new org_tubepress_impl_template_SimpleTemplate(dirname(__FILE__) . '/fake_template.php');
        $template->setVariable('world', 'World!');
        $this->assertEquals('Hello World!', $template->toString());
    }

    /**
    * @expectedException Exception
    */
    public function testReset()
    {
        $template = new org_tubepress_impl_template_SimpleTemplate(dirname(__FILE__) . '/fake_template.php');
        $template->setVariable('world', 'World!');
        $this->assertEquals('Hello World!', $template->toString());

        $template->reset();
        $template->toString();
    }
}
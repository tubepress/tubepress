<?php

require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/impl/template/SimpleTemplateBuilder.class.php';

class org_tubepress_impl_template_SimpleTemplateBuilderTest extends TubePressUnitTest
{
    private $_sut;

    public function setUp()
    {
        $this->_sut = new org_tubepress_impl_template_SimpleTemplateBuilder();
        ob_start();
    }

    public function tearDown()
    {
        ob_end_clean();
    }

    /**
     * @expectedException Exception
     */
    public function testNoSuchFile()
    {
        $this->_sut->getNewTemplateInstance('nosuchfile.php');
    }

    public function testBuild()
    {
        $result = $this->_sut->getNewTemplateInstance(dirname(__FILE__) . '/fake_template.php');

        $this->assertTrue(is_a($result, 'org_tubepress_impl_template_SimpleTemplate'));
    }
}
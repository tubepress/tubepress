<?php

require_once BASE . '/sys/classes/org/tubepress/impl/options/ui/DefaultWidgetBuilder.class.php';

class FakeThingy
{
    public $_arg;

    public function __construct($arg)
    {
        $this->_arg = $arg;
    }
}

class org_tubepress_impl_options_ui_DefaultWidgetBuilderTest extends TubePressUnitTest {

	private $_sut;

	public function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_options_ui_DefaultWidgetBuilder();
	}

	public function testBuild()
	{
        $result = $this->_sut->build('something awesome', 'FakeThingy');

        $this->assertTrue($result instanceof FakeThingy);
        $this->assertEquals('something awesome', $result->_arg);
	}
}


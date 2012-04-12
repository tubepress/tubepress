<?php

abstract class org_tubepress_impl_plugin_filters_AbstractStringMagicFilterTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		parent::setUp();

		$this->_sut = $this->_buildSut();
	}

	function testBooleanVariations()
	{
	    $this->_booleanConversion(true, 'true');
	    $this->_booleanConversion(true, 'TRUE');
	    $this->_booleanConversion(true, ' TRuE  ');
	    $this->_booleanConversion(true, ' 1');

	    $this->_booleanConversion(false, 'false  ');
	    $this->_booleanConversion(false, 'FALSE');
	    $this->_booleanConversion(false, ' faLSe  ');
	    $this->_booleanConversion(false, ' 0');
	}

    function testInt()
    {
        $result = $this->_sut->alter_preValidationOptionSet('name', 5);

        $this->assertTrue($result === 5);
    }

    function testDeepArray()
    {
        $val = array(

            array(

                array('name' => '  some <value> \\\\" ')
            )
        );

        $expected = array(

                array(

                        array('name' => 'some &lt;value&gt; "')
                )
        );

        $result = $this->_sut->alter_preValidationOptionSet('otherName', $val);

        $this->assertArrayEquality($expected, $result, var_export($result, true));
    }

    protected abstract function _buildSut();

    private function _booleanConversion($expected, $val)
    {
        $result = $this->_sut->alter_preValidationOptionSet('name', $val);

        return $this->assertTrue($result === $expected);
    }
}
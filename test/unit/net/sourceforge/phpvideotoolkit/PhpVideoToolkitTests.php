<?php
require_once '/Applications/MAMP/bin/php5/lib/php/PHPUnit/Framework.php';
require_once 'PhpVideoToolkitTest.php';

class PhpVideoToolkitTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite("PhpVideoToolkit Tests");
		$suite->addTestSuite('net_sourceforge_phpvideotoolkit_PhpVideoToolkitTest');
		return $suite;
	}
}
?>
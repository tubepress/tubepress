<?php
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/patterns/StrategyManagerImpl.class.php';
require_once dirname(__FILE__) . '/../../../../../../test/unit/TubePressUnitTest.php';

class org_tubepress_impl_patterns_StrategyManagerImplTest extends TubePressUnitTest {

	private $_sut;

	function setUp()
	{
		$this->_sut = new org_tubepress_impl_patterns_StrategyManagerImpl();
	}
}
?>

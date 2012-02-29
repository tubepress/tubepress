<?php

require_once dirname(__FILE__) . '/../../../includes/TubePressUnitTest.php';

require_once 'cor/ChainGangTest.php';

class org_tubepress_impl_patterns_PatternsTestSuite
{
	public static function suite()
	{
		return new TubePressUnitTestSuite(array(
            'org_tubepress_impl_patterns_cor_ChainGangTest'
        ));
	}
}


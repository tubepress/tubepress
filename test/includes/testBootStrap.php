<?php

define('BASE', realpath(dirname(__FILE__) . '/../../'));

/** Load up PHPUnit */
#require_once 'PHPUnit/Framework.php';

/** Load up PHPMockery */
require_once 'Mockery/Loader.php';
require_once 'Hamcrest/hamcrest.php';
$loader = new \Mockery\Loader;
$loader->register();

/** Load up the mock function stuff */
require_once 'phpunit/MockFunction.php';

/** Load up the class loader */
class_exists('org_tubepress_impl_classloader_ClassLoader') || require BASE . '/sys/classes/org/tubepress/impl/classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClass('org_tubepress_impl_ioc_IocContainer');

/** Load up the test base classes */
require_once 'TubePressUnitTestSuite.php';
require_once 'TubePressUnitTest.php';

/** Set the tubepress_base_url */
global $tubepress_base_url;
$tubepress_base_url = '<tubepress_base_url>';

/** Load up the ReturnMapping stuff */
require_once 'phpunit/ReturnMapping/ReturnMapping.php';
require_once 'phpunit/ReturnMapping/ReturnMapping/Builder.php';
require_once 'phpunit/ReturnMapping/ReturnMapping/Entry.php';
require_once 'phpunit/ReturnMapping/ReturnMapping/EntryBuilder.php';

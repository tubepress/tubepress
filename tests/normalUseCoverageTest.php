<?php

define("PHPCOVERAGE_HOME", "/Applications/s/spikecoverage/src"); 
require_once PHPCOVERAGE_HOME . "/CoverageRecorder.php";
require_once PHPCOVERAGE_HOME . "/reporter/HtmlCoverageReporter.php";
$reporter = new HtmlCoverageReporter("Code Coverage Report", "", "/Users/ehough/Desktop/realCoverage");

$includePaths = array(dirname(__FILE__) . "/../tubepress.php",
    dirname(__FILE__) . "/../common",
    dirname(__FILE__) . "/../common/class",
    dirname(__FILE__) . "/../common/class/options"
);
$excludePaths = array("");
$cov = new CoverageRecorder($includePaths, $excludePaths, $reporter);

$cov->startInstrumentation();

require_once(dirname(__FILE__) . "/../tubepress.php");

$tubepress_base_url = "/Applications/MAMP/htdocs/wp/wp-content/plugins/tubepress";

$options = new TubePressOptionsPackage();
$options->setValue(TP_OPT_MODE, TP_MODE_POPULAR);
$options->setValue(TP_OPT_POPVAL, "day");
//TubePressDebug::debug($options);
TubePressGallery::generate($options); 

$cov->stopInstrumentation();
$cov->generateReport();
?>


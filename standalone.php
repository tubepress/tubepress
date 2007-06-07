<?php
require_once("tubepress.php");

$tubepress_base_url = "http://localhost/wp/wp-content/plugins/tubepress";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        	<?php tp_insertCSSJS(); ?>
                <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
                <title>TubePress Standalone</title>        
        </head>
        <body>
<?php


$options = new TubePressOptionsPackage();
$options->setValue(TP_OPT_MODE, TP_MODE_POPULAR);
$options->setValue(TP_OPT_POPVAL, "day");
//TubePressDebug::debug($options);
echo TubePressGallery::generate($options); 

?>
                
        </body>
</html>

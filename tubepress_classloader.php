<?php
spl_autoload_register("tubepress_classloader");

function tubepress_classloader($className) {
	
    $folder = tp_classFolder($className);
    
    if ($folder !== false) {
        include_once($folder . $className . ".class.php");
    } else {
        if (!class_exists($className, false)) {
            echo $className . " class not found <br />";
        }
    }
}
    
function tp_classFolder($className, $sub = DIRECTORY_SEPARATOR) {
    
    $currentDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . "..";

    $dir = dir($currentDir . $sub);

    if (file_exists($currentDir.$sub.$className.".class.php")) {
        return $currentDir.$sub;
    }
    
    while (false !== ($folder = $dir->read())) {
            
        if (strpos($folder, ".") === 0) {
            continue;
        }
            
        if (is_dir($currentDir.$sub.$folder)) {
            $subFolder = tp_classFolder($className, $sub.$folder.DIRECTORY_SEPARATOR);
                    
            if ($subFolder) {
                return $subFolder;
            }
        }     
    }
    $dir->close();
    return false;
}
?>
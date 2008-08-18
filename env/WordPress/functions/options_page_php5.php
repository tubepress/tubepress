<?php
/**
 * This is where the fun stuff happens
 */
function __tp_executeOptionsPage()
{
	try {
	    /* initialize the database if we need to */
	    $wpsm = new WordPressStorageManager();
	    $wpsm->init();
	        
	    /* are we updating? */
	    if (isset($_POST['tubepress_save'])) {
	        try {
	            TubePressOptionsForm::collect($wpsm, $_POST);
	            echo '<div id="message" class="updated fade"><p><strong>Options updated</strong></p></div>';
	        } catch (Exception $error) {
	            echo '<div id="message" class="error fade"><p><strong>' . 
	                $error->getMessage() . '</strong></p></div>';
	        }
	    }
	    
	    TubePressOptionsForm::display($wpsm);
	} catch (Exception $e) {
		print $e->getMessage();
	}
}
?>

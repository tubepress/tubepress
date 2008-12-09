<?php
/**
 * This is where the fun stuff happens
 */
function __tp_executeOptionsPage()
{
	try {
		$msgService = new WordPressMessageService();
		$validationService = new SimpleTubePressInputValidationService();
		$validationService->setMessageService($msgService);
		
	    /* initialize the database if we need to */
	    $wpsm = new WordPressStorageManager();
	    $wpsm->setValidationService($validationService);
	    $wpsm->init();
	    
	    $optionsForm = new TubePressOptionsForm();
	    $optionsForm->setMessageService($msgService);
	        
	    /* are we updating? */
	    if (isset($_POST['tubepress_save'])) {
	        try {
	            $optionsForm->collect($wpsm, $_POST);
	            echo '<div id="message" class="updated fade"><p><strong>Options updated</strong></p></div>';
	        } catch (Exception $error) {
	            echo '<div id="message" class="error fade"><p><strong>' . 
	                $error->getMessage() . '</strong></p></div>';
	        }
	    }
	    
	    $optionsForm->display($wpsm);
	} catch (Exception $e) {
		print $e->getMessage();
	}
}
?>

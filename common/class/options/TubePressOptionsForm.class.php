<?php
class TubePressOptionsForm {
    
    public final function display(TubePressStorage_v157 $stored) {

        /* load up the template */
        $tpl = new HTML_Template_IT(dirname(__FILE__) . "/../../ui");
        if (!$tpl->loadTemplatefile("options_page.tpl.html", true, true)) {
            throw new Exception("Could not load options template");
        }
        
        $tpl->setVariable("PAGETITLE", "TubePress Options");
        $tpl->setVariable("INTROTEXT", "This is some intro text");
        $tpl->setVariable("SAVE", "Save");
        
        /* go through each category */
        foreach ($stored->getOptionPackages() as $optionCategory) {
            $optionCategory->printForOptionsForm($tpl);
        }
  
        $tpl->parse("options");
        print $tpl->get();
    }
    
    public final function collect(array $postVars) {
        
    }
    
}
?>
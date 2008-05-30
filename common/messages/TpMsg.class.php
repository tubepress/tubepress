<?php
class TpMsg {
    
    /**
     * Retrieves a message for TubePress
     *
     * @param string $msgId The message ID
     * 
     * @return string The corresponding message, or "" if not found
     */
    static function _($msgId)
    {        
        $translation = __($msgId, "tubepress");
        
        return $translation == $msgId ? "" : $translation;
    }
}

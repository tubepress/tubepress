<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
?>
<input id="<?php echo $id; ?>" type="text" name="<?php echo $id; ?>" size="6" class="color" value="<?php echo $value; ?>" />

<script type="text/javascript">
    jQuery(function() {

        jQuery('#<?php echo $id; ?>').spectrum({

            showInitial : true,
            preferredFormat : "<?php echo $preferredFormat; ?>",
            showAlpha : <?php echo $showAlpha ? 'true' : 'false'; ?>,
            showInput : <?php echo $showInput ? 'true' : 'false'; ?>,
            showPalette : <?php echo $showPalette ? 'true' : 'false'; ?>,
            showSelectionPalette : <?php echo $showPalette ? 'true' : 'false'; ?>,
            cancelText : "<?php echo $cancelText; ?>",
            chooseText : "<?php echo $chooseText; ?>",
            localStorageKey : "tubepress.spectrum"
        });
    });
</script>
<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
?>
<input id="<?php echo $id; ?>" type="text" name="<?php echo $id; ?>" size="6" class="tubepress-spectrum-field" value="<?php echo $value; ?>"
    data-preferredformat="<?php echo $preferredFormat; ?>"
    data-showalpha="<?php echo $showAlpha ? 'true' : 'false'; ?>"
    data-showinput="<?php echo $showInput ? 'true' : 'false'; ?>"
    data-showpalette="<?php echo $showPalette ? 'true' : 'false'; ?>"
    data-canceltext="<?php echo htmlspecialchars($cancelText); ?>"
    data-choosetext="<?php echo htmlspecialchars($chooseText); ?>"
    />
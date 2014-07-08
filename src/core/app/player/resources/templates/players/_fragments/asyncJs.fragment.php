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

<?php
/**
 * The following JS snippet asynchronously loads the required script for this player to work correctly.
 * Putting it here allows themes to customize the actual script that's loaded. For instance, you could
 * do something like this:
 *
 * tubePressDomInjector.push(['loadJs', 'http://some.thing/foo/bar.js']);
 */
?>
<script type="text/javascript">
    var tubePressDomInjector = tubePressDomInjector || [];
    tubePressDomInjector.push(['loadJs', '<?php echo $jsPath; ?>']);
</script>
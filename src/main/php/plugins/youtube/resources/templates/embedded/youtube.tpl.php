<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
?>
<iframe id="tubepress-youtube-player-<?php echo ${tubepress_api_const_template_Variable::VIDEO_ID}; ?>" class="youtube-player" type="text/html" width="<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_WIDTH}; ?>" height="<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_HEIGHT}; ?>" src="<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_DATA_URL}; ?>" frameborder="0"></iframe>
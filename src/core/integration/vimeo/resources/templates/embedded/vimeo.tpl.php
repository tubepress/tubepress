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
<iframe src="<?php echo htmlspecialchars(${tubepress_app_embedded_api_Constants::TEMPLATE_VAR_DATA_URL}, ENT_QUOTES, 'UTF-8'); ?>" width="<?php echo ${tubepress_app_embedded_api_Constants::TEMPLATE_VAR_WIDTH}; ?>" height="<?php echo ${tubepress_app_embedded_api_Constants::TEMPLATE_VAR_HEIGHT}; ?>" frameborder="0" allowfullscreen></iframe>
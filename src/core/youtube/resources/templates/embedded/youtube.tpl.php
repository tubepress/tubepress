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
<iframe id="<?php echo ${tubepress_core_embedded_api_Constants::TEMPLATE_VAR_VIDEO_DOM_ID}; ?>" data-videoid="<?php echo ${tubepress_core_embedded_api_Constants::TEMPLATE_VAR_VIDEO_ID}; ?>" data-playerimplementation="<?php echo ${tubepress_core_embedded_api_Constants::TEMPLATE_VAR_IMPL_NAME}; ?>" data-videoprovidername="<?php echo ${tubepress_core_embedded_api_Constants::TEMPLATE_VAR_MEDIA_PROVIDER_NAME}; ?>" class="youtube-player" type="text/html" width="<?php echo ${tubepress_core_embedded_api_Constants::TEMPLATE_VAR_WIDTH}; ?>" height="<?php echo ${tubepress_core_embedded_api_Constants::TEMPLATE_VAR_HEIGHT}; ?>" src="<?php echo ${tubepress_core_embedded_api_Constants::TEMPLATE_VAR_DATA_URL}; ?>" frameborder="0" allowfullscreen></iframe>
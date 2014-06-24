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

/**
 * The following <dt> prints out each attribute label (e.g. Ratings, Views, Comment Count, etc). You may add to, but not remove,
 * the existing class names for each of the elements.
 */
?>
<dt class="tubepress_meta tubepress_meta_<?php echo $attributeName; ?>"><?php
    if (isset(${tubepress_core_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTE_LABELS}[$attributeName])):
        echo ${tubepress_core_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTE_LABELS}[$attributeName];
    endif;
?></dt>

<?php
/**
 * The following <dd> prints out each attribute value. You may add to, but not remove,
 * the existing class names for each of the elements.
 */
?>
<dd class="tubepress_meta tubepress_meta_<?php echo $attributeName; ?>"><?php

    /**
     * Some media attributes contain like to add links to their attribute values.
     */
    echo $mediaItem->getAttribute("$attributeName.preHtml");

    /**
     * Print out the attribute value.
     */
    echo htmlspecialchars($mediaItem->getAttribute($attributeName), ENT_QUOTES, 'UTF-8');

    /**
     * Close any open tags, if necessary.
     */
    echo $mediaItem->getAttribute("$attributeName.postHtml");

?></dd><?php //end of dd.tubepress_meta ?>
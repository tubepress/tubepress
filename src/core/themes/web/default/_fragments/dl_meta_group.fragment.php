

<?php
/**
 * Loop over the attributes for this media item.
 */
?>
<?php foreach (${tubepress_core_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW} as $attributeName): ?>

<?php
/**
 * The following <dt> prints out each attribute label (e.g. Ratings, Views, Comment Count, etc). You may add to, but not remove,
 * the existing class names for each of the elements.
 */
?>
<dt class="tubepress_meta tubepress_meta_<?php echo $attributeName; ?>">
    <?php if (isset(${tubepress_core_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTE_LABELS}[$attributeName])): ?>
        <?php echo ${tubepress_core_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTE_LABELS}[$attributeName]; ?>
    <?php endif; ?>
</dt>

<?php
/**
 * The following <dd> prints out each attribute value. You may add to, but not remove,
 * the existing class names for each of the elements.
 */
?>
<dd class="tubepress_meta tubepress_meta_<?php echo $attributeName; ?>">

    <?php
    /**
     * Some media attributes contain like to add links to their attribute values.
     */
    ?>
    <?php echo $mediaItem->getAttribute("$attributeName.preHtml"); ?>

    <?php
    /**
     * Print out the attribute value.
     */
    ?>
    <?php echo htmlspecialchars($mediaItem->getAttribute($attributeName), ENT_QUOTES, 'UTF-8'); ?>

    <?php
    /**
     * Close any open tags, if necessary.
     */
    ?>
    <?php echo $mediaItem->getAttribute("$attributeName.postHtml"); ?>
</dd><?php //end of dd.tubepress_meta ?>

<?php endforeach; ?>

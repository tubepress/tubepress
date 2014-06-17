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
 * Most of this logic is loosely based on
 * https://github.com/KnpLabs/KnpPaginatorBundle/blob/master/Pagination/SlidingPagination.php
 *
 * If you simply wish to modify the HTML, skip down to around line 109.
 */
$current      = ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_CURRENT_PAGE};
$totalItems   = ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_TOTAL_ITEMS};
$itemsPerPage = ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_RESULTS_PER_PAGE};
$pageCount    = intval(ceil($totalItems / $itemsPerPage));
$dots         = '<li class="disabled"><a href="#">...</a></li>';
$pageRange    = 5;

if ($pageCount < $current) {

    $current = $pageCount;
}

if ($pageRange > $pageCount) {

    $pageRange = $pageCount;
}

$delta = ceil($pageRange / 2);

if ($current - $delta > $pageCount - $pageRange) {

    $pages = range($pageCount - $pageRange + 1, $pageCount);

} else {

    if ($current - $delta < 0) {

        $delta = $current;
    }

    $offset = $current - $delta;
    $pages  = range($offset + 1, $offset + $pageRange);
}

$proximity  = floor($pageRange / 2);
$startPage  = $current - $proximity;
$endPage    = $current + $proximity;

if ($startPage < 1) {

    $endPage   = min($endPage + (1 - $startPage), $pageCount);
    $startPage = 1;
}

if ($endPage > $pageCount) {

    $startPage = max($startPage - ($endPage - $pageCount), 1);
    $endPage   = $pageCount;
}

if ($current - 1 > 0) {

    $previousPage = $current - 1;
}

if ($current + 1 <= $pageCount) {

    $nextPage = $current + 1;
}

$firstPageInRange = min($pages);
$lastPageInRange  = max($pages);
$currentItemCount = count($pages);
$firstItemNumber  = (($current - 1) * $itemsPerPage) + 1;
$lastItemNumber   = $firstItemNumber + $currentItemCount - 1;

if (!function_exists('___a')) {

    function ___a($page, $format, $innerText = null)
    {
        if (!$innerText) {

            $innerText = $page;
        }
        $href = sprintf($format, $page);

        if ($page > 1) {

            $noFollow = ' rel="nofollow"';

        } else {

            $noFollow = '';
        }
        echo <<<ABC
<a href="$href" data-page="$page"$noFollow>$innerText</a>
ABC;

    }
}

if ($pageCount > 1) : ?>
    <div class="tubepress_pagination tubepress_pagination-centered">
        <ul>

        <?php if (isset($previousPage)) : ?>
            <li>
                <?php ___a($previousPage, ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_HREF_FORMAT}, '&laquo;&nbsp;' . ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_TEXT_PREV}); ?>
            </li>
        <?php else: ?>
            <li class="disabled">
                <span>&laquo;&nbsp;<?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_TEXT_PREV}; ?></span>
            </li>
        <?php endif; ?>

        <?php if ($startPage > 1) : ?>

            <li>
                <?php ___a(1, ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_HREF_FORMAT}); ?>
            </li>

            <?php if ($startPage == 3) : ?>

                <li>
                    <?php ___a(2, ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_HREF_FORMAT}); ?>
                </li>

            <?php elseif ($startPage != 2): ?>

                <li class="disabled">
                    <span>&hellip;</span>
                </li>

            <?php endif; ?>
        <?php endif; ?>
        <?php foreach ($pages as $page) : ?>
            <?php if ($page != $current) : ?>
                <li>
                    <?php ___a($page, ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_HREF_FORMAT}); ?>
                </li>
            <?php else: ?>
                <li class="active">
                    <span><?php echo $page; ?></span>
                </li>
            <?php endif ?>
        <?php endforeach; ?>

        <?php if ($pageCount > $endPage) : ?>
            <?php if ($pageCount > ($endPage + 1)) : ?>
                <?php if ($pageCount > ($endPage + 2)) : ?>
                    <li class="disabled">
                        <span>&hellip;</span>
                    </li>
                <?php else: ?>
                    <li>
                        <?php ___a(($pageCount - 1), ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_HREF_FORMAT}); ?>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
            <li>
                <?php ___a($pageCount, ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_HREF_FORMAT}); ?>
            </li>
        <?php endif; ?>
        <?php if (isset($nextPage)) : ?>
            <li>
                <?php ___a($nextPage, ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_HREF_FORMAT}, ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_TEXT_NEXT} . '&nbsp;&raquo;'); ?>
            </li>
        <?php else: ?>
            <li class="disabled">
                <span><?php echo ${tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_PAGINATION_TEXT_NEXT}; ?>&nbsp;&raquo;</span>
            </li>
        <?php endif; ?>
        </ul>
    </div>
<?php endif; ?>
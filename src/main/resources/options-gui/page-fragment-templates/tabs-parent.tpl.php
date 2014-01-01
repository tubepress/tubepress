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

<div class="row">
    <div class="col-md-12">
        <div class="tabbable">

            <ul class="nav nav-tabs">

                <?php
                $tabIndex = 0;

                /**
                 * @var $categories tubepress_spi_options_ui_OptionsPageItemInterface[]
                 */
                foreach ($categories as $category): ?>

                    <li<?php if ($tabIndex++ === 0): ?> class="active"<?php endif; ?>>

                        <a href="#<?php echo $category->getId(); ?>" data-toggle="tab">

                            <?php echo $category->getTranslatedDisplayName(); ?>

                        </a>

                    </li>

                <?php endforeach; ?>
            </ul>

            <div class="tab-content">

                <?php
                $categoryIndex = 0;

                foreach ($categories as $category) {

                    require 'single-category.tpl.php';
                }
                ?>

            </div>
        </div>
    </div>
</div>



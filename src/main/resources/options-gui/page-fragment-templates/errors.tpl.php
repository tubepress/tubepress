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

<script type="text/javascript">
    var TubePressErrors = [

            <?php
                $errorIndex = 1;

                foreach ($errors as $fieldId => $errorMessage) {

                    echo '[\'' . $fieldId . '\' , \'' . htmlspecialchars($errorMessage) . '\']';

                    if ($errorIndex++ < count($errors)) {

                        echo ',';
                    }
                }
            ?>
        ];
</script>

<?php if (count($errors) > 0): ?>

    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger">
                <button type="button" class="tp-bs close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <ul>
                    <?php foreach ($errors as $fieldId => $message): ?>
                    <li><?php echo htmlspecialchars($message); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

<?php endif; ?>
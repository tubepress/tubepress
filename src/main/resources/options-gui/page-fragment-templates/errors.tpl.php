<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
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

    <div class="container">
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?php echo $correctErrorsMessage; ?>
        </div>
    </div>

<?php endif; ?>
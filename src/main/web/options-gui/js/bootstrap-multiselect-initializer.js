/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
var TubePressBootstrapMultiselectInitializer = (function () {

    'use strict';

    var init = function () {

        jQuery('.tubepress-bootstrap-multiselect-field').multiselect({

            buttonClass : 'btn btn-default btn-sm',
            dropRight   : true,
            buttonText  : jQuery(this).data('selectText')
        });
    };

    return {

        init : init
    };

}());

jQuery(function () {

    'use strict';

    TubePressBootstrapMultiselectInitializer.init();
});
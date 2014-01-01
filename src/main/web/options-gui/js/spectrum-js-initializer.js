/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
(function (jquery) {

    'use strict';

    var getDataAttr = function (element, name, fallback) {

            var elem = jquery(element),
                val = elem.data(name);

            return val === undefined ? fallback : val;
        },

        initSpectrum = function () {

            var preferredFormat = getDataAttr(this, 'preferredformat', 'hex'),
                showAlpha       = Boolean(getDataAttr(this, 'showalpha', false)),
                showPalette     = Boolean(getDataAttr(this, 'showpalette', true)),
                showInput       = Boolean(getDataAttr(this, 'showinput', true)),
                cancelText      = getDataAttr(this, 'canceltext', 'cancel'),
                chooseText      = getDataAttr(this, 'choosetext', 'Choose'),
                spectrumOptions = {

                    showInitial          : true,
                    preferredFormat      : preferredFormat,
                    showAlpha            : showAlpha,
                    showInput            : showInput,
                    showPalette          : showPalette,
                    showSelectionPalette : showPalette,
                    cancelText           : cancelText,
                    chooseText           : chooseText,
                    localStorageKey      : "tubepress.spectrum"
                };

            jquery(this).spectrum(spectrumOptions);
        },

        init = function () {

            jquery('.tubepress-spectrum-field').each(initSpectrum);
        };

    jquery(init);

}(jQuery));
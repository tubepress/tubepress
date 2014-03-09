/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
(function (jquery, win, doc) {

    'use strict';

    var themeData,
        fieldNames = [

            'description', 'author', 'licenses', 'version',
            'demo', 'keywords', 'homepage', 'docs', 'download', 'bugs'
        ],
        fieldMap = {},
        selectField,
        screenshotsDiv,

        setVisibility = function (field, visible) {

            var display = 'none';

            if (visible) {

                display = 'inherit';
            }

            return field.css('display', display);
        },

        showField = function (field) {

            setVisibility(field.prev('dt'), true);
            setVisibility(field, true);
        },

        hideField = function (field) {

            setVisibility(field.prev('dt'), false);
            setVisibility(field, false);
        },

        makeUrl = function (href, text) {

            return '<a href="' + href + '" target="_blank">' + text + '</a>';
        },

        getFieldHtml = function (activeThemeData, fieldName) {

            var data = activeThemeData[fieldName],
                toReturn = '',
                i;

            switch (fieldName) {

            case 'demo':
            case 'homepage':
            case 'docs':
            case 'download':
            case 'bugs':

                return makeUrl(data, data);

            case 'licenses':

                for (i in data) {

                    if (data.hasOwnProperty(i)) {

                        toReturn += makeUrl(data[i].url, data[i].type);

                        if (i !== (data.length - 1)) {

                            toReturn += '<br />';
                        }
                    }
                }

                return toReturn;

            case 'author':

                if (data.hasOwnProperty('url')) {

                    return makeUrl(data.url, data.name);
                }

                return data.name;

            default:
                return data;
            }
        },

        getScreenshotDivs = function (screenshots) {

            var i, columnDiv, anchor, img, toReturn = [];

            for (i in screenshots) {

                if (screenshots.hasOwnProperty(i)) {

                    columnDiv = jquery(doc.createElement('div')).addClass('col-xs-12 col-lg-6');
                    anchor    = jquery(doc.createElement('a')).attr('href', screenshots[i]).addClass('thumbnail').attr('data-gallery', '');
                    img       = jquery(doc.createElement('img')).attr('src', screenshots[i]).addClass('img-responsive').attr('alt', 'theme screenshot');

                    anchor.append(img);
                    columnDiv.append(anchor);

                    toReturn.push(columnDiv);
                }
            }

            return toReturn;
        },

        showScreenshots = function (screenshots) {

            var firstDiv = screenshotsDiv.children('div:first');

            firstDiv.children('div').remove();
            firstDiv.append(getScreenshotDivs(screenshots));

            screenshotsDiv.children('p:first').css('display', 'none');
            firstDiv.css('display', 'none');
            firstDiv.fadeIn(300);
        },

        hideScreenshots = function () {

            screenshotsDiv.children('div:first').css('display', 'none');
            screenshotsDiv.children('p:first').css('display', 'inherit');
        },

        loadTheme = function (themeName) {

            var activeThemeData = themeData[themeName],
                i,
                fieldName;

            for (i in fieldNames) {

                if (fieldNames.hasOwnProperty(i)) {

                    fieldName = fieldNames[i];

                    if (activeThemeData[fieldName] && (!jquery.isArray(activeThemeData[fieldName]) || activeThemeData[fieldName].length > 0)) {

                        fieldMap[fieldName].html(getFieldHtml(activeThemeData, fieldName));
                        showField(fieldMap[fieldName]);

                    } else {

                        if (fieldName === 'description') {

                            fieldMap[fieldName].html('&nbsp;');

                        } else {

                            hideField(fieldMap[fieldName]);
                        }
                    }
                }
            }

            if (activeThemeData.screenshots && activeThemeData.screenshots.length > 0) {

                showScreenshots(activeThemeData.screenshots);

            } else {

                hideScreenshots();
            }
        },

        switchTheme = function () {

            var current = selectField.find(':selected').val();

            loadTheme(current);
        },

        init = function () {

            themeData      = win.TubePressThemes;
            screenshotsDiv = jquery('#theme-screenshots');

            var i, fieldName, blueImpGallery = jquery('#blueimp-gallery');

            for (i in fieldNames) {

                if (fieldNames.hasOwnProperty(i)) {

                    fieldName = fieldNames[i];

                    fieldMap[fieldName] = jquery('#theme-field-' + fieldName);
                }
            }

            selectField = jquery('select#theme');

            selectField.change(switchTheme);

            switchTheme();

            blueImpGallery.data('useBootstrapModal', false);
            blueImpGallery.toggleClass('blueimp-gallery-controls', true);
        };

    jquery(init);

}(jQuery, window, document));
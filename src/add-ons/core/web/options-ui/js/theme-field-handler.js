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
        emptyScreenshotsP,
        clickToEnlarge,

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

            var thumbUrl, columnDiv, anchor, img, toReturn = [];

            for (thumbUrl in screenshots) {

                if (screenshots.hasOwnProperty(thumbUrl)) {

                    columnDiv = jquery(doc.createElement('div')).addClass('col-xs-6 col-sm-12 col-md-4').css('display', 'none');
                    anchor    = jquery(doc.createElement('a')).attr('href', screenshots[thumbUrl]).addClass('thumbnail').attr('data-gallery', '');
                    img       = jquery(doc.createElement('img')).attr('src', thumbUrl).addClass('img-responsive').attr('alt', 'theme screenshot');

                    anchor.append(img);
                    columnDiv.append(anchor);

                    toReturn.push(columnDiv);
                }
            }

            return toReturn;
        },

        showScreenshots = function (screenshots) {

            var thumbnails;

            screenshotsDiv.children('div').remove();
            screenshotsDiv.append(getScreenshotDivs(screenshots));
            emptyScreenshotsP.css('display', 'none');

            thumbnails = screenshotsDiv.children('div');
            thumbnails.fadeIn(300);
            clickToEnlarge.fadeIn(300);
        },

        hideScreenshots = function () {

            screenshotsDiv.children('div').remove();
            emptyScreenshotsP.css('display', 'inherit');
            clickToEnlarge.css('display', 'none');
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

            if (activeThemeData.hasOwnProperty('screenshots') && jquery.isPlainObject(activeThemeData.screenshots)) {

                showScreenshots(activeThemeData.screenshots);

            } else {

                hideScreenshots();
            }
        },

        switchTheme = function () {

            var current = selectField.find(':selected').val();

            loadTheme(current);
        },

        onTabSwitch = function (e) {

            var newTab = e.target;

            if (newTab.hash === '#theme_category') {

                switchTheme();
            }
        },

        init = function () {

            themeData         = win.TubePressThemes;
            screenshotsDiv    = jquery('#theme-screenshots div.panel-body:first');
            emptyScreenshotsP = screenshotsDiv.children('p:first');
            clickToEnlarge    = jquery('#click-to-englarge-screenshots');

            var i, fieldName, blueImpGallery = jquery('#blueimp-gallery');

            for (i in fieldNames) {

                if (fieldNames.hasOwnProperty(i)) {

                    fieldName = fieldNames[i];

                    fieldMap[fieldName] = jquery('#theme-field-' + fieldName);
                }
            }

            selectField = jquery('select#theme');

            selectField.change(switchTheme);

            jquery('a[data-toggle="tab"]').on('show.bs.tab', onTabSwitch);

            blueImpGallery.data('useBootstrapModal', false);
            blueImpGallery.toggleClass('blueimp-gallery-controls', true);
        };

    jquery(init);

}(jQuery, window, document));
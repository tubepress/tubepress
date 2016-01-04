/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
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
        simpleFieldNames = [

            'authors', 'description', 'license', 'screenshots', 'version'
        ],
        urlFieldNames = [
            'demo', 'homepage', 'docs', 'download', 'bugs', 'forum', 'sourceCode'
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
                i,
                author,
                license,
                licenseUrls;

            if (jquery.inArray(fieldName, urlFieldNames) !== -1) {

                return makeUrl(data, data);
            }

            switch (fieldName) {

            case 'authors':

                for (i in data) {

                    if (data.hasOwnProperty(i)) {

                        author = data[i];

                        if (author.hasOwnProperty('url')) {

                            toReturn += makeUrl(author.url, author.name);

                        } else {

                            toReturn += author.name;
                        }

                        if (parseInt(i, 10) !== (data.length - 1) && data.length > (parseInt(i, 10) + 1)) {

                            toReturn += '<br />';
                        }
                    }
                }

                return toReturn;

            case 'license':

                license  = data;
                toReturn = license.type;

                if (!license.hasOwnProperty('urls')) {

                    return toReturn;
                }

                if (!jquery.isArray(license.urls)) {

                    return toReturn;
                }

                licenseUrls = license.urls;

                for (i in licenseUrls) {

                    if (licenseUrls.hasOwnProperty(i)) {

                        if (parseInt(i, 10) === 0) {

                            toReturn += ' [';
                        }

                        toReturn += makeUrl(licenseUrls[i], (parseInt(i, 10) + 1));

                        if (parseInt(i, 10) !== (licenseUrls.length - 1) && licenseUrls.length > (parseInt(i, 10) + 1)) {

                            toReturn += ', ';
                        }
                    }
                }

                return toReturn + ']';

            default:
                return data;
            }
        },

        getScreenshotDivs = function (screenshots) {

            var i, urlThumb, urlFull, columnDiv, anchor, img, toReturn = [];

            for (i in screenshots) {

                if (screenshots.hasOwnProperty(i)) {

                    urlThumb = screenshots[i][0];
                    urlFull  = screenshots[i][1];

                    columnDiv = jquery(doc.createElement('div')).addClass('col-xs-6 col-sm-12 col-md-4').css('display', 'none');
                    anchor    = jquery(doc.createElement('a')).attr('href', urlFull).addClass('thumbnail').attr('data-gallery', '');
                    img       = jquery(doc.createElement('img')).attr('src', urlThumb).addClass('img-responsive').attr('alt', 'theme screenshot');

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

            var activeThemeData  = themeData[themeName],
                text_support     = 'support',
                text_screenshots = 'screenshots',
                i,
                fieldName;

            for (i in simpleFieldNames) {

                if (simpleFieldNames.hasOwnProperty(i)) {

                    fieldName = simpleFieldNames[i];

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

            for (i in urlFieldNames) {

                if (urlFieldNames.hasOwnProperty(i)) {

                    fieldName = urlFieldNames[i];

                    if (activeThemeData[text_support] && activeThemeData[text_support][fieldName] && (!jquery.isArray(activeThemeData[text_support][fieldName]) || activeThemeData[text_support][fieldName].length > 0)) {

                        fieldMap[fieldName].html(getFieldHtml(activeThemeData[text_support], fieldName));
                        showField(fieldMap[fieldName]);

                    } else {

                        hideField(fieldMap[fieldName]);
                    }
                }
            }

            if (activeThemeData.hasOwnProperty(text_screenshots) && jquery.isArray(activeThemeData[text_screenshots])) {

                if (activeThemeData[text_screenshots].length > 0) {

                    showScreenshots(activeThemeData[text_screenshots]);

                } else {

                    hideScreenshots();
                }
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

            for (i in simpleFieldNames) {

                if (simpleFieldNames.hasOwnProperty(i)) {

                    fieldName = simpleFieldNames[i];

                    fieldMap[fieldName] = jquery('#theme-field-' + fieldName);
                }
            }

            for (i in urlFieldNames) {

                if (urlFieldNames.hasOwnProperty(i)) {

                    fieldName = urlFieldNames[i];

                    fieldMap[fieldName] = jquery('#theme-field-support-' + fieldName);
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
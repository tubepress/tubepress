<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_test_translations_CoreTranslationsTest extends tubepress_api_test_translation_AbstractTranslationsTest
{
    protected function getPoFilePaths()
    {
        $finder = new \Symfony\Component\Finder\Finder();
        $finder->files()
            ->in(TUBEPRESS_ROOT . '/src/translations/en')
            ->name('*.po')
            ->notPath('add-ons');

        return iterator_to_array($finder);
    }

    protected function getSearchPaths()
    {
        return array(
            TUBEPRESS_ROOT . '/src',
        );
    }
}
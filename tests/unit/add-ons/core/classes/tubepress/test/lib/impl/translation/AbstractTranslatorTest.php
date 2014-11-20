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
 * @covers tubepress_lib_impl_translation_AbstractTranslator
 */
class tubepress_test_lib_impl_translation_AbstractTranslatorTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_test_lib_impl_translation_AbstractTranslatorTest__translator
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_test_lib_impl_translation_AbstractTranslatorTest__translator();
    }

    /**
     * @dataProvider getPluralData
     */
    public function testTranslateChoice($id, $expected, $number, array $params = array())
    {
        $actual = $this->_sut->transChoice($id, $number, $params);

        $this->assertEquals($expected, $actual);
    }

    public function getPluralData()
    {
        return array(

            array('hello!', 'hello!', 1),
            array('hello %s!', 'hello eric!', 1, array('%s' => 'eric')),
            array('hello {{ name }}!', 'hello eric!', 1, array('{{ name }}' => 'eric')),
            array('{0} There are no apples|{1} There is one apple|]1,Inf] There are %count% apples', 'There are no apples', 0),
            array('{0} There are no apples|{1} There is one apple|]1,Inf] There are %count% apples', 'There is one apple', 1),
            array('{0} There are no apples|{1} There is one apple|]1,Inf] There are %count% apples', 'There are 33 apples', 33, array('%count%' => 33)),
            array('There is one apple|There are %count% apples', 'There is one apple', 1),
            array('There is one apple|There are %count% apples', 'There are 2 apples', 2, array('%count%' => 2)),
            array('{0} There are no apples|one: There is one apple|more: There are %count% apples', 'There are no apples', 0),
            array('{0} There are no apples|one: There is one apple|more: There are %count% apples', 'There is one apple', 1),
            array('{0} There are no apples|one: There is one apple|more: There are %count% apples', 'There are 55 apples', 55, array('%count%' => 55)),
        );
    }

    /**
     * @dataProvider getSingularData
     */
    public function testTranslate($id, $expected, array $params = array())
    {
        $actual = $this->_sut->trans($id, $params);

        $this->assertEquals($expected, $actual);
    }

    public function getSingularData()
    {
        return array(

            array('hello!', 'hello!'),
            array('hello %s!', 'hello eric!', array('%s' => 'eric')),
            array('hello {{ name }}!', 'hello eric!', array('{{ name }}' => 'eric')),
        );
    }
}

class tubepress_test_lib_impl_translation_AbstractTranslatorTest__translator extends tubepress_lib_impl_translation_AbstractTranslator
{
    private $_locale = 'en';

    protected function translate($id, $domain = null, $locale = null)
    {
        return $id;
    }

    public function setLocale($locale)
    {
        $this->_locale = $locale;
    }

    public function getLocale()
    {
        return $this->_locale;
    }
}
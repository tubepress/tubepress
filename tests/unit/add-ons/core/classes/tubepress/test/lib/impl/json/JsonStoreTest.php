<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_lib_impl_json_JsonStore<extended>
 */
class tubepress_test_lib_impl_json_JsonStoreTest extends tubepress_test_TubePressUnitTest
{
    private $json;

    /**
     * @var tubepress_lib_impl_json_JsonStore
     */
    private $jsonStore;

    public function onSetup()
    {
        $this->json = '{
            "store": {
                "book": [
                    {
                        "category": "reference",
                        "author": "Nigel Rees",
                        "title": "Sayings of the Century",
                        "price": 8.95
                    },
                    {
                        "category": "fiction",
                        "author": "Evelyn Waugh",
                        "title": "Sword of Honour",
                        "price": 12.99,
                        "code": "01.02"
                    },
                    {
                        "category": "fiction",
                        "author": "Herman Melville",
                        "title": "Moby Dick",
                        "isbn": "0-553-21311-3",
                        "price": 8.99
                    },
                    {
                        "category": "fiction",
                        "author": "J. R. R. Tolkien",
                        "title": "The Lord of the Rings",
                        "isbn": "0-395-19395-8",
                        "price": 22.99
                    }
                ],
                "bicycle": {
                    "color": "red",
                    "price": 19.95
                }
            }
        }';

        $this->jsonStore = new tubepress_lib_impl_json_JsonStore($this->json);
    }

    public function testGetMissingAttribute()
    {
        $actual = $this->jsonStore->get('$..book[0].nope');
        $this->assertEquals(array(), $actual);
    }

    public function testGetFirstBookAuthor()
    {
        $actual = $this->jsonStore->get('$..book[0].author');
        $this->assertEquals(array('Nigel Rees'), $actual);
    }

    public function testSetData()
    {
        $this->assertEquals($this->jsonStore->toArray(), json_decode($this->json, true));
        $new = ['a' => 'b'];
        $this->jsonStore->setData($new);
        $this->assertEquals($this->jsonStore->toArray(), $new);
        $this->assertNotEquals($this->jsonStore->toArray(), json_decode($this->json, true));
    }

    public function testGetAllByKey()
    {
        $data = $this->jsonStore->get("$..book.*.category");
        $expected = ["reference", "fiction", "fiction", "fiction"];
        $this->assertEquals($data, $expected);
        $data = $this->jsonStore->get("$..category");
        $this->assertEquals($data, $expected);
    }

    public function testGetAllByKeyUnique()
    {
        $data = $this->jsonStore->get("$..book.*.category", true);
        $expected = ["reference", "fiction"];
        $this->assertEquals($data, $expected);
        $data = $this->jsonStore->get("$..category", true);
        $this->assertEquals($data, $expected);
    }

    public function onTearDown()
    {
        $this->jsonStore = null;
        $this->json = null;
    }
}
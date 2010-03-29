<?php
/**
 * PHP Reader Library
 *
 * Copyright (c) 2008 The PHP Reader Project Workgroup. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *  - Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *  - Neither the name of the project workgroup nor the names of its
 *    contributors may be used to endorse or promote products derived from this
 *    software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    php-reader
 * @subpackage ID3
 * @copyright  Copyright (c) 2008 The PHP Reader Project Workgroup
 * @license    http://code.google.com/p/php-reader/wiki/License New BSD License
 * @version    $Id: Object.php 75 2008-04-14 23:57:21Z svollbehr $
 */

/**
 * The base class for all ID3v2 objects.
 *
 * @package    php-reader
 * @subpackage ID3
 * @author     Sven Vollbehr <svollbehr@gmail.com>
 * @copyright  Copyright (c) 2008 The PHP Reader Project Workgroup
 * @license    http://code.google.com/p/php-reader/wiki/License New BSD License
 * @version    $Rev: 75 $
 */
abstract class ID3_Object
{
  /**
   * The reader object.
   *
   * @var Reader
   */
  protected $_reader;
  
  /**
   * The options array.
   *
   * @var Array
   */
  protected $_options;
  
  /**
   * Constructs the class with given parameters and reads object related data
   * from the ID3v2 tag.
   *
   * @param Reader $reader The reader object.
   * @param Array $options The options array.
   */
  public function __construct($reader = null, &$options = array())
  {
    $this->_reader = $reader;
    $this->_options = $options;
  }
  
  /**
   * Returns the options array.
   *
   * @return Array
   */
  public function getOptions() { return $this->_options; }
  
  /**
   * Sets the options array. See {@link ID3v2} class for available options.
   *
   * @param Array $options The options array.
   */
  public function setOptions(&$options) { $this->_options = $options; }
  
  /**
   * Magic function so that $obj->value will work.
   *
   * @param string $name The field name.
   * @return mixed
   */
  public function __get($name)
  {
    if (method_exists($this, "get" . ucfirst($name)))
      return call_user_func(array($this, "get" . ucfirst($name)));
    else throw new Reader_Exception("Unknown field: " . $name);
  }
  
  /**
   * Magic function so that assignments with $obj->value will work.
   *
   * @param string $name  The field name.
   * @param string $value The field value.
   * @return mixed
   */
  public function __set($name, $value)
  {
    if (method_exists($this, "set" . ucfirst($name)))
      call_user_func
        (array($this, "set" . ucfirst($name)), $value);
    else throw new Reader_Exception("Unknown field: " . $name);
  }
  
  /**
   * Encodes the given 32-bit integer to 28-bit synchsafe integer, where the
   * most significant bit of each byte is zero, making seven bits out of eight
   * available.
   * 
   * @param integer $val The integer to encode.
   * @return integer
   */
  protected function encodeSynchsafe32($val)
  {
    if (!isset($this->_options["version"]) || $this->_options["version"] >= 4) {
      for ($i = 0, $mask = 0xffffff00; $i < 4; $i++, $mask <<= 8)
        $val = ($val << 1 & $mask) | ($val << 1 & ~$mask) >> 1;
      return $val & 0x7fffffff;
    }
    return $val;
  }

  /**
   * Decodes the given 28-bit synchsafe integer to regular 32-bit integer.
   * 
   * @param integer $val The integer to decode
   * @return integer
   */
  protected function decodeSynchsafe32($val)
  {
    if (!isset($this->_options["version"]) || $this->_options["version"] >= 4)
      for ($i = 0, $mask = 0xff000000; $i < 3; $i++, $mask >>= 8)
        $val = ($val & $mask) >> 1 | ($val & ~$mask);
    return $val;
  }
}

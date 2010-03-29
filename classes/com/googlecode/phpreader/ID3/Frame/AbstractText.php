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
 * @version    $Id: AbstractText.php 75 2008-04-14 23:57:21Z svollbehr $
 */

/**#@+ @ignore */
require_once("ID3/Frame.php");
require_once("ID3/Encoding.php");
/**#@-*/

/**
 * A base class for all the text frames.
 * 
 * @package    php-reader
 * @subpackage ID3
 * @author     Sven Vollbehr <svollbehr@gmail.com>
 * @copyright  Copyright (c) 2008 The PHP Reader Project Workgroup
 * @license    http://code.google.com/p/php-reader/wiki/License New BSD License
 * @version    $Rev: 75 $
 */
abstract class ID3_Frame_AbstractText extends ID3_Frame
  implements ID3_Encoding
{
  /**
   * The text encoding.
   *
   * @var integer
   */
  protected $_encoding = ID3_Encoding::UTF8;
  
  /**
   * The text array.
   *
   * @var string
   */
  protected $_text;
  
  /**
   * Constructs the class with given parameters and parses object related data.
   *
   * @param Reader $reader The reader object.
   * @param Array $options The options array.
   */
  public function __construct($reader = null, &$options = array())
  {
    parent::__construct($reader, $options);
    
    if ($reader === null)
      return;
    
    $this->_encoding = Transform::fromInt8($this->_data[0]);
    $this->_data = substr($this->_data, 1);
    switch ($this->_encoding) {
    case self::UTF16:
      $this->_text =
        preg_split("/\\x00\\x00/", Transform::fromString16($this->_data));
      break;
    case self::UTF16BE:
      $this->_text =
        preg_split("/\\x00\\x00/", Transform::fromString16BE($this->_data));
      break;
    default:
      $this->_text =
        preg_split("/\\x00/", Transform::fromString8($this->_data));
    }
  }
  
  /**
   * Returns the text encoding.
   * 
   * @return integer
   */
  public function getEncoding() { return $this->_encoding; }
  
  /**
   * Sets the text encoding.
   * 
   * @see ID3_Encoding
   * @param integer $encoding The text encoding.
   */
  public function setEncoding($encoding) { $this->_encoding = $encoding; }
  
  /**
   * Returns the first text chunk the frame contains.
   * 
   * @return string
   */
  public function getText() { return $this->_text[0]; }
  
  /**
   * Returns an array of texts the frame contains.
   * 
   * @return Array
   */
  public function getTexts() { return $this->_text; }
  
  /**
   * Sets the text using given encoding.
   * 
   * @param mixed $text The test string or an array of strings.
   * @param integer $encoding The text encoding.
   */
  public function setText($text, $encoding = false)
  {
    $this->_text = is_array($text) ? $text : array($text);
    if ($encoding !== false)
      $this->_encoding = $encoding;
  }
  
  /**
   * Returns the frame raw data.
   *
   * @return string
   */
  public function __toString()
  {
    $data = Transform::toInt8($this->_encoding);
    switch ($this->_encoding) {
    case self::UTF16:
      $data .= Transform::toString16(implode("\0\0", $this->_text));
      break;
    case self::UTF16BE:
      $data .= Transform::toString16BE(implode("\0\0", $this->_text));
      break;
    case self::UTF16LE:
      $data .= Transform::toString16LE(implode("\0\0", $this->_text));
      break;
    default:
      $data .= implode("\0", $this->_text);
    }
    $this->setData($data);
    return parent::__toString();
  }
}

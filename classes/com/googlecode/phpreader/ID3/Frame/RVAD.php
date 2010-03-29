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
 * @version    $Id: RVAD.php 75 2008-04-14 23:57:21Z svollbehr $
 * @deprecated ID3v2.3.0
 */

/**#@+ @ignore */
require_once("ID3/Frame.php");
/**#@-*/

/**
 * The <i>Relative volume adjustment</i> frame is a more subjective function
 * than the previous ones. It allows the user to say how much he wants to
 * increase/decrease the volume on each channel while the file is played. The
 * purpose is to be able to align all files to a reference volume, so that you
 * don't have to change the volume constantly. This frame may also be used to
 * balance adjust the audio.
 *
 * There may only be one RVAD frame in each tag.
 *
 * @package    php-reader
 * @subpackage ID3
 * @author     Sven Vollbehr <svollbehr@gmail.com>
 * @copyright  Copyright (c) 2008 The PHP Reader Project Workgroup
 * @license    http://code.google.com/p/php-reader/wiki/License New BSD License
 * @version    $Rev: 75 $
 * @deprecated ID3v2.3.0
 */
final class ID3_Frame_RVAD extends ID3_Frame
{
  /** @var Array */
  private $_adjustments;
  
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
    
    $flags = Transform::fromInt8($this->_data[0]);
    $descriptionBits = Transform::fromInt8($this->_data[0]); //16
    
    $this->_adjustments["right"] =
      ($flags & 0x20) == 0x20 ?
       Transform::fromUInt16BE(substr($this->_data, 0, 2)) :
       -Transform::fromUInt16BE(substr($this->_data, 0, 2));
    $this->_adjustments["left"] =
      ($flags & 0x10) == 0x10 ?
       Transform::fromUInt16BE(substr($this->_data, 2, 2)) :
       -Transform::fromUInt16BE(substr($this->_data, 2, 2));
    $this->_adjustments["peakRight"] =
      Transform::fromUInt16BE(substr($this->_data, 4, 2));
    $this->_adjustments["peakLeft"] =
      Transform::fromUInt16BE(substr($this->_data, 6, 2));

    if ($this->getSize() <= 8)
      return;
    
    $this->_adjustments["rightBack"] =
      ($flags & 0x8) == 0x8 ?
       Transform::fromUInt16BE(substr($this->_data, 8, 2)) :
       -Transform::fromUInt16BE(substr($this->_data, 8, 2));
    $this->_adjustments["leftBack"] =
      ($flags & 0x4) == 0x4 ?
       Transform::fromUInt16BE(substr($this->_data, 10, 2)) :
       -Transform::fromUInt16BE(substr($this->_data, 10, 2));
    $this->_adjustments["peakRightBack"] =
      Transform::fromUInt16BE(substr($this->_data, 12, 2));
    $this->_adjustments["peakLeftBack"] =
      Transform::fromUInt16BE(substr($this->_data, 14, 2));

    if ($this->getSize() <= 16)
      return;
    
    $this->_adjustments["center"] =
      ($flags & 0x2) == 0x2 ?
       Transform::fromUInt16BE(substr($this->_data, 16, 2)) :
       -Transform::fromUInt16BE(substr($this->_data, 16, 2));
    $this->_adjustments["peakCenter"] =
      Transform::fromUInt16BE(substr($this->_data, 18, 2));
    
    if ($this->getSize() <= 20)
      return;
    
    $this->_adjustments["bass"] =
      ($flags & 0x1) == 0x1 ?
       Transform::fromUInt16BE(substr($this->_data, 20, 2)) :
       -Transform::fromUInt16BE(substr($this->_data, 20, 2));
    $this->_adjustments["peakBass"] =
      Transform::fromUInt16BE(substr($this->_data, 22, 2));
  }

  /**
   * Returns the array containing the volume adjustments. The array must contain
   * the following keys: right, left, peakRight, peakLeft. It may optionally
   * contain the following keys: rightBack, leftBack, peakRightBack,
   * peakLeftBack, center, peakCenter, bass, and peakBass.
   * 
   * @return Array
   */
  public function getAdjustments() { return $this->_adjustments; }
  
  /**
   * Sets the array of volume adjustments. The array must contain the following
   * keys: right, left, peakRight, peakLeft. It may optionally contain the
   * following keys: rightBack, leftBack, peakRightBack, peakLeftBack, center,
   * peakCenter, bass, and peakBass.
   * 
   * @param Array $adjustments The volume adjustments array.
   */
  public function setAdjustments($adjustments)
  {
    $this->_adjustments = $adjustments;
  }
  
  /**
   * Returns the frame raw data.
   *
   * @return string
   */
  public function __toString()
  {
    $flags = 0;
    if ($this->_adjustments["right"] > 0)
      $flags = $flags | 0x20;
    if ($this->_adjustments["left"] > 0)
      $flags = $flags | 0x10;
    $data = Transform::toInt8(16) . 
      Transform::toUInt16BE(abs($this->_adjustments["right"])) .
      Transform::toUInt16BE(abs($this->_adjustments["left"])) .
      Transform::toUInt16BE(abs($this->_adjustments["peakRight"])) .
      Transform::toUInt16BE(abs($this->_adjustments["peakLeft"]));
    
    if (isset($this->_adjustments["rightBack"]) &&
        isset($this->_adjustments["leftBack"]) &&
        isset($this->_adjustments["peakRightBack"]) &&
        isset($this->_adjustments["peakLeftBack"])) {
      if ($this->_adjustments["rightBack"] > 0)
        $flags = $flags | 0x8;
      if ($this->_adjustments["leftBack"] > 0)
        $flags = $flags | 0x4;
      $data .= 
        Transform::toUInt16BE(abs($this->_adjustments["rightBack"])) .
        Transform::toUInt16BE(abs($this->_adjustments["leftBack"])) .
        Transform::toUInt16BE(abs($this->_adjustments["peakRightBack"])) .
        Transform::toUInt16BE(abs($this->_adjustments["peakLeftBack"]));
    }
    
    if (isset($this->_adjustments["center"]) &&
        isset($this->_adjustments["peakCenter"])) {
      if ($this->_adjustments["center"] > 0)
        $flags = $flags | 0x2;
      $data .= 
        Transform::toUInt16BE(abs($this->_adjustments["center"])) .
        Transform::toUInt16BE(abs($this->_adjustments["peakCenter"]));
    }
    
    if (isset($this->_adjustments["bass"]) &&
        isset($this->_adjustments["peakBass"])) {
      if ($this->_adjustments["bass"] > 0)
        $flags = $flags | 0x1;
      $data .= 
        Transform::toUInt16BE(abs($this->_adjustments["bass"])) .
        Transform::toUInt16BE(abs($this->_adjustments["peakBass"]));
    }
    $this->setData(Transform::toInt8($flags) . $data);
    return parent::__toString();
  }
}

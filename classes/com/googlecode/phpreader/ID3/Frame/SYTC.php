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
 * @version    $Id: SYTC.php 75 2008-04-14 23:57:21Z svollbehr $
 */

/**#@+ @ignore */
require_once("ID3/Frame.php");
require_once("ID3/Timing.php");
/**#@-*/

/**
 * For a more accurate description of the tempo of a musical piece, the
 * <i>Synchronised tempo codes</i> frame might be used.
 * 
 * The tempo data consists of one or more tempo codes. Each tempo code consists
 * of one tempo part and one time part. The tempo is in BPM described with one
 * or two bytes. If the first byte has the value $FF, one more byte follows,
 * which is added to the first giving a range from 2 - 510 BPM, since $00 and
 * $01 is reserved. $00 is used to describe a beat-free time period, which is
 * not the same as a music-free time period. $01 is used to indicate one single
 * beat-stroke followed by a beat-free period.
 *
 * The tempo descriptor is followed by a time stamp. Every time the tempo in the
 * music changes, a tempo descriptor may indicate this for the player. All tempo
 * descriptors must be sorted in chronological order. The first beat-stroke in
 * a time-period is at the same time as the beat description occurs. There may
 * only be one SYTC frame in each tag.
 *
 * @todo       The data could be parsed further; data samples needed
 * @package    php-reader
 * @subpackage ID3
 * @author     Sven Vollbehr <svollbehr@gmail.com>
 * @copyright  Copyright (c) 2008 The PHP Reader Project Workgroup
 * @license    http://code.google.com/p/php-reader/wiki/License New BSD License
 * @version    $Rev: 75 $
 */
final class ID3_Frame_SYTC extends ID3_Frame
  implements ID3_Timing
{
  /** @var integer */
  private $_format = 1;
  
  /** @var string */
  private $_tempoData;
  
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

    $this->_format = Transform::fromInt8($this->_data[0]);
    $this->_tempoData = substr($this->_data, 1); // FIXME: Better parsing of data
  }
  
  /**
   * Returns the timing format.
   * 
   * @return integer
   */
  public function getFormat() { return $this->_format; }

  /**
   * Sets the timing format.
   * 
   * @see ID3_Timing
   * @param integer $format The timing format.
   */
  public function setFormat($format) { $this->_format = $format; }
  
  /**
   * Returns the tempo data.
   * 
   * @return string
   */
  public function getData() { return $this->_tempoData; }
  
  /**
   * Sets the tempo data.
   * 
   * @param string $data The data string.
   */
  public function setData($tempoData) { $this->_tempoData = $tempoData; }

  /**
   * Returns the frame raw data.
   *
   * @return string
   */
  public function __toString()
  {
    parent::setData(Transform::toInt8($this->_format) . $this->_tempoData);
    return parent::__toString();
  }
}

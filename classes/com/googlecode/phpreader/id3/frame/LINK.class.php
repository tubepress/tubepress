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
 * @version    $Id: LINK.php 75 2008-04-14 23:57:21Z svollbehr $
 */

/**#@+ @ignore */
//require_once("ID3/Frame.php");
/**#@-*/

/**
 * The <i>Linked information</i> frame is used to keep information duplication
 * as low as possible by linking information from another ID3v2 tag that might
 * reside in another audio file or alone in a binary file. It is recommended
 * that this method is only used when the files are stored on a CD-ROM or other
 * circumstances when the risk of file separation is low.
 *
 * Data should be retrieved from the first tag found in the file to which this
 * link points. There may be more than one LINK frame in a tag, but only one
 * with the same contents.
 *
 * A linked frame is to be considered as part of the tag and has the same
 * restrictions as if it was a physical part of the tag (i.e. only one
 * {@link com_googlecode_phpreader_id3_frame_RVRB} frame allowed, whether it's linked or not).
 *
 * Frames that may be linked and need no additional data are
 * {@link com_googlecode_phpreader_id3_frame_ASPI}, {@link com_googlecode_phpreader_id3_frame_ETCO}, {@link com_googlecode_phpreader_id3_frame_EQU2},
 * {@link com_googlecode_phpreader_id3_frame_MCDI}, {@link com_googlecode_phpreader_id3_frame_MLLT}, {@link com_googlecode_phpreader_id3_frame_OWNE},
 * {@link com_googlecode_phpreader_id3_frame_RVA2}, {@link com_googlecode_phpreader_id3_frame_RVRB}, {@link com_googlecode_phpreader_id3_frame_SYTC}, the
 * text information frames (ie frames descendats of
 * {@link com_googlecode_phpreader_id3_frame_AbstractText}) and the URL link frames (ie frames descendants
 * of {@link com_googlecode_phpreader_id3_frame_AbstractLink}).
 *
 * The {@link com_googlecode_phpreader_id3_frame_AENC}, {@link com_googlecode_phpreader_id3_frame_APIC}, {@link com_googlecode_phpreader_id3_frame_GEOB}
 * and {@link com_googlecode_phpreader_id3_frame_TXXX} frames may be linked with the content descriptor
 * as additional ID data.
 *
 * The {@link com_googlecode_phpreader_id3_frame_USER} frame may be linked with the language field as
 * additional ID data.
 *
 * The {@link com_googlecode_phpreader_id3_frame_PRIV} frame may be linked with the owner identifier as
 * additional ID data.
 *
 * The {@link com_googlecode_phpreader_id3_frame_COMM}, {@link com_googlecode_phpreader_id3_frame_SYLT} and {@link com_googlecode_phpreader_id3_frame_USLT}
 * frames may be linked with three bytes of language descriptor directly
 * followed by a content descriptor as additional ID data.
 *
 * @package    php-reader
 * @subpackage ID3
 * @author     Sven Vollbehr <svollbehr@gmail.com>
 * @copyright  Copyright (c) 2008 The PHP Reader Project Workgroup
 * @license    http://code.google.com/p/php-reader/wiki/License New BSD License
 * @version    $Rev: 75 $
 */
final class com_googlecode_phpreader_id3_frame_LINK extends com_googlecode_phpreader_id3_Frame
{
  /** @var string */
  private $_target;

  /** @var string */
  private $_url;
  
  /** @var string */
  private $_qualifier;
  
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

    $this->_target = substr($this->_data, 0, 4);
    list($this->_url, $this->_qualifier) =
      preg_split("/\\x00/", substr($this->_data, 4), 2);
  }
  
  /**
   * Returns the target tag identifier.
   * 
   * @return string
   */
  public function getTarget() { return $this->_target; }
  
  /**
   * Sets the target tag identifier.
   * 
   * @param string $target The target tag identifier.
   */
  public function setTarget($target) { $this->_target = $target; }
  
  /**
   * Returns the target tag URL.
   * 
   * @return string
   */
  public function getUrl() { return $this->_url; }
  
  /**
   * Sets the target tag URL.
   * 
   * @param string $url The target URL.
   */
  public function setUrl($url) { $this->_url = $url; }
  
  /**
   * Returns the additional data to identify further the tag.
   * 
   * @return string
   */
  public function getQualifier() { return $this->_qualifier; }
  
  /**
   * Sets the additional data to be used in tag identification.
   * 
   * @param string $identifier The qualifier.
   */
  public function setQualifier($qualifier)
  {
    $this->_qualifier = $qualifier;
  }
  
  /**
   * Returns the frame raw data.
   *
   * @return string
   */
  public function __toString()
  {
    $this->setData
      (com_googlecode_phpreader_Transform::toString8(substr($this->_target, 0, 4), 4) .
       $this->_url . "\0" . $this->_qualifier);
    return parent::__toString();
  }
}

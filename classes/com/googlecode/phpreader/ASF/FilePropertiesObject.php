<?php
/**
 * PHP Reader Library
 *
 * Copyright (c) 2006-2008 The PHP Reader Project Workgroup. All rights
 * reserved.
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
 * @subpackage ASF
 * @copyright  Copyright (c) 2006-2008 The PHP Reader Project Workgroup
 * @license    http://code.google.com/p/php-reader/wiki/License New BSD License
 * @version    $Id: FilePropertiesObject.php 39 2008-03-26 17:27:22Z svollbehr $
 */

/**#@+ @ignore */
require_once("Object.php");
/**#@-*/

/**
 * The <i>ASF_File_Properties_Object</i> object implementation. This object
 * contains various information about the ASF file.
 *
 * @package    php-reader
 * @subpackage ASF
 * @author     Sven Vollbehr <svollbehr@gmail.com>
 * @copyright  Copyright (c) 2006-2008 The PHP Reader Project Workgroup
 * @license    http://code.google.com/p/php-reader/wiki/License New BSD License
 * @version    $Rev: 39 $
 */
final class ASF_FilePropertiesObject extends ASF_Object
{
  /** @var string */
  private $_fileId;

  /** @var string */
  private $_fileSize;

  /** @var string */
  private $_creationDate;

  /** @var string */
  private $_dataPacketsCount;

  /** @var string */
  private $_playDuration;

  /** @var string */
  private $_sendDuration;

  /** @var string */
  private $_preroll;

  /** @var string */
  private $_flags;

  /** @var string */
  private $_minimumDataPacketSize;

  /** @var string */
  private $_maximumDataPacketSize;

  /** @var string */
  private $_maximumBitrate;
  
  /**
   * Constructs the class with given parameters and reads object related data
   * from the ASF file.
   *
   * @param Reader  $reader The reader object.
   * @param string  $id     The object GUID identifier.
   * @param integer $size   The object size.
   */
  public function __construct($reader, $id, $size)
  {
    parent::__construct($reader, $id, $size);
    
    $this->_fileId = $this->_reader->readGUID();
    $this->_fileSize = $this->_reader->readInt64LE();
    $this->_creationDate = $this->_reader->readInt64LE();
    $this->_dataPacketsCount = $this->_reader->readInt64LE();

    $seconds = floor($this->_reader->readInt64LE() / 10000000);
    $minutes = floor($seconds / 60);
    $hours = floor($minutes / 60);
    $this->_playDuration =
      ($minutes > 0 ?
       ($hours > 0 ? $hours . ":" .
        str_pad($minutes % 60, 2, "0", STR_PAD_LEFT) : $minutes % 60) . ":" .
        str_pad($seconds % 60, 2, "0", STR_PAD_LEFT) : $seconds % 60);

    $seconds = floor($this->_reader->readInt64LE() / 10000000);
    $minutes = floor($seconds / 60);
    $hours = floor($minutes / 60);
    $this->_sendDuration =
      ($minutes > 0 ?
       ($hours > 0 ? $hours . ":" .
        str_pad($minutes % 60, 2, "0", STR_PAD_LEFT) : $minutes % 60) . ":" .
        str_pad($seconds % 60, 2, "0", STR_PAD_LEFT) : $seconds % 60);

    $this->_preroll = $this->_reader->readInt64LE();
    $this->_flags = $this->_reader->readUInt32LE();
    $this->_minimumDataPacketSize = $this->_reader->readUInt32LE();
    $this->_maximumDataPacketSize = $this->_reader->readUInt32LE();
    $this->_maximumBitrate = $this->_reader->readUInt32LE();
  }

  /**
   * Returns the file id field.
   *
   * @return integer
   */
  public function getFileId() { return $this->_fileId; }

  /**
   * Returns the file size field.
   *
   * @return integer
   */
  public function getFileSize() { return $this->_fileSize; }

  /**
   * Returns the creation date field.
   *
   * @return integer
   */
  public function getCreationDate() { return $this->_creationDate; }

  /**
   * Returns the data packets field.
   *
   * @return integer
   */
  public function getDataPacketsCount() { return $this->_dataPacketsCount; }

  /**
   * Returns the play duration field.
   *
   * @return integer
   */
  public function getPlayDuration() { return $this->_playDuration; }

  /**
   * Returns the send duration field.
   *
   * @return integer
   */
  public function getSendDuration() { return $this->_sendDuration; }

  /**
   * Returns the preroll field.
   *
   * @return integer
   */
  public function getPreroll() { return $this->_preroll; }

  /**
   * Returns the flags field.
   *
   * @return integer
   */
  public function getFlags() { return $this->_flags; }
  
  /**
   * Returns the minimum data packet size field.
   * 
   * @return integer
   */
  public function getMinimumDataPacketSize()
  {
    return $this->_minimumDataPacketSize;
  }
  
  /**
   * Returns the maximum data packet size field.
   * 
   * @return integer
   */
  public function getMaximumDataPacketSize()
  {
    return $this->_maximumDataPacketSize;
  }
  
  /**
   * Returns the maximum bitrate field.
   * 
   * @return integer
   */
  public function getMaximumBitrate() { return $this->_maximumBitrate; }
}

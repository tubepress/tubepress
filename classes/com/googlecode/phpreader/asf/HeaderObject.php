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
 * @version    $Id: HeaderObject.php 39 2008-03-26 17:27:22Z svollbehr $
 */

/**#@+ @ignore */
require_once("Object.php");
/**#@-*/

/**
 * The <i>ASF_Header_Object</i> object implementation. This object contains
 * objects that give information about the file. See corresponding object
 * classes for more.
 * 
 * @package    php-reader
 * @subpackage ASF
 * @author     Sven Vollbehr <svollbehr@gmail.com>
 * @copyright  Copyright (c) 2006-2008 The PHP Reader Project Workgroup
 * @license    http://code.google.com/p/php-reader/wiki/License New BSD License
 * @version    $Rev: 39 $
 */
final class ASF_HeaderObject extends ASF_Object
{
  /** @var     integer */
  private $_objectCount;

  /**
   * @internal Internal variable to have the start of the stream stored in.
   * @var      integer
   */
  private $_readerSOffset;

  /**
   * @internal Internal variable to have the current position of the stream
   *           pointer stored in.
   * @var      integer
   */
  private $_readerCOffset;

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

    $this->_readerSOffset = $this->_reader->getOffset();
    $this->_objectCount = $this->_reader->readUInt32LE();
    $this->_reader->skip(2);
    $this->_readerCOffset = $this->_reader->getOffset();
  }
  
  /**
   * Returns the number of standard ASF header objects this object contains.
   * 
   * @return integer
   */
  public function getObjectCount() { return $this->_objectCount; }

  /**
   * Checks whether there is more to be read within the bounds of the parent
   * object size. Returns <var>true</var> if there are child objects unread,
   * <var>false</var> otherwise.
   *
   * @return boolean
   */
  public function hasChildObjects()
  {
    return ($this->_readerSOffset + $this->_size) > $this->_readerCOffset;
  }

  /**
   * Returns the next ASF object or <var>false</var> if end of stream has been
   * reached. Returned objects are of the type <var>ASFObject</var> or of any of
   * the other object types that inherit from that base class.
   *
   * @todo   Only limited subset of possible child objects are regognized.
   * @return ASF_Object|false
   */
  public function nextChildObject()
  {
    $object = false;
    if ($this->hasChildObjects()) {
      $this->_reader->setOffset($this->_readerCOffset);
      $guid = $this->_reader->readGUID();
      $size = $this->_reader->readInt64LE();
      $offset = $this->_reader->getOffset();
      switch ($guid) {
      /* ASF_Content_Description_Object */
      case "75b22633-668e-11cf-a6d9-00aa0062ce6c":
        $object =
          new ASF_ContentDescriptionObject($this->_reader, $guid, $size);
        break;
      /* ASF_Header_Extension_Object */
      case "5fbf03b5-a92e-11cf-8ee3-00c00c205365":
        $this->_reader->skip(48);
        $this->_readerCOffset = $this->_reader->getOffset();
        $object = $this->nextChildObject();
        break;
      /* ASF_Extended_Content_Description_Object */
      case "d2d0a440-e307-11d2-97f0-00a0c95ea850":
        $object = new ASF_ExtendedContentDescriptionObject
          ($this->_reader, $guid, $size);
        break;
      /* ASF_File_Properties_Object */
      case "8cabdca1-a947-11cf-8ee4-00c00c205365":
        $object = new ASF_FilePropertiesObject($this->_reader, $guid, $size);
        break;
      default:  // not implemented
        $object = new ASF_Object($this->_reader, $guid, $size);
      }
      $this->_reader->setOffset(($this->_readerCOffset = $offset - 24 + $size));
    }
    return $object;
  }
}

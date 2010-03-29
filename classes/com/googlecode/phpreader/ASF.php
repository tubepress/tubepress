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
 * @version    $Id: ASF.php 39 2008-03-26 17:27:22Z svollbehr $
 */

/**#@+ @ignore */
require_once("Reader.php");
require_once("ASF/Object.php");
require_once("ASF/HeaderObject.php");
require_once("ASF/ContentDescriptionObject.php");
require_once("ASF/ExtendedContentDescriptionObject.php");
require_once("ASF/FilePropertiesObject.php");
/**#@-*/

/**
 * This class represents a file in Advanced Systems Format (ASF) as described in
 * {@link http://go.microsoft.com/fwlink/?LinkId=31334 The Advanced Systems
 * Format (ASF) Specification}. It is a file format that can contain various
 * types of information ranging from audio and video to script commands and
 * developer defined custom streams.
 *
 * This is hardly a full implementation of a ASF reader but provides you with
 * the ability to read metadata out of an ASF based file (WMA, WMV, etc).
 * 
 * The ASF file consists of code blocks that are called content objects. Each
 * of these objects have a format of their own. They may contain other objects
 * or other specific data. Each supported object has been implemented as their
 * own classes to ease the correct use of the information.
 * 
 * @package    php-reader
 * @subpackage ASF
 * @author     Sven Vollbehr <svollbehr@gmail.com>
 * @copyright  Copyright (c) 2006-2008 The PHP Reader Project Workgroup
 * @license    http://code.google.com/p/php-reader/wiki/License New BSD License
 * @version    $Rev: 39 $
 */
class ASF
{
  /** @var Reader */
  private $_reader;
  
  /**
   * Constructs the ASF class with given file.
   *
   * @param string $filename The path to the file.
   */
  public function __construct($filename)
  {
    $this->_reader = new Reader($filename);
  }
  
  /**
   * Checks whether there are objects left in the stream. Returns
   * <var>true</var> if there are objects left in the stream, <var>false</var>
   * otherwise.
   * 
   * @return boolean
   */
  public function hasObjects()
  {
    return $this->_reader->available();
  }
  
  /**
   * Returns the next ASF object or <var>false</var> if end of stream has been
   * reached. Returned objects are of the type ASF_Object or of any of its child
   * types.
   * 
   * @todo   Only the ASF_Header_Object top level object is regognized. 
   * @return ASF_Object
   */
  public function nextObject()
  {
    $object = false;
    if ($this->hasObjects()) {
      $guid = $this->_reader->readGUID();
      $size = $this->_reader->readInt64LE();
      $offset = $this->_reader->getOffset();

      switch ($guid) {
      case "75b22630-668e-11cf-a6d9-00aa0062ce6c": /* ASF_Header_Object */
        $object = new ASF_HeaderObject($this->_reader, $guid, $size);
        break;
      default:
        $object = new ASF_Object($this->_reader, $guid, $size);
      }
      $this->_reader->setOffset($offset - 24 + $size);
    }
    return $object;
  }
}

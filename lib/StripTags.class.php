<?php
/**
 *=--------------------------------------------------------------------------=
 * strip_tags.inc
 *=--------------------------------------------------------------------------=
 * This contains code to do some tag stripping that is both MBCS-safe and
 * more powerful than the default tag stripping available on the PHP 
 * strip_tags function.  This basically involves writing a little HTML
 * parsing state machine.  I'm not the best at this, but it seems to work
 * quite well, and isn't terribly inefficient.
 *
 * Author: marc, 2005-05-08
 *
 * Note that this class assumes all input is UTF-8.
 *
 * UNDONE: marc, 2005-05-08: add support for other character set encodings.
 */

/**
 * Copyright (c) 2005
 *      Marc Wandschneider. All Rights Reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. The name Marc Wandschneider may not be used to endorse or promote
 *    products derived from this software without specific prior
 *    written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS
 * OF USE, DATA, OR PROFITS; DEATH OF YOUR CAT, DOG, OR FISH; OR
 * BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN
 * IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 *=--------------------------------------------------------------------------=
 * StripTags
 *=--------------------------------------------------------------------------=
 * This class is how you use the tag stripping functionality.  In short, you
 * create an instance of it, passing to it an array keyed by HTML elements
 * that you would like to permit (or deny, based on the value of the second
 * parameter).  The VALUES of these indices are themselves arrays of permitted
 * attributes for each tag.  Thus, if we wanted to allow 'strong', 'em', 'a'
 * with href and title, and 'img' with src, border, and alt, we would 
 * create the object as follows:
 *
 * $tagsAndAttrs = array(
 *     'strong' => array(),    // no attrs allowed.
 *     'em' => array(),
 *     'a' => array('title', 'href'),
 *     'img' => array('src', 'border', 'alt')
 * );
 *
 * $st = new StripTags($tagsAndAttrs);
 *
 * Usage is then as simple as:
 *
 * $malicious = <<<EOS
 *   This is an eeveeeillll
 *   <script> document.location = "http://evilsite.url"; </script>
 *   string.
 * EOS;
 *
 * $fixed = $st->strip($malicious);
 */
class StripTags
{
  /**
   * RemoveColons Property:
   *
   * This property controls whether or not we should remove colons from
   * attribute values.  By default, we will remove any colons that do
   * not come right after an http, https, or ftp in an attribute
   * value.  This is very restrictive, but I haven't coded up a better
   * solution just yet ...
   */
  public $RemoveColons = FALSE;

  /**
   * List of tags and attributes.
   */
  protected $m_tagsAndAttrs;

  /**
   * Whether they are inclusive or exclusive lists 
   */
  protected $m_tagsInclusive;

  /**
   * These are used to maintain the state machine we use to parse through
   * strings.
   */
  protected $m_exploded;        // input string asploded
  protected $m_max;             // max size of m_exploded
  protected $m_x;               // current position
  protected $m_output;          // the string we're building.
  protected $m_currentTag;      // as we process attrs, the tag we're in.

  /**
   * Possible ways to end a tag -- SLASHGT /> and GT, >.
   */
  const SLASHGT = 0;
  const GT = 1;

  /**
   *=------------------------------------------------------------------------=
   * __construct
   *=------------------------------------------------------------------------=
   * Initialises a new instance of this class.  We require a list of tags and
   * attributes and a flag saying whether they are an inclusive or exclusive
   * set.
   *
   * Parameters:
   *    $in_tagsAndAttrs    - array keyed by tags, with values being arrays
   *                          of allowed attributes on those keys.
   *    $in_tagsincl        - [optional] are tags inclusive (only listed 
   *                          tags are allowed) or exclusive (all but those
   *                          tags are permitted).
   */
  public function __construct
  (
    $in_tagsAndAttrs,
    $in_tagsincl = TRUE
  )
  {
    if (!is_null($in_tagsAndAttrs) and !is_array($in_tagsAndAttrs))
      throw new InvalidArgumentException('$in_tagsAndAttrs');

    /**
     * save out the local vars, making sure that they have at least 
     * some value set.
     */
    $this->m_tagsAndAttrs = $in_tagsAndAttrs;
    if ($this->m_tagsAndAttrs === NULL)
      $this->m_tagsAndAttrs = array();
    $this->m_tagsInclusive = $in_tagsincl;
  }


  /**
   *=------------------------------------------------------------------------=
   * strip
   *=------------------------------------------------------------------------=
   * Removes evil baddie tags from the input string, excepting (or restricting
   * to) those tags specimified in the arguments to the constructor.
   *
   * Parameters:
   *      $in_string           - strip me please.
   * 
   *
   * Returns:
   *      stripped string.
   *
   * Notes:
   *      STRING IS ASSUMED TO BE UTF-8.
   */
  public function strip($in_string)
  {
    if ($in_string === NULL or $in_string == '')
      return '';

    /**
     * 1. explode the string into its constituent CHARACTERS (not bytes,
     *    which in UTF-8 are most certainly not the same thing).
     */
    $this->m_exploded = $this->explodeString($in_string);
    $this->m_max = count($this->m_exploded);

    /**
     * 2. Parse the string.  We will be quite robust about this, supporting
     *    arbitrary whitespace characters and > and < chars within attribute
     *    values (which is valid HTML, but prolly not valid XHTML).
     *    This will require setting up a bit of a state machine, which is a
     *    pain, but worth it.  Robustness is good.
     */
    $this->m_output = array();
    for ($this->m_x = 0; $this->m_x < $this->m_max; $this->m_x++)
    {
      if ($this->m_exploded[$this->m_x] != '<')
	$this->m_output[] = $this->m_exploded[$this->m_x];
      else
	$this->processTag();
    }

    return $this->rebuildString($this->m_output);
  }


  /**
   *=------------------------------------------------------------------------=
   * processTag
   *=------------------------------------------------------------------------=
   * We have encountered a tag in our string.  See if it's a valid tag and
   * process (out) any attributes within it.
   */
  protected function processTag()
  {
    /**
     * 1. Get the name of the tag and see if it's valid or not.
     */
    $this->m_x++;
    $tagName = $this->getTagName();
    if ($tagName === NULL)
      return ;                // there's nothing there!  

    if (!$this->isPermissibleTag($tagName))
    {
      $this->processEndOfTag();
      return;
    }
    else if (substr($tagName, 0, 1) == '/')
    {
      /**
       * If it's a closing tag, just consume everything up until the
       * closing tag character.
       */
      $this->processEndOfTag(FALSE);
      $fullTag = "<$tagName>";
      $this->m_output = array_merge($this->m_output,
				    $this->explodeString($fullTag));
    }
    else
    {
      /**
       * tag's valid.  go and get any attributes associated with it.
       */
      $this->m_currentTag = $tagName;
      $attrs = $this->processAttributes();
      $fullTag = "<$tagName";
      foreach ($attrs as $attr)
      {
        if ($attr['value'] != '')
        {
          $fullTag .= " {$attr['name']}=\""
            . $this->furtherProcess($attr['value']) . "\"";
        }
        else
          $fullTag .= " {$attr['name']}";
      }

      /**
       * figure out closing tag type and duplicate.
       */
      $tagType = $this->processEndOfTag();
      $fullTag .= ($tagType == StripTags::SLASHGT) ? '/>' : '>';
      $this->m_output = array_merge($this->m_output,
                                    $this->explodeString($fullTag));
    }
  }


  /**
   *=------------------------------------------------------------------------=
   * getTagName
   *=------------------------------------------------------------------------=
   * Given that we are positioned RIGHT after the opening < char, go and
   * find the name of the tag.  We will actually handle the case where we 
   * are given an empty tag, like < > or < />.
   *
   * Returns:
   *      string name or NULL indicating empty tag (or EOS)
   */
  protected function getTagName()
  {
    /**
     * skip over any space chars.
     */
    $this->consumeWhiteSpace();
    $tag = array();

    /**
     * Is it a closing tag??
     */
    if ($this->m_x < $this->m_max and $this->m_exploded[$this->m_x] == '/')
    {
      $tag[] = '/';
      $this->m_x++;
    }

    /**
     * now get anything until the next whitespace character or /> or >.
     */
    while ($this->m_x < $this->m_max
           and !$this->isSpaceChar($this->m_exploded[$this->m_x])
           and ($this->m_exploded[$this->m_x] != '>')
           and !($this->m_exploded[$this->m_x] == '/'
                 and $this->m_x + 1 < $this->m_max
                 and $this->m_exploded[$this->m_x + 1] == '>'))
    {
      $tag[] = $this->m_exploded[$this->m_x];
      $this->m_x++;
    }

    if (count($tag) == 0)
      return NULL;
    else
      return $this->rebuildString($tag);
  }


  /**
   *=------------------------------------------------------------------------=
   * isPermissibleTag
   *=------------------------------------------------------------------------=
   * Checks to see whether the given tag is valid or not given the user's
   * options to our constructor.
   *
   * Parameters:
   *      $in_tagName                   - tag name to check.
   *
   * Returns:
   *      TRUE == ok, FALSE == AAAIEEEE!!!
   */
  protected function isPermissibleTag($in_tagName)
  {
    /**
     * If it's a closing tag, remove the / for the purposes of this search.
     */
    if (substr($in_tagName, 0, 1) == '/')
      $check = substr($in_tagName, 1);
    else
      $check = $in_tagName;

    /**
     * Zip through all the tags in the array seeing if it is
     * valid.  We have to see if they gave us an inclusive or
     * exclusive list of permissible tags.
     */
    foreach ($this->m_tagsAndAttrs as $tag => $attrs)
    {
      $t = trim($tag);
      if ($this->m_tagsInclusive)
      {
        if ($t == $check)
          return TRUE;
      }
      else
      {
        if ($t == $check)
          return FALSE;
      }
    }

    return $this->m_tagsInclusive ? FALSE : TRUE;
  }



  /**
   *=------------------------------------------------------------------------=
   * processEndOfTag
   *=------------------------------------------------------------------------=
   * Skip all characters looking for the end of tag (> or />).  Unfortunately,
   * we cannnot simply zip through the string looking for these two 
   * character sequences, as they might be embedded within quotes.  We thus
   * have to manage a little state and remember whether or not we are in
   * quotes ...
   *
   * Parameters:
   *      $in_slashGTOk          - is /> allowed or only >  ??
   *
   * Returns:
   *      SLASHGT or GT, indicating which type of closing tag was found.
   */
  protected function processEndOfTag($in_slashGTOk = TRUE)
  {
    /**
     * This is not as simple as just looking for the next > character,
     * as that might be within an attribute string.  We will thus
     * have to maintain some state and make sure that we handle that
     * case properly.
     */
    $in_quote = FALSE;
    $quote_char = '';

    while ($this->m_x < $this->m_max)
    {
      switch ($this->m_exploded[$this->m_x])
      {
        case '\'':
          if ($in_quote and $quote_char == '\'')
            $in_quote = FALSE;
          else if (!$in_quote)
          {
            $in_quote = TRUE;
            $quote_char = '\'';
          }
          $this->m_x++;
          break;

        case '"':
          if ($in_quote and $quote_char == '"')
            $in_quote = FALSE;
          else if (!$in_quote)
          {
            $in_quote = TRUE;
            $quote_char = '"';
          }

          $this->m_x++;
          break;

        case '/':
          if (!$in_quote
              and ($this->m_x + 1 < $this->m_max)
              and $this->m_exploded[$this->m_x + 1] = '>'
              and $in_slashGTOk)
          {
            $this->m_x += 1;
            return StripTags::SLASHGT;
          }
          
          $this->m_x++;
          break;

        case '>':
          if (!$in_quote)
          {
            return StripTags::GT;
          }

          $this->m_x++;
          break;

        default:
          $this->m_x++;
          break;
      }
    }
  }


  /**
   *=------------------------------------------------------------------------=
   * processAttributes
   *=------------------------------------------------------------------------=
   * Given that we have a valid tag name, we are now going to go process its
   * attributes and see how we like them.  We will assume that all are in one
   * of the two following formats:
   *
   *  attribute = value   [valid is quoted string or single word]
   *  attribute           [attribute is sequence of non-space chars]
   *
   * Returns:
   *      an array of 'attrName' => 'attrValue' pairs.
   *
   * Note:
   *      the $m_x 'cursor' is pointing to the first space char right after
   *      the attr name.
   */
  protected function processAttributes()
  {
    $attrs = array();

    while (($attrDetails = $this->nextAttribute()) !== NULL)
    {
      if ($this->isPermissibleAttribute($attrDetails['name']))
        $attrs[] = $attrDetails;
    }

    return $attrs;
  }


  /**
   *=------------------------------------------------------------------------=
   * nextAttribute
   *=------------------------------------------------------------------------=
   * We are processing a tag.  Get the next attribute, or return NULL if there
   * are no mo'.
   *
   * Returns:
   *      an array with 'attrName' => 'attrValue' or NULL if there is not 
   *      another attribute.
   */
  protected function nextAttribute()
  {
    /**
     * skip over any space chars.
     */
    $this->consumeWhiteSpace();

    /**
     * 1. Attribute Name.
     *
     * Now get anything until the next whitespace character, = character,
     * end of tag (> or />), or end of buffer.
     */
    $attr = array();
    while ($this->m_x < $this->m_max
           and !$this->isSpaceChar($this->m_exploded[$this->m_x])
           and ($this->m_exploded[$this->m_x] != '=')
           and ($this->m_exploded[$this->m_x] != '>')
           and !($this->m_exploded[$this->m_x] == '/' 
                 and $this->m_x + 1 < $this->m_max
                 and $this->m_exploded[$this->m_x + 1] == '>'))
    {
      $attr[] = $this->m_exploded[$this->m_x];
      $this->m_x++;
    }

    /**
     * If it's at the end of of the tag or the end of the string, then
     * evidence suggests we only got an attribute name.
     */
    if ($this->m_x == $this->m_max
        or $this->m_exploded[$this->m_x] == '>'
        or $this->m_exploded[$this->m_x] == '/')
    {
      if (count($attr) > 0)
        return array('name' => $this->rebuildString($attr), 'value' => '');
      else
        return NULL;
    }

    /**
     * We got a space.  If there is an = sign ahead after only whitespaces,
     * then that will point to the value.  Otherwise, we only have an attr
     * name.
     */
    if ($this->isSpaceChar($this->m_exploded[$this->m_x]))
    {
      if (!$this->peekAheadInTag('=')) 
        return array('name' => $this->rebuildString($attr), 'value' => '');
    }

    /**
     * otherwise, if we're here, then we're at an equals sign.
     */
    $this->m_x++;
    $this->consumeWhiteSpace();
    if ($this->m_x == $this->m_max)
      return array('name' => $this->rebuildString($attr), 'value' => '');
    
    /**
     * 2. Attribute Value
     *
     * Now get anything until the next whitespace character,
     * end of tag (> or />), or end of buffer.  We have to be careful,
     * however, to be able to handle a string enclosed attribute
     * value, such as 'this is a value'.
     */
    $in_quote = FALSE;
    $quote_char = '';
    $value = array();
    if ($this->isQuoteChar($this->m_exploded[$this->m_x]))
    {
      $in_quote = TRUE;
      $quote_char = $this->m_exploded[$this->m_x];
      $this->m_x++;
    }

    /**
     * This is an annoying expression.  We want to skip characters IF:
     *
     * - we are in a quoted attr value and the current character is not
     *   the closing quote.
     * OR
     * - we are NOT in a quoted attr value and the current character is
     *   not:
     *    - EOS
     *    - >
     *    - />
     *    - white space
     *
     * In all cases, don't go past EOS.
     */
    while (($in_quote
            and $this->m_x < $this->m_max
            and $this->m_exploded[$this->m_x] != $quote_char)
           or (!$in_quote
               and  ($this->m_x < $this->m_max
                     and !$this->isSpaceChar($this->m_exploded[$this->m_x])
                     and ($this->m_exploded[$this->m_x] != '>')
                     and !($this->m_exploded[$this->m_x] == '/' 
                           and $this->m_x + 1 < $this->m_max
                           and $this->m_exploded[$this->m_x + 1] == '>'))))
    {
      $value[] = $this->m_exploded[$this->m_x];
      $this->m_x++;
    }

    if ($this->m_x < $this->m_max
        and $in_quote and $this->m_exploded[$this->m_x])
    {
      $this->m_x++;
    }

    /**
     * return the attribute name and value.
     */
    return array('name' => $this->rebuildString($attr), 
                 'value' => $this->rebuildString($value));
  }


  /**
   *=------------------------------------------------------------------------=
   * peekAheadInTag
   *=------------------------------------------------------------------------=
   * Looks to see if the NEXT NON-WHITESPACE character is the specified
   * character.
   *
   * Parameters:
   *      $in_char                      - character to look for.
   *
   * Returns:
   *      TRUE -- it is!  FALSE, it's not!
   *
   * Notes:
   *      IFF TRUE is returned, then $this->m_x is updated to point at this
   *      character.
   */
  protected function peekAheadInTag($in_char)
  {
    $x = $this->m_x;
    while ($x < $this->m_max and $this->isSpaceChar($this->m_exploded[$x])
           and $this->m_exploded[$x] != $in_char)
    {
      $x++;
    }

    if ($x == $this->m_max)
      return FALSE;
    else if ($this->m_exploded[$x] == $in_char)
    {
      $this->m_x = $x;
      return TRUE;
    }
    else
      return FALSE;
  }


  /**
   *=------------------------------------------------------------------------=
   * isPermissibleAttribute
   *=------------------------------------------------------------------------=
   * Checks to see whether the given attribute is valid or not given the
   * user's options to our constructor.
   *
   * Parameters:
   *      $in_attrName                   - attribute name to check.
   *
   * Returns:
   *      TRUE == ok, FALSE == AAAIEEEE!!!
   */
  protected function isPermissibleAttribute($in_attrName)
  {
    $attrs = $this->m_tagsAndAttrs[$this->m_currentTag];
    if ($attrs === NULL)
      $attrs = array();

    /**
     * Zip through all the attributes in the array seeing if it is
     * valid.  We have to see if they gave us an inclusive or
     * exclusive list of permissible attributes.
     */
    $check = strtolower($in_attrName);
    foreach ($attrs as $attr)
    {
      $t = strtolower(trim($attr));
      if ($t == $check)
        return TRUE;
    }

    return FALSE;
  }


  /**
   *=------------------------------------------------------------------------=
   * explodeString
   *=------------------------------------------------------------------------=
   * Takes the string given as input and asplodes it into its constituent
   * characters.
   *
   * Parameters:
   *      $in_string                    - asplode me please.
   *
   * Returns:
   *      Array of characters.
   *
   * Notes:
   *      We are assuming you have compiled PHP with mbstring turned on (Unix
   *      / OS X) or php_mbstring.dll enabled (Winders).  You should also have
   *      the following set up in your php.ini:
   *
   *      mbstring.language = Neutral
   *      mbstring.internal_encoding = UTF-8
   *      mbstring.http_output = UTF-8
   *      mbstring.func_overload = 7
   */
  protected function explodeString($in_string)
  {
    /**
     * UNDONE: marc, 2005-05-08: is there a faster way to do this?
     *         this is kinda sucky.
     *
     * We'll go ahead and call the mb_ functions here to make it clear
     * that we will not be testing this on non-mbcs installations.
     */
    $asploded = array();
    $len = strlen($in_string);
    $str = $in_string;
    for ($x = 0; $x < $len; $x++)
    {
      $asploded[] = substr($str, $x, 1);
    }

    return $asploded;
  }


  /**
   *=------------------------------------------------------------------------=
   * consumeWhiteSpace
   *=------------------------------------------------------------------------=
   * Sucks up characters as long as there are characters left in the string
   * AND they are whitespace characters.
   *
   * Notes:
   *      This function does not accept double-wide space characters such as
   *      those seen in Asian character sets.  We assume these are not valid
   *      in HTML documents.
   */
  protected function consumeWhiteSpace()
  {
    while ($this->m_x < $this->m_max 
           and $this->isSpaceChar($this->m_exploded[$this->m_x]))
      $this->m_x++;
  }


  /**
   *=------------------------------------------------------------------------=
   * isSpaceChar
   *=------------------------------------------------------------------------=
   * Is the next character a whitespace character.  We do NOT include
   * double wide space characters from Asian character sets.
   *
   * Parameters:
   *      $in_char                      - Character to examiminine.
   *
   * Returns:
   *      TRUE == it's a Space!   FALSE == it's not a space!
   */
  protected function isSpaceChar($in_char)
  {
    switch ($in_char)
    {
      case ' ':
      case "\t":
      case "\n":
      case "\r":
      case "\v":
        return TRUE;
      default:
        return FALSE;
    }
  }


  /**
   *=------------------------------------------------------------------------=
   * isQuoteChar
   *=------------------------------------------------------------------------=
   * Asks whether the given UTF-8 character is a quote character.  We only
   * char about ISO-8859 quote characters, namely " and '.
   *
   * Parameters:
   *      $in_char                 - check me please.
   *
   * Returns:
   *      TRUE, si, ees a quote.
   *      FALSE, no mang, ees no a quote.
   */
  protected function isQuoteChar($in_char)
  {
    switch ($in_char)
    {
      case '\'':
      case '"':
        return TRUE;
    }

    return FALSE;
  }


  /**
   *=------------------------------------------------------------------------=
   * rebuildString
   *=------------------------------------------------------------------------=
   * Takes an array of characters and reconstructs a string for it.
   *
   * Parameters:
   *      $in_charArray                  - array containing chars
   *
   * Returns:
   *      string for those .
   */
  protected function rebuildString($in_charArray)
  {
    return implode('', $in_charArray);
  }


  /**
   *=------------------------------------------------------------------------=
   * furtherProcess
   *=------------------------------------------------------------------------=
   * This function actually inspects the text of an attribute string, and 
   * takes further action to try and prevent XSS by removing colons:
   *
   * UNDONE: marcwan, 2005-06-08: I'd like to have a better/more robust
   *         solution to inline scripting attacks.  Must ponder more.
   *
   * Parameters:
   *     $in_attrString                 - the attribute string to process more
   *
   * Returns:
   *     processed (and hopefully safe) attribute string.
   *
   * Notes:
   *     this function does nothing if RemoveColons is set to FALSE
   */
  protected function furtherProcess($in_attrString)
  {
    if (!$this->RemoveColons)
      return $in_attrString;

    //
    // 1. strip out colon characters, unless the attr value begins
    // with http:...
    //
    if (!ereg('^(https:|http:|ftp:)', trim($in_attrString), $matches))
    {
      $str = $in_attrString;
      $prefix = '';
    }
    else
    {
      $str = substr(trim($in_attrString), strlen($matches[0]));
      $prefix = $matches[0];
    }

    $str = ereg_replace(':', '', $str);

    //
    // 2. strip out ANY representations of : characters ... i can't imagine
    //    that this is even remotely efficient.
    //
    $str = ereg_replace('(&#[0]*58;|&#[xX][0]*3[aA];|\\\\[xX][0]*3[aA];|\\\\[uU][0]*3[aA];)', '', $str);

    return $prefix.$str;
  }


  /**
   *=------------------------------------------------------------------------=
   * escapeDoubleQuotes
   *=------------------------------------------------------------------------=
   * Double quotes will be replaced by &quot;
   *
   * Parameters:
   *      $in_string               - string to fix.
   *
   * Returns:
   *      fixed string.
   */
  protected function escapeDoubleQuotes($in_string)
  {
    return ereg_replace('"', '&quot;', $in_string);
  }

}


?>

<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * http://adventure-php-framework.org.
 *
 * The APF is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The APF is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
 * -->
 */
namespace APF\core\pagecontroller;

/**
 * Static parser for XML / XSL Strings.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 22.12.2006<br />
 */
final class XmlParser {

   /**
    * Let's you define the maximum number of attributes allows before the parser stops executions to prevent an endless loop.
    *
    * @var int $maxParserLoops
    */
   public static $maxParserLoops = 20;

   /**
    * @var int Internal counter to generate unique APF DOM tree node IDs.
    */
   private static $domNodeCounter = 1;

   private function __construct() {
   }

   /**
    * Extracts attributes and content from an XML tag string.
    *
    * @param string $prefix The prefix of the tag definition.
    * @param string $name The name of the tag definition.
    * @param string $tagString The string, that contains the tag definition.
    *
    * @return string[] The attributes of the tag.
    * @throws ParserException In case of tag mismatch.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 22.12.2006<br />
    * Version 0.2, 30.12.2006 (Bug-fix: tag-to-attribute delimiter is now a constant value)<br />
    * Version 0.3, 03.01.2007<br />
    * Version 0.4, 13.01.2007 (Improved error messages)<br />
    * Version 0.5, 16.11.2007 (Improved error message. Now affected tag string is displayed, too)<br />
    * Version 0.6, 03.11.2008 (Fixed the issue, that a TAB character is no valid token to attributes delimiter)<br />
    * Version 0.7, 04.11.2008 (Fixed issue, that a combination of TAB and SPACE characters leads to wrong attributes parsing)<br />
    * Version 0.8, 05.11.2008 (Removed the TAB support due to performance and fault tolerance problems)<br />
    * Version 0.9, 26.09.2012 (Introduced additional arguments for prefix and name to gain performance)<br />
    * Version 1.0, 23.12.2013 (ID#112: fixed parser issue with nested tags of the same tag name)<br />
    */
   public static function getTagAttributes($prefix, $name, $tagString) {

      // search for taglib to attributes string delimiter
      $tagAttributeDel = strpos($tagString, ' ');

      // search for the first appearance of the closing sign after the attribute string
      $posTagClosingSign = strpos($tagString, '>');

      // In case, the separator between tag and attribute is not found, or in case the tag
      // end position is located between the tag and the attribute, the end sign (">") is used
      // as separator. This allows tags without attributes.
      if ($tagAttributeDel === false || $tagAttributeDel > $posTagClosingSign) {
         $tagAttributeDel = strpos($tagString, '>');
      }

      // extract the rest of the tag string.
      $attributesString = substr($tagString, $tagAttributeDel + 1, $posTagClosingSign - $tagAttributeDel);

      // ID#253: In case we are using an extended templating expression within a tag attribute
      // (e.g. "model[0]->getFoo()") the ending ">" is contained within the attribute and thus the first
      // substr() produces wrong results. For this reason, search for the last ">" with an even number of
      // quotes in the string to fix this.
      $parserLoops = 0;
      while (substr_count($attributesString, '"') % 2 !== 0) {

         $parserLoops++;

         // limit parse loop count to avoid endless searching
         if ($parserLoops > self::$maxParserLoops) {
            throw new ParserException('[XmlParser::getTagAttributes()] Error while parsing: "'
                  . $tagString . '". Maximum number of loops ("' . self::$maxParserLoops
                  . '") exceeded!', E_USER_ERROR);
         }

         $posTagClosingSign = strpos($tagString, '>', $posTagClosingSign + 1);
         $attributesString = substr($tagString, $tagAttributeDel + 1, $posTagClosingSign - $tagAttributeDel);
      }

      // parse the tag's attributes
      $tagAttributes = XmlParser::getAttributesFromString($attributesString);

      // Check, whether the tag is self-closing. If not, read the content.
      if (substr($tagString, $posTagClosingSign - 1, 1) == '/') {
         $content = '';
      } else {
         // search for the outer-most explicit closing tag to support nested tag hierarchies
         $tagEndPos = strrpos($tagString, '</' . $prefix . ':' . $name . '>');
         if ($tagEndPos === false) {
            throw new ParserException('[XmlParser::getTagAttributes()] No closing tag found for '
                  . 'tag "<' . $prefix . ':' . $name . ' />"! Tag string: "' . $tagString . '".',
                  E_USER_ERROR);
         }

         // read the content of the tag
         $content = substr($tagString, $posTagClosingSign + 1, ($tagEndPos - $posTagClosingSign) - 1);
      }

      return [
            'attributes' => $tagAttributes,
            'content'    => $content
      ];
   }

   /**
    * Extracts XML attributes from an attributes string. Returns an associative array with the attributes as keys and
    * their associated values:
    * <pre>
    * $array['ATTRIBUTE_NAME'] = 'ATTRIBUTE_VALUE';
    * </pre>
    *
    * @param string $attributesString The attributes string of the tag to analyze.
    *
    * @return string[] The attributes of the tag.
    * @throws ParserException In case of tar attribute mismatch that may cause infinite loops.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 22.12.2006<br />
    * Version 0.2, 30.12.2006 (Enhanced the documentation)<br />
    * Version 0.3, 14.01.2007 (Improved the error message)<br />
    * Version 0.4, 14.11.2007 (Removed $hasFound; see http://forum.adventure-php-framework.org/viewtopic.php?t=7)<br />
    */
   public static function getAttributesFromString($attributesString) {

      $attributes = [];
      $offset = 0;

      $parserLoops = 0;

      while (true) {

         $parserLoops++;

         // limit parse loop count to avoid endless while loops
         if ($parserLoops > self::$maxParserLoops) {
            throw new ParserException('[XmlParser::getAttributesFromString()] Error while parsing: "'
                  . $attributesString . '". Maximum number of loops ("' . self::$maxParserLoops
                  . '") exceeded!', E_USER_ERROR);
         }

         // find attribute
         $foundAtr = strpos($attributesString, '=', $offset);

         // if no attribute was found -> end at this point
         if ($foundAtr === false) {
            break;
         }

         // extract values
         $key = substr($attributesString, $offset, $foundAtr - $offset);
         $attrValueStart = strpos($attributesString, '"', $foundAtr);
         $attrValueStart++;
         $attrValueEnd = strpos($attributesString, '"', $attrValueStart);
         $attrValue = substr($attributesString, $attrValueStart, $attrValueEnd - $attrValueStart);
         $offset = $attrValueEnd + 1;

         // add to key => value array
         $attributes[trim($key)] = $attrValue;
      }

      return $attributes;
   }

   /**
    * Generates a unique id, that is used as the object id for the APF DOM tree.
    *
    * @return string The unique id used as GUID for the APF DOM tree.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 22.12.2006<br />
    * Version 0.2, 25.10.2012 (Removed md5 choosing parameter to gain performance)<br />
    * Version 0.3, 27.07.2015 (Switched to static counter to gain performance by factor 3 up to 6)<br />
    */
   public static function generateUniqID() {
      // Parser is optimized to 32 characters of DOM GUIDs. Hence return a 32 characters unique string.
      return sprintf('node-%027s', self::$domNodeCounter++);
   }

}

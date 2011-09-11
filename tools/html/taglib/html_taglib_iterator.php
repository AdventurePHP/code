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
import('tools::html::taglib', 'iterator_taglib_item');
import('tools::html::taglib', 'iterator_taglib_addtaglib');
import('tools::html::taglib', 'iterator_taglib_getstring');
import('tools::html::taglib', 'iterator_taglib_placeholder');

/**
 * @package tools::html::taglib
 * @class html_taglib_iterator
 *
 * Implements a taglib, that can display a list of objects (arrays with numeric offsets)
 * or associative arrays by defining a iterator with items and place holders within the
 * items. For convenience, the iterator can contain additional (html) content.
 * <p/>
 * Further, the
 * <pre><iterator:addtaglib /></pre>
 * tag allows you to include custom tags (e.g. for language dependent table headers). In
 * order to display language dependent values, you can use the
 * <pre><iterator:getstring /></pre>
 * tag.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 01.06.2008<br />
 * Version 0.2, 04.06.2008 (Replaced __getIteratorItem() with key())<br />
 */
class html_taglib_iterator extends Document {

   /**
    * @protected
    * Data container. Array with numeric or associative offsets
    * or a list of objects.
    */
   protected $dataContainer = array();

   /**
    * @protected
    * Indicates, whether the iterator template should be displayed
    * at it's definition place (transform-on-place feature).
    */
   protected $transformOnPlace = false;

   /**
    * @public
    *
    * Defines the known taglibs. In this case, only the iterator item is parsed.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 01.06.2008<br />
    * Version 0.2, 09.08.2009 (Added the addtaglib tag to enable custom tags.)<br />
    */
   public function __construct() {
      $this->__TagLibs[] = new TagLib('tools::html::taglib', 'iterator', 'item');
      $this->__TagLibs[] = new TagLib('tools::html::taglib', 'iterator', 'addtaglib');
      $this->__TagLibs[] = new TagLib('tools::html::taglib', 'iterator', 'getstring');
      $this->__TagLibs[] = new TagLib('tools::html::taglib', 'iterator', 'placeholder');
   }

   /**
    * @public
    *
    * Implements the onParseTime method. Parses the iterator item taglib.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 01.06.2008<br />
    */
   public function onParseTime() {
      $this->__extractTagLibTags();
   }

   /**
    * @public
    *
    * This method allows you to fill the data container. Arrays with associative
    * keys are allowed as well as lists of objects (arrays with numeric offsets).
    *
    * @param array $data List of objects of an associative array.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 01.06.2008<br />
    */
   public function fillDataContainer($data) {
      $this->dataContainer = $data;
   }

   /**
    * @public
    *
    * Activates the transform-on-place feature for the iterator tag.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 01.06.2008<br />
    */
   public function transformOnPlace() {
      $this->transformOnPlace = true;
   }

   /**
    * @public
    *
    * Creates the output of the iterator. Can be called manually to use the output within
    * a document controller or surrounding taglib or automatically using the
    * transform-on-place feature.
    *
    * @return string String representation of the iterator object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 01.06.2008<br />
    * Version 0.2, 04.06.2008 (Enhanced method)<br />
    * Version 0.3, 15.06.2008 (Bugfix: the item was not found using PHP5)<br />
    * Version 0.4, 09.08.2009 (Added new taglibs iterator:addtaglib and iterator:getstring due to request in forum)<br />
    */
   public function transformIterator() {

      $t = &Singleton::getInstance('BenchmarkTimer');
      $t->start('(html_taglib_iterator) ' . $this->getObjectId() . '::transformIterator()');

      $buffer = (string)'';

      // the iterator item must not always be the first child
      // of the current node!
      $itemObjectId = $this->getIteratorItemObjectId();
      $iteratorItem = &$this->__Children[$itemObjectId];
      /* @var $iteratorItem iterator_taglib_item */

      // define the dynamic getter.
      $getter = $iteratorItem->getAttribute('getter');

      // get the place holders
      $placeHolders = &$iteratorItem->getPlaceHolders();

      $itemCount = count($this->dataContainer);
      for ($i = 0; $i < $itemCount; $i++) {

         if (is_array($this->dataContainer[$i])) {

            foreach ($placeHolders as $objectId => $DUMMY) {
               $placeHolders[$objectId]->setContent($this->dataContainer[$i][$placeHolders[$objectId]->getAttribute('name')]);
            }

            $buffer .= $iteratorItem->transform();

         } elseif (is_object($this->dataContainer[$i])) {

            foreach ($placeHolders as $objectId => $DUMMY) {
               $placeHolders[$objectId]->setContent($this->dataContainer[$i]->{
                                                    $getter
                                                    }($placeHolders[$objectId]->getAttribute('name')));
            }

            $buffer .= $iteratorItem->transform();

         } else {
            throw new InvalidArgumentException('[html_taglib_iterator::transformIterator()] '
                                               . 'Given list entry is not an array or object (' . $this->dataContainer[$i]
                                               . ')! The data container must contain a list of associative arrays or objects!',
               E_USER_WARNING);
         }

      }

      $t->stop('(html_taglib_iterator) ' . $this->__ObjectID . '::transformIterator()');

      // add the surrounding content of the iterator to enable the
      // user to define some html code as well.
      $iterator = str_replace('<' . $itemObjectId . ' />', $buffer, $this->__Content);

      // transform all other child tags except the iterator item(s)
      foreach ($this->__Children as $objectId => $DUMMY) {

         if (get_class($this->__Children[$objectId]) !== 'iterator_taglib_item') {
            $iterator = str_replace('<' . $objectId . ' />', $this->__Children[$objectId]->transform(), $iterator);
         }

      }

      return $iterator;

   }

   /**
    * @public
    *
    *  Implements the transform method for the iterator tag.
    *
    * @return string Content of the tag or an empty string.
    *
    * @author Christian Achatz
    * @version
    *  Version 0.1, 01.06.2008<br />
    */
   public function transform() {

      if ($this->transformOnPlace === true) {
         return $this->transformIterator();
      }

      return (string)'';

   }

   /**
    * @protected
    *
    * Returns the first iterator item, that is found in the children list.
    * All other occurrences are ignored, due to the fact, that it is not
    * allowed to define more that one iterator item.
    *
    * @return string The iterator item's object id.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.08.2009<br />
    */
   protected function getIteratorItemObjectId() {

      foreach ($this->__Children as $objectId => $DUMMY) {
         if (get_class($this->__Children[$objectId]) === 'iterator_taglib_item') {
            return $objectId;
         }
      }

      // defining no iterator item is not allowed!
      throw new InvalidArgumentException('[html_taglib_iterator::getIteratorItemObjectId()] '
                                         . 'The definition for iterator "' . $this->getAttribute('name')
                                         . '" does not contain a iterator item, hence this is no legal iterator tag '
                                         . 'definition. Please refer to the documentation.', E_USER_ERROR);

   }

   /**
    * @public
    *
    * Fills a place holder within the iterator.
    *
    * @param string $name The name of the place holder to set.
    * @param string $value The value of the place holder.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.09.2010<br />
    */
   public function setPlaceHolder($name, $value) {
      $count = 0;
      foreach ($this->__Children as $objectId => $DUMMY) {
         if (get_class($this->__Children[$objectId]) == 'iterator_taglib_placeholder'
             && $this->__Children[$objectId]->getAttribute('name') === $name
         ) {
            $this->__Children[$objectId]->setContent($value);
            $count++;
         }
      }

      if ($count == 0 || count($this->__Children) == 0) {
         throw new InvalidArgumentException('[' . get_class($this) . '::setPlaceHolder()] No place '
                                            . 'holder object with name "' . $name . '" can be found within html:iterator tag '
                                            . 'with name "' . $this->getAttribute('name') . '" requested in document controller '
                                            . '"' . ($this->getParentObject()->getDocumentController()) . '"!', E_USER_ERROR);
      }
   }

   /**
    * @public
    *
    * This method is for concenient setting of multiple place holders. The applied
    * array must contain a structure like this:
    * <code>
    * array(
    *    'key-a' => 'value-a',
    *    'key-b' => 'value-b',
    *    'key-c' => 'value-c',
    *    'key-d' => 'value-d',
    *    'key-e' => 'value-e',
    * )
    * </code>
    * Thereby, the <em>key-*</em> offsets define the name of the place holders, theire
    * values are used as the place holder's values.
    *
    * @param array $placeHolderValues Key-value-couples to fill place holders.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.11.2010<br />
    */
   public function setPlaceHolders(array $placeHolderValues) {
      foreach ($placeHolderValues as $key => $value) {
         $this->setPlaceHolder($key, $value);
      }
   }

}

?>
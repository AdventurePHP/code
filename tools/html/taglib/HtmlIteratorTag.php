<?php
namespace APF\tools\html\taglib;

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
use APF\core\pagecontroller\Document;
use APF\core\pagecontroller\TemplateTag;
use APF\tools\request\RequestHandler;
use InvalidArgumentException;

/**
 * @package APF\tools\html\taglib
 * @class IteratorStatus
 *
 * Represents the status of a current loop run within the iterator. Can be accessed
 * via the extended template syntax:
 * <code>
 * ${status->isFirst()}
 * </code>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 12.05.2014 (ID#189: introduced status variable to ease access and usage)<br />
 */
class IteratorStatus {

   /**
    * @var bool <em>True</em>, in case current loop outputs the first element of the list, <em>false</em> otherwise.
    */
   private $isFirst;

   /**
    * @var bool <em>True</em>, in case current loop outputs the last element of the list, <em>false</em> otherwise.
    */
   private $isLast;

   /**
    * @return int The number of total items within this iterator run.
    */
   private $itemCount;

   /**
    * @return int A counter that increments with each loop run. Can be used to number lists and tables.
    */
   private $counter;

   /**
    * @var string Css class tailored to the current loop run (first, middle, last).
    */
   private $cssClass;

   public function __construct($isFirst, $isLast, $itemCount, $counter, $cssClass) {
      $this->isFirst = $isFirst;
      $this->isLast = $isLast;
      $this->itemCount = $itemCount;
      $this->counter = $counter;
      $this->cssClass = $cssClass;
   }

   public function getCssClass() {
      return $this->cssClass;
   }

   public function isFirst($asString = false) {
      return $asString === false ? $this->isFirst : $this->convertToString($this->isFirst);
   }

   public function isLast($asString = false) {
      return $asString === false ? $this->isLast : $this->convertToString($this->isLast);
   }

   public function getItemCount() {
      return $this->itemCount;
   }

   public function getCounter() {
      return $this->counter;
   }

   private function convertToString($bool) {
      return $bool === true ? '1' : '0';
   }

}

/**
 * @package APF\tools\html\taglib
 * @class HtmlIteratorTag
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
 * Version 0.3, 11.05.2014 (ID#187: allow template expressions within iterators)<br />
 */
class HtmlIteratorTag extends Document {

   /**
    * @const Defines the "normal" fallback mode (fallback content is displayed additionally).
    */
   const FALLBACK_MODE_NORMAL = 'normal';

   /**
    * @const Defines the "extended" fallback mode (fallback content is displayed instead).
    */
   const FALLBACK_MODE_REPLACE = 'replace';

   /**
    * @const Defines default CSS class for first item.
    */
   const DEFAULT_CSS_CLASS_FIRST = 'first';

   /**
    * @const Defines default CSS class for "normal" items.
    */
   const DEFAULT_CSS_CLASS_MIDDLE = 'middle';

   /**
    * @const Defines default CSS class for last item.
    */
   const DEFAULT_CSS_CLASS_LAST = 'last';

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
    * @protected
    * The iteration number
    */
   protected $iterationNumber = 0;

   public function __construct() {
      // nothing to do here, especially not generate document id as it is generated by the APF parser.
   }

   public function onParseTime() {
      $this->extractTagLibTags();
   }

   public function onAfterAppend() {
      $this->extractExpressionTags();
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
    * Sets the value of the iterationNumber-Object-Attribute
    *
    * @param integer $number The number of iterationNumber
    *
    * @author Nicolas Pecher
    * @version
    * Version 0.1, 15.03.2012
    */
   public function setIterationNumber($number) {
      $this->iterationNumber = intval($number);
   }

   /**
    * @public
    *
    * Creates the output of the iterator. Can be called manually to use the output within
    * a document controller or surrounding taglib or automatically using the
    * transform-on-place feature.
    *
    * @return string String representation of the iterator object.
    * @throws InvalidArgumentException In case the data container does not contain an array or object list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 01.06.2008<br />
    * Version 0.2, 04.06.2008 (Enhanced method)<br />
    * Version 0.3, 15.06.2008 (Bug-fix: the item was not found using PHP5)<br />
    * Version 0.4, 09.08.2009 (Added new taglibs iterator:addtaglib and iterator:getstring due to request in forum)<br />
    * Version 0.5, 11.05.2014 (ID#105: added fallback content feature)<br />
    */
   public function transformIterator() {

      $buffer = '';

      // set iteration number if it's value is zero
      if ($this->iterationNumber == 0) {

         $this->iterationNumber = 1; // Default value 

         $pager = $this->getAttribute('pager', false);

         if ($pager != false) {

            // get pager-config
            $pagerConfig = $this->getConfiguration('APF\modules\pager', 'pager.ini')->getSection($pager);

            // get the number of entries per page
            $entriesPerPage = RequestHandler::getValue(
                  $pagerConfig->getValue('Pager.ParameterCountName'),
                  $pagerConfig->getValue('Pager.EntriesPerPage')
            );

            // get the number of the actual page
            $actualPage = RequestHandler::getValue(
                  $pagerConfig->getValue('Pager.ParameterPageName'),
                  1
            );

            $startNumber = $entriesPerPage * (--$actualPage);
            $startNumber++;
            $this->iterationNumber = $startNumber;

         }

      }

      // the iterator item must not always be the first child
      // of the current node!
      $itemObjectId = $this->getIteratorItemObjectId();
      $iteratorItem = & $this->children[$itemObjectId];
      /* @var $iteratorItem HtmlIteratorItemTag */

      // define the dynamic getter.
      $getter = $iteratorItem->getAttribute('getter');

      // get the place holders
      $placeHolders = & $iteratorItem->getPlaceHolders();

      $itemCount = count($this->dataContainer);

      // ID#105: display fallback content in case no items are available.
      if ($itemCount === 0) {

         /* @var $fallback TemplateTag */
         $fallbackObjectId = $this->getFallbackContentItemObjectId();

         if ($fallbackObjectId !== null) {
            $fallbackMode = $this->getAttribute('fallback-mode', self::FALLBACK_MODE_NORMAL);

            if ($fallbackMode === self::FALLBACK_MODE_NORMAL) {
               // activate auto-transformation to display fallback content.
               /** @noinspection PhpUndefinedMethodInspection */
               $this->children[$fallbackObjectId]->transformOnPlace();
            } else {
               // display fallback content exclusively
               /** @noinspection PhpUndefinedMethodInspection */
               return $this->children[$fallbackObjectId]->transformTemplate();
            }
         }

      } else {

         for ($i = 0; $i < $itemCount; $i++) {

            // ID#187: fill data container of the iterator item to allow object and array
            // access from within the item using APF's template expression language.
            $iteratorItem->setData('item', $this->dataContainer[$i]);

            // ID#189: make status variables available for current run to allow output customizing on
            // that basis (e.g. output separate CSS classes using custom tags within an <iterator:item />).
            $isFirst = $i === 0;
            $isLast = $i === ($itemCount - 1);

            // ID#189: make CSS classes available tailored to the current loop run
            if ($isFirst) {
               $cssClass = $this->getAttribute('first-element-css-class', self::DEFAULT_CSS_CLASS_FIRST);
            } else if ($isLast) {
               $cssClass = $this->getAttribute('last-element-css-class', self::DEFAULT_CSS_CLASS_LAST);
            } else {
               $cssClass = $this->getAttribute('middle-element-css-class', self::DEFAULT_CSS_CLASS_MIDDLE);
            }

            $iteratorItem->setData('status', new IteratorStatus($isFirst, $isLast, $itemCount, $this->iterationNumber, $cssClass));

            if (is_array($this->dataContainer[$i])) {

               foreach ($placeHolders as $objectId => $DUMMY) {

                  // if we find a place holder with IterationNumber as name-Attribute-Value set Iteration number
                  if ($placeHolders[$objectId]->getAttribute('name') == 'IterationNumber') {
                     $placeHolders[$objectId]->setContent($this->iterationNumber);
                     continue;
                  }

                  $placeHolders[$objectId]->setContent($this->dataContainer[$i][$placeHolders[$objectId]->getAttribute('name')]);
               }

               $buffer .= $iteratorItem->transform();

            } elseif (is_object($this->dataContainer[$i])) {

               foreach ($placeHolders as $objectId => $DUMMY) {

                  // if we find a place holder with IterationNumber as name-Attribute-Value set Iteration number
                  if ($placeHolders[$objectId]->getAttribute('name') == 'IterationNumber') {
                     $placeHolders[$objectId]->setContent($this->iterationNumber);
                     continue;
                  }

                  // evaluate per-place-holder getter
                  $localGetter = $placeHolders[$objectId]->getAttribute('getter');
                  if ($localGetter == null) {
                     $placeHolders[$objectId]->setContent($this->dataContainer[$i]->{
                                 $getter
                                 }(
                                       $placeHolders[$objectId]->getAttribute('name'))
                     );
                  } else {
                     $placeHolders[$objectId]->setContent($this->dataContainer[$i]->{
                           $localGetter
                           }());
                  }
               }

               $buffer .= $iteratorItem->transform();

            } else {
               throw new InvalidArgumentException('[HtmlIteratorTag::transformIterator()] '
                     . 'Given list entry is not an array or object (' . $this->dataContainer[$i]
                     . ')! The data container must contain a list of associative arrays or objects!',
                     E_USER_WARNING);
            }

            // increment counter that can be used to number lists or tables
            $this->iterationNumber++;
         }
      }

      // add the surrounding content of the iterator to enable the
      // user to define some html code as well.
      $html = str_replace('<' . $itemObjectId . ' />', $buffer, $this->content);

      // Transform all other child tags except the iterator item(s).
      // ID#105: this also includes the default content in case no items available (case: mode=normal)
      foreach ($this->children as $objectId => $DUMMY) {

         if (!($this->children[$objectId] instanceof HtmlIteratorItemTag)) {
            $html = str_replace('<' . $objectId . ' />', $this->children[$objectId]->transform(), $html);
         }

      }

      return $html;
   }

   /**
    * @public
    *
    * Implements the transform method for the iterator tag.
    *
    * @return string Content of the tag or an empty string.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 01.06.2008<br />
    */
   public function transform() {
      if ($this->transformOnPlace === true) {
         return $this->transformIterator();
      }

      return '';
   }

   /**
    * @protected
    *
    * Returns the first iterator item, that is found in the children list.
    * All other occurrences are ignored, due to the fact, that it is not
    * allowed to define more that one iterator item.
    *
    * @return string The iterator item's object id.
    * @throws \InvalidArgumentException In case no <iterator:item /> is specified.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.08.2009<br />
    */
   protected function getIteratorItemObjectId() {

      foreach ($this->children as $objectId => $DUMMY) {
         if ($this->children[$objectId] instanceof HtmlIteratorItemTag) {
            return $objectId;
         }
      }

      // defining no iterator item is not allowed!
      throw new InvalidArgumentException('[HtmlIteratorTag::getIteratorItemObjectId()] '
            . 'The definition for iterator "' . $this->getAttribute('name')
            . '" does not contain a iterator item, hence this is no legal iterator tag '
            . 'definition. Please refer to the documentation.', E_USER_ERROR);

   }

   /**
    * @protected
    *
    * Returns the fallback content template object id if found in the children list.
    * All other occurrences are ignored, due to the fact, that it is not
    * allowed to define more that one fallback content.
    *
    * @return string|null The fallback content's object id or <em>null</em> in case no <iterator:fallback /> is specified.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.05.2014<br />
    */
   protected function getFallbackContentItemObjectId() {

      foreach ($this->children as $objectId => $DUMMY) {
         if ($this->children[$objectId] instanceof TemplateTag) {
            return $objectId;
         }
      }

      return null;
   }

   /**
    * @public
    *
    * Returns the fallback content template in case defined for the present iterator.
    *
    * @return TemplateTag|null Fallback template or <em>null</em> in case nothing is defined.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.05.2014<br />
    */
   public function &getFallbackContent() {

      $fallbackObjectId = $this->getFallbackContentItemObjectId();

      if ($fallbackObjectId !== null) {
         return $this->children[$fallbackObjectId];
      }

      // avoid PHP issue "only variables can be returned as reference"
      $null = null;

      return $null;
   }

}

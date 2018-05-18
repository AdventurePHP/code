<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
namespace APF\tools\html\taglib;

use APF\core\pagecontroller\Document;
use APF\core\pagecontroller\DomNode;
use APF\core\pagecontroller\TemplateTag;
use APF\tools\html\Iterator;
use APF\tools\html\model\IteratorStatus;
use InvalidArgumentException;

/**
 * Implements a taglib, that can display a list of objects (arrays with numeric offsets)
 * or associative arrays by defining a iterator with items and place holders within the
 * items. For convenience, the iterator can contain additional (html) content.
 * <p/>
 * Further, the
 * <pre><core:addtaglib /></pre>
 * tag allows you to include custom tags (e.g. for language dependent table headers). In
 * order to display language dependent values, you can use the
 * <pre><html:getstring /></pre>
 * tag.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 01.06.2008<br />
 * Version 0.2, 04.06.2008 (Replaced __getIteratorItem() with key())<br />
 * Version 0.3, 11.05.2014 (ID#187: allow template expressions within iterators)<br />
 */
class HtmlIteratorTag extends Document implements Iterator {

   use GetIterator;

   /**
    * Defines the name of the iteration number place holder.
    *
    * @var string ITERATION_NUMBER_PLACE_HOLDER
    */
   const ITERATION_NUMBER_PLACE_HOLDER = 'IterationNumber';

   /**
    * Defines the "normal" fallback mode (fallback content is displayed additionally).
    *
    * @var string FALLBACK_MODE_NORMAL
    */
   const FALLBACK_MODE_NORMAL = 'normal';

   /**
    * Defines the "extended" fallback mode (fallback content is displayed instead).
    *
    * @var string FALLBACK_MODE_REPLACE
    */
   const FALLBACK_MODE_REPLACE = 'replace';

   /**
    * Defines default CSS class for first item.
    *
    * @var string DEFAULT_CSS_CLASS_FIRST
    */
   const DEFAULT_CSS_CLASS_FIRST = 'first';

   /**
    * Defines default CSS class for "normal" items.
    *
    * @var string DEFAULT_CSS_CLASS_MIDDLE
    */
   const DEFAULT_CSS_CLASS_MIDDLE = 'middle';

   /**
    * Defines default CSS class for last item.
    *
    * @var string DEFAULT_CSS_CLASS_LAST
    */
   const DEFAULT_CSS_CLASS_LAST = 'last';

   /**
    * Data container. Array with numeric or associative offsets
    * or a list of objects.
    *
    * @var array $dataContainer
    */
   protected $dataContainer = [];

   /**
    * Indicates, whether the iterator template should be displayed
    * at it's definition place (transform-on-place feature).
    */
   protected $transformOnPlace = false;

   /**
    * The iteration number
    */
   protected $iterationNumber = 0;

   public function onParseTime() {

      // ID#118: allow stacking of iterator tags by allowing an iterator to be
      // transformed at it's definition place activated by attribute.
      if ($this->getAttribute('transform-on-place', 'false') === 'true') {
         $this->transformOnPlace();
      }

      $this->extractTagLibTags();
   }

   /**
    * Activates the transform-on-place feature for the iterator tag.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 01.06.2008<br />
    */
   public function transformOnPlace() {
      $this->transformOnPlace = true;
   }

   public function fillDataContainer(array $data = []) {
      $this->dataContainer = $data;

      return $this;
   }

   /**
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
            $request = $this->getRequest();
            $entriesPerPage = $request->getParameter(
                  $pagerConfig->getValue('Pager.ParameterCountName'),
                  $pagerConfig->getValue('Pager.EntriesPerPage')
            );

            // get the number of the actual page
            $actualPage = $request->getParameter(
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

      /* @var $iteratorItem HtmlIteratorItemTag */
      $iteratorItem = &$this->children[$itemObjectId];
      $itemCount = count($this->dataContainer);

      // ID#105: display fallback content in case no items are available.
      if ($itemCount === 0) {

         $fallback = $this->getFallbackContent();

         if ($fallback !== null) {
            $fallbackMode = $this->getAttribute('fallback-mode', self::FALLBACK_MODE_NORMAL);

            if ($fallbackMode === self::FALLBACK_MODE_NORMAL) {
               // activate auto-transformation to display fallback content.
               $fallback->transformOnPlace();
            } else {
               // display fallback content exclusively
               return $fallback->transformTemplate();
            }
         }

      } else {

         // define the dynamic getter.
         $getter = $iteratorItem->getAttribute('getter');

         // get the place holders
         $placeHolders = $iteratorItem->getPlaceHolderNames();

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

            $iteratorItem->setPlaceHolder(self::ITERATION_NUMBER_PLACE_HOLDER, $this->iterationNumber);

            if (is_array($this->dataContainer[$i])) {

               // inject place holders into item and transform
               $iteratorItem->setPlaceHolders($this->dataContainer[$i]);
               $buffer .= $iteratorItem->transform();

            } elseif (is_object($this->dataContainer[$i])) {

               foreach ($placeHolders as $name) {

                  // don't touch iterator numbers as we've already set it above
                  if ($name === self::ITERATION_NUMBER_PLACE_HOLDER) {
                     continue;
                  }

                  // use getter defined with <iterator:item /> to retrieve appropriate value
                  $iteratorItem->setPlaceHolder($name, $this->dataContainer[$i]->{$getter}($name));
               }

               $buffer .= $iteratorItem->transform();
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
      foreach ($this->children as &$child) {
         if (!($child instanceof HtmlIteratorItemTag)) {
            $html = str_replace('<' . $child->getObjectId() . ' />', $child->transform(), $html);
         }
      }

      return $html;
   }

   /**
    * Returns the first iterator item, that is found in the children list.
    * All other occurrences are ignored, due to the fact, that it is not
    * allowed to define more that one iterator item.
    *
    * @return string The iterator item's object id.
    * @throws InvalidArgumentException In case no <iterator:item /> is specified.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.08.2009<br />
    */
   protected function getIteratorItemObjectId() {

      foreach ($this->children as &$child) {
         if ($child instanceof HtmlIteratorItemTag) {
            return $child->getObjectId();
         }
      }

      // defining no iterator item is not allowed!
      throw new InvalidArgumentException('[HtmlIteratorTag::getIteratorItemObjectId()] '
            . 'The definition for iterator "' . $this->getAttribute('name')
            . '" does not contain a iterator item, hence this is no legal iterator tag '
            . 'definition. Please refer to the documentation.', E_USER_ERROR);

   }

   /**
    * Returns the fallback content template in case defined for the present iterator.
    *
    * @return DomNode|TemplateTag|null
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.05.2014<br />
    */
   public function getFallbackContent() {

      $fallbackObjectId = $this->getFallbackContentItemObjectId();

      if ($fallbackObjectId !== null) {
         return $this->children[$fallbackObjectId];
      }

      // avoid PHP issue "only variables can be returned as reference"
      $null = null;

      return $null;
   }

   /**
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

      foreach ($this->children as &$child) {
         if ($child instanceof TemplateTag) {
            return $child->getObjectId();
         }
      }

      return null;
   }

}

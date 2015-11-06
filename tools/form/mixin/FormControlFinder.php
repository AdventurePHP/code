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
namespace APF\tools\form\mixin;

use APF\core\pagecontroller\LanguageLabel;
use APF\core\pagecontroller\LanguageLabelTag;
use APF\tools\form\FormControl;
use APF\tools\form\FormElementGroup;
use APF\tools\form\FormException;
use APF\tools\form\HtmlForm;
use APF\tools\form\taglib\DynamicFormElementMarkerTag;
use APF\tools\form\taglib\HtmlFormTag;

/**
 * Implements methods of the <em>FormControlFinder</em> interface supporting <em>HtmlFormTag</em>
 * and <em>FormGroupTag</em> to find APF form controls within a given form.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 21.08.2014 (ID#198: Extracted common functionality to be re-used within a group control)<br />
 */
trait FormControlFinder {

   /**
    * @param string $id The ID of the desired form element.
    *
    * @return FormControl A reference on the form element.
    * @throws FormException In case the form element cannot be found.
    */
   public function &getFormElementByID($id) {

      if (count($this->children) > 0) {
         foreach ($this->children as &$child) {

            // when we directly find something - get it!
            if ($child->getAttribute('id') == $id) {
               return $child;
            }

            // facing a group, let's recurs into it!
            if ($child instanceof FormElementGroup) {
               try {
                  return $child->getFormElementByID($id);
               } catch (FormException $e) {
                  // nothing to do here, it's just a not found status...
               }
            }
         }
      }

      // display extended debug message in case no form element was found
      /* @var $form HtmlFormTag */
      if ($this instanceof HtmlForm) {
         $form = &$this;
      } else {
         $form = &$this->getForm();
      }
      $parent = &$form->getParentObject();
      $docCon = get_class($parent->getDocumentController());
      throw new FormException('[' . get_class($this) . '::getFormElementByID()] No form element with id "'
            . $id . '" composed in current form "' . $form->getAttribute('name')
            . '" in document controller "' . $docCon . '". Please double-check your taglib definitions '
            . 'within this form (especially attributes, that are used for referencing other form '
            . 'controls)!', E_USER_ERROR);
   }

   /**
    * @param string $markerName The desired marker's name.
    *
    * @return DynamicFormElementMarkerTag The marker.
    * @throws FormException In case the marker cannot be found.
    */
   public function &getMarker($markerName) {
      return $this->getFormElementByName($markerName);
   }

   /**
    * @param string $name The name of the desired form element.
    *
    * @return FormControl A reference on the form element.
    * @throws FormException In case the form element cannot be found.
    */
   public function &getFormElementByName($name) {

      if (count($this->children) > 0) {
         foreach ($this->children as &$child) {

            // when we directly find something - get it!
            if ($child->getAttribute('name') == $name) {
               return $child;
            }

            // facing a group, let's recurs into it!
            if ($child instanceof FormElementGroup) {
               try {
                  return $child->getFormElementByName($name);
               } catch (FormException $e) {
                  // nothing to do here, it's just a not found status...
               }
            }
         }
      }

      // display extended debug message in case no form element was found
      /* @var $form HtmlFormTag */
      if ($this instanceof HtmlForm) {
         $form = &$this;
      } else {
         $form = &$this->getForm();
      }
      $parent = &$form->getParentObject();
      $docCon = get_class($parent->getDocumentController());
      throw new FormException('[' . get_class($this) . '::getFormElementByName()] No form element with name "'
            . $name . '" composed in current form "' . $form->getAttribute('name')
            . '" in document controller "' . $docCon . '". Please double-check your taglib definitions '
            . 'within this form (especially attributes, that are used for referencing other form '
            . 'controls)!', E_USER_ERROR);
   }

   /**
    * @param string $name The name of the form label to return.
    *
    * @return LanguageLabelTag The instance of the desired label.
    * @throws FormException In case no label can be found.
    */
   public function &getLabel($name) {
      if (count($this->children) > 0) {
         foreach ($this->children as &$child) {

            // when we directly find something - get it!
            if ($child instanceof LanguageLabel) {
               if ($child->getAttribute('name') == $name) {
                  return $child;
               }
            }

            // facing a group, let's recurs into it!
            if ($child instanceof FormElementGroup) {
               try {
                  return $child->getLabel($name);
               } catch (FormException $e) {
                  // nothing to do here, it's just a not found status...
               }
            }
         }
      }

      // display extended debug message in case no form elements were found
      /* @var $form HtmlFormTag */
      if ($this instanceof HtmlForm) {
         $form = &$this;
      } else {
         $form = &$this->getForm();
      }
      $parent = &$form->getParentObject();
      $docCon = get_class($parent->getDocumentController());
      throw new FormException('[' . get_class($this) . '::getLabel()] No label found with name "' . $name
            . '" composed in form with name "' . $form->getAttribute('name') . '" for document controller "'
            . $docCon . '"!', E_USER_ERROR);
   }

   /**
    * @param string $name The name of the form elements to collect (e.g. for radio buttons).
    *
    * @return FormControl[] The list of form controls with the given name.
    */
   public function &getFormElementsByName($name) {
      $elements = [];
      if (count($this->children) > 0) {
         foreach ($this->children as &$child) {

            // when we directly find something - get it!
            if ($child->getAttribute('name') == $name) {
               $elements[] = &$child;
            }

            // facing a group, let's recurs into it!
            if ($child instanceof FormElementGroup) {
               $elements = array_merge($elements, $child->getFormElementsByName($name));
            }
         }
      }

      return $elements;
   }

   /**
    * @param string $tagName The tag name of the desired form element (e.g. "form:text").
    *
    * @return FormControl[] A list of references on the form elements.
    * @throws FormException In case the form element cannot be found or desired tag is not registered.
    */
   public function &getFormElementsByTagName($tagName) {

      /* @var $form HtmlFormTag */
      if ($this instanceof HtmlForm) {
         $form = &$this;
      } else {
         $form = &$this->getForm();
      }

      $tagClassName = $form->getTagClass($tagName);

      if (count($this->children) > 0) {

         $formElements = [];
         foreach ($this->children as &$child) {

            // when we directly find something - get it!
            if ($child instanceof $tagClassName) {
               $formElements[] = &$child;
            }

            // facing a group, let's recurs into it!
            if ($child instanceof FormElementGroup) {
               $formElements = array_merge($formElements, $child->getFormElementsByTagName($tagName));
            }
         }

         return $formElements;
      }

      // display extended debug message in case no form elements were found
      $parent = &$form->getParentObject();
      $docCon = get_class($parent->getDocumentController());
      throw new FormException('[' . get_class($this) . '::getFormElementsByType()] No form elements of type "&lt;'
            . $tagName . ' /&gt;" composed in ' . 'current form "' . $form->getAttribute('name') . '" in document controller "'
            . $docCon . '"!', E_USER_ERROR);
   }

}
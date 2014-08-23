<?php
namespace APF\tools\form;

use APF\core\pagecontroller\LanguageLabelTag;
use APF\tools\form\taglib\AbstractFormControl;
use APF\tools\form\taglib\DynamicFormElementMarkerTag;

/**
 * Defines methods to select form control(s) from a given form or form group element.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.08.2014<br />
 */
interface FormControlFinder {

   /**
    * Returns a reference on the form element identified by the given id.
    *
    * @param string $id The ID of the desired form element.
    *
    * @return AbstractFormControl A reference on the form element.
    * @throws FormException In case the form element cannot be found.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 21.01.2007<br />
    */
   public function &getFormElementByID($id);

   /**
    * Returns a list of form controls with the given name.
    *
    * @param string $name The name of the form elements to collect (e.g. for radio buttons).
    *
    * @return AbstractFormControl[] The list of form controls with the given name.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.08.2010<br />
    */
   public function &getFormElementsByName($name);

   /**
    * Returns a list of form elements addressed by their tag name.
    *
    * @param string $tagName The tag name of the desired form element (e.g. "form:text").
    *
    * @return AbstractFormControl[] A list of references on the form elements.
    * @throws FormException In case the form element cannot be found or desired tag is not registered.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 14.06.2008 (API change: do use this function instead of getFormElementsByType()!)<br />
    * Version 0.2, 12.12.2012 (Refactoring due to tag renaming)<br />
    */
   public function &getFormElementsByTagName($tagName);

   /**
    * Returns a reference on the desired marker or null.
    *
    * @param string $markerName The desired marker's name.
    *
    * @return DynamicFormElementMarkerTag The marker.
    * @throws FormException In case the marker cannot be found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.09.2008<br />
    */
   public function &getMarker($markerName);

   /**
    * Returns a reference on the form element identified by the given name.
    *
    * @param string $name The name of the desired form element.
    *
    * @return AbstractFormControl A reference on the form element.
    * @throws FormException In case the form element cannot be found.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 07.01.2007<br />
    * Version 0.2, 12.09.2009 (Corrected debug message)<br />
    */
   public function &getFormElementByName($name);

   /**
    * Let's you retrieve an &lt;form:getstring /&gt; tag instance with the specified name.
    *
    * @param string $name The name of the form label to return.
    *
    * @return LanguageLabelTag The instance of the desired label.
    * @throws FormException In case no label can be found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 17.01.2012<br />
    */
   public function &getLabel($name);

}
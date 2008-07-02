<?php
   /**
   *  @package core::pagecontroller
   *  @class core_taglib_setattribute
   *
   *  Bietet die M�glichkeit ein Attribut eines Documents in der Template-Datei zu setzen.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 04.04.2007<br />
   */
   class core_taglib_setattribute extends coreObject
   {

      function core_taglib_setproperty(){
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode "onAfterAppend" und setzt ein Attribut der Eltern-Klasse.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 04.04.2007<br />
      */
      function onAfterAppend(){
         $this->__ParentObject->setAttribute($this->__Attributes['name'],$this->__Attributes['value']);
       // end function
      }

    // end class
   }
?>
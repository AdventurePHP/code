<?php
   import('modules::alterationdate::biz','alterationDateManager');


   /**
   *  @package modules::alternationdate::pres
   *  @module alterationdate_v2_controller
   *
   *  Implementiert den DocumentController f�r das Template 'alternationdate.html'.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 17.03.2007<br />
   */
   class alterationdate_v2_controller extends baseController
   {

      function transformContent(){

         // Datum einsetzen
         $ADM = &$this->__getServiceObject('modules::aenderungsdatum::biz','alterationDateManager');
         $this->setPlaceHolder('Datum',$ADM->loadDate(trim($this->__Attributes['ConfigParam'])));

       // end function
      }

    // end class
   }
?>
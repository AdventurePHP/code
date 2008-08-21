<?php
   import('modules::comments::biz','commentManager');
   import('modules::comments::biz','ArticleComment');


   /**
   *  @package modules::comments::pres::documentcontroller
   *  @class commentBaseController
   *
   *  Implementiert den BaseController f�r die Kommentar-Funktion.<br />
   *
   *  @author Christian W. Sch�fer
   *  @version
   *  Version 0.1, 21.08.2007<br />
   */
   class commentBaseController extends baseController
   {

      /**
      *  @private
      *  Kategorie-Schl�ssel.
      */
      var $__CategoryKey;


      function commentBaseController(){
      }


      /**
      *  @private
      *
      *  L�d den CategoryKey vom Eltern-Objekt.<br />
      *
      *  @author Christian W. Sch�fer
      *  @version
      *  Version 0.1, 21.08.2007<br />
      */
      function __loadCategoryKey(){

        // AttributCategoryKey vom Parent holen
        $DocParent = &$this->__Document->getByReference('ParentObject');
        $CategoryKey = $DocParent->getAttribute('categorykey');

        if($CategoryKey == null){
           $this->__CategoryKey = 'standard';
         // end if
        }
        else{
           $this->__CategoryKey = $CategoryKey;
         // end else
        }

       // end function
      }

    // end function
   }
?>
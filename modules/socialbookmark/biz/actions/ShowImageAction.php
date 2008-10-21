<?php
   /**
   *  @package modules::socialbookmark::biz::actions
   *  @class ShowImageAction
   *
   *  Action für die Ausgabe der Icons.<br />
   *
   *  @author Christian W. Schäfer
   *  @version
   *  Version 0.1, 07.09.2007<br />
   */
   class ShowImageAction extends AbstractFrontcontrollerAction
   {

      function ShowImageAction(){
      }


      /**
      *  @public
      *
      *  Implementiert die Interface-Methode "run()" der AbstractFrontcontrollerAction.<br />
      *
      *  @author Christian W. Schäfer
      *  @version
      *  Version 0.1, 07.09.2007<br />
      */
      function run(){

         // Bild aus dem Input-Objekt beenden
         $Image = APPS__PATH.'/modules/socialbookmark/pres/image/'.$this->__Input->getAttribute('img').'.'.$this->__Input->getAttribute('imgext');

         // Header senden
         header('Content-type: image/'.$this->__Input->getAttribute('imgext'));
         header('Cache-Control: public');

         // Datei streamen
         readfile($Image);

         // Abarbeitung beenden
         exit();

       // end function
      }

    // end class
   }
?>
<?php
   /**
   *  @package tools::html::taglib
   *  @class ui_getstring
   *  @abstract
   *
   *  Implementiert die Basis für die TagLibs "<html:getstring />" und "<template:getstring />".<br />
   *  Mit diesen Tags wird ein definierter String aus einer Konfigurations-Datei in das Design eingesetzt.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 21.04.2006<br />
   */
   class ui_getstring extends Document
   {

      function ui_getstring(){
      }


      /**
      *  @public
      *
      *  Implementier die Abstrakte Methode "transform()" der Klasse coreObject.<br />
      *  Liest den gegenenen Config-String aus uns gibt diesen zurück.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 21.04.2006<br />
      */
      function transform(){

         // Timer starten
         $T = &Singleton::getInstance('benchmarkTimer');
         $ID = '('.get_class($this).') '.$this->__ObjectID.'::transform()';
         $T->start($ID);

         // Namespace auslesen
         if(!isset($this->__Attributes['namespace']) || empty($this->__Attributes['namespace'])){
            trigger_error('['.get_class($this).'->transform()] No attribute "namespace" given in tag definition!');
            $T->stop($ID);
            return (string)'';
          // end if
         }
         else{
            $Namespace = $this->__Attributes['namespace'];
          // end else
         }


         // Config auslesen
         if(!isset($this->__Attributes['config']) || empty($this->__Attributes['config'])){
            trigger_error('['.get_class($this).'->transform()] No attribute "config" given in tag definition!');
            $T->stop($ID);
            return (string)'';
          // end if
         }
         else{
            $Config = $this->__Attributes['config'];
          // end else
         }


         // Entry auslesen
         if(!isset($this->__Attributes['entry']) || empty($this->__Attributes['entry'])){
            trigger_error('['.get_class($this).'->transform()] No attribute "entry" given in tag definition!');
            $T->stop($ID);
            return (string)'';
          // end if
         }
         else{
            $Entry = $this->__Attributes['entry'];
          // end else
         }


         // Config holen
         $Config = &$this->__getConfiguration($Namespace,$Config);

         if($Config == null){
            $T->stop($ID);
            return (string)'';
          // end if
         }
         else{

            // Wert auslesen
            $Value = $Config->getValue($this->__Language,$Entry);

            if($Value == null){
               trigger_error('['.get_class($this).'->transform()] Given entry "'.$Entry.'" is not defined in section "'.$this->__Language.'" in configuration "'.$Config.'"!');
               $T->stop($ID);
               return (string)'';
             // end if
            }
            else{
               $T->stop($ID);
               return $Value;
             // end if
            }

          // end else
         }

       // end function
      }

    // end class
   }
?>
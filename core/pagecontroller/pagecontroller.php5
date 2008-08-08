<?php
   /**
   *  @file pagecontroller.php
   *
   *  Setups the framework's core environment. Initializes the Registry, that stores parameters,
   *  that are used within the complete framework. Among these are
   *
   *  - Environment  : environment, the application is executed in. The value is 'DEFAULT' in common
   *  - URLBasePath  : absolute url base path of the application (not really necessary)
   *  - URLRewriting : indicates, is url rewriting should be used
   *  - LogPath      : path, where logfiles are stored. The value is './logs' by default.
   *  - LibPath      : path, where the framework and your own libraries reside. This path can be used
   *                   to adress files with in the lib path directly (e.g. images or other ressources)
   *
   *  The file also contains the pagecontroller core implementation with the classes Page,
   *  Document, TagLib, coreObject, xmlParser and baseController (the basic MVC document controller).
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 20.06.2008<br />
   *  Version 0.2, 16.07.2008 (added the LibPath to the registry namespace apf::core)
   *  Version 0.3, 07.08.2008 (Made LibPath readonly)<br />
   */

   /////////////////////////////////////////////////////////////////////////////////////////////////
   // Define the internally used base path for the adventure php framework libraries.             //
   /////////////////////////////////////////////////////////////////////////////////////////////////

   // get current path
   $Path = explode('/',str_replace('\\','/',dirname(__FILE__)));

   // get relevant segments
   $count = count($Path);
   $AppsPath = array();
   for($i = 0; $i < $count; $i++){

      if($Path[$i] != 'core'){
         $AppsPath[] = $Path[$i];
       // end if
      }
      else{
         break;
       // end else
      }

    // end for
   }

   // define the APPS__PATH constand
   define('APPS__PATH',implode($AppsPath,'/'));

   /////////////////////////////////////////////////////////////////////////////////////////////////


   // include necessary core libraries for the pagecontroller
   import('core::errorhandler','errorHandler');
   import('core::singleton','Singleton');
   import('core::registry','Registry');
   import('core::service','serviceManager');
   import('core::configuration','configurationManager');
   import('core::benchmark','benchmarkTimer');
   import('core::filter','filterFactory');


   // define base parameters of the framework's core and tools layer
   $Reg = &Singleton::getInstance('Registry');
   $Reg->register('apf::core','Environment','DEFAULT');
   $Reg->register('apf::core','URLRewriting',false);
   $Reg->register('apf::core','LogDir','./logs');
   $Reg->register('apf::core','URLBasePath',$_SERVER['HTTP_HOST']);
   $Reg->register('apf::core','LibPath',APPS__PATH,true);


   /**
   *  @package core::pagecontroller
   *
   *  Importiert Klassen und Module aus einem angegebenem Namespace. Ist bei aktiviertem<br />
   *  PHP-5-Support eine Datei mit der Endung ".php5" vorhanden wird diese gezogen. Befindet<br />
   *  sich keine Datei im angegebenen Namespace wird ein Fallback auf die Endung ".php" initiiert<br />
   *  damit nicht alle Dateien, in denen keine Anpassungen f�r PHP 5 vorgenommen werden m�ssen<br />
   *  auf dem Filesystem vorgehalten sein m�ssen.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 03.12.2005<br />
   *  Version 0.2, 14.04.2006<br />
   *  Version 0.3, 14.01.2007 (Propriet�ren Support f�r PHP 5 implementiert)<br />
   *  Version 0.4, 03.03.2007 (Sch�nheitskorrekturen am Code)<br />
   *  Version 0.5, 03.03.2007 (Support f�r den Betrieb unter PHP 4 und PHP 5 hinzugef�gt)<br />
   *  Version 0.6, 24.03.2008 (Aus Performance-Gr�nden Cache f�r bereits inportierte Dateien eingef�hrt)<br />
   *  Version 0.7, 20.06.2008 (Wegen Einf�hrung Registry in den pagecontroller verlagert)<br />
   */
   function import($Namespace,$File,$ActivatePHP5Support = true){

      // Dateinamen zusammenbauen
      $File = APPS__PATH.'/'.str_replace('::','/',$Namespace).'/'.$File;

      // Pr�fen, ob Datei bereits inkludiert ist
      if(isset($GLOBALS['IMPORT_CACHE'][$File])){
         return true;
       // end if
      }
      else{
         $GLOBALS['IMPORT_CACHE'][$File] = true;
       // end else
      }

      // Datei importieren
      if(intval(phpversion()) == 5 && $ActivatePHP5Support == true){

         // Dateiendung anh�ngen
         $ImportFile = $File.'.php5';

         // Datei importieren
         if(!file_exists($ImportFile)){

            // php5-File annehmen
            $ImportFile = $File.'.php';

            if(!file_exists($ImportFile)){
               trigger_error('[import()] The given module ('.$ImportFile.') cannot be loaded!');
               exit();
             // end if
            }
            else{
               include_once($ImportFile);
             // end else
            }

          // end if
         }
         else{
            include_once($ImportFile);
          // end else
         }

       // end if
      }
      else{

         // Dateiendung anh�ngen
         $ImportFile = $File.'.php';

         // Datei importieren
         if(!file_exists($ImportFile)){
            trigger_error('[import()] The given module ('.$ImportFile.') cannot be loaded!');
            exit();
          // end if
         }
         else{
            include_once($ImportFile);
          // end else
         }

       // end else
      }

    // end function
   }


   /**
   *  @package core::applicationmanager
   *
   *  Erzeugt die print_r()-Ausgabe eines �bergebenen Objekts und gibt diese zur�ck.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 04.02.3006<br />
   *  Version 0.2, 23.04.2006 (Ausgabe wird nun in einem Puffer �bergeben)<br />
   */
   function printObject($o,$transformhtml = false){

      $buffer = (string)'';
      $buffer .= "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
      $buffer .= "<br />\n";
      $buffer .= "<strong>\n";
      $buffer .= "Output of printObject():\n";
      $buffer .= "</strong>\n";
      $buffer .= "<br />\n";
      $buffer .= "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
      $buffer .= "\n<pre>";

      if($transformhtml == true){
         $buffer .= htmlentities(print_R($o,true));
       // end if
      }
      else{
         $buffer .= print_R($o,true);
       // end else
      }

      $buffer .= "</pre>\n";
      $buffer .= "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
      $buffer .= "<br />\n";
      $buffer .= "<br />\n";

      return $buffer;

    // end function
   }


   /**
   *  @package core::pagecontroller
   *  @class xmlParser
   *  @static
   *
   *  Statischer Parser f�r XML-Strings.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 22.12.2006<br />
   */
   class xmlParser
   {

      function xmlParser(){
      }


      /**
      *  @public
      *  @static
      *
      *  Extrahiert die Attribute eines Tags aus einem XML-String.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 22.12.2006<br />
      *  Version 0.2, 30.12.2006 (Fehler beim Parsen von Tags ohne Attribute behoben (setzen von $tagAttributeDel))<br />
      *  Version 0.3, 03.01.2007 (Fehler beim Bestimmen des Attribut-Strings behoben)<br />
      *  Version 0.4, 13.01.2007 (Fehlerausgabe beim Parse-Fehler verbessert)<br />
      *  Version 0.5, 16.11.2007 (Fehler bei Fehlerausgabe von Tags verbessert)<br />
      */
      static function getTagAttributes($TagString){

         // Trennzeichen von Taglib und Klasse suchen
         $tagAttributeDel = strpos($TagString, ' ');


         // Den Tag schlie�endes Zeichen suchen
         $posTagClosingSign = strpos($TagString,'>');


         // Falls Trennposition zwischen Tag und Attributen nicht gefunden wurden, oder das
         // TagEnde-Zeichen vor dem Delimiter zwischen Tag und Attributen liegt, wird
         // das "Ende-Zeichen" ('>') als Trennzeichen gesetzt. So k�nnen Tags ohne
         // Attribute erlaubt werden.
         if($tagAttributeDel === false || $tagAttributeDel > $posTagClosingSign){
            //OLD: Machte Fehler beim Parsen von Tags ohne Attribute
            //$tagAttributeDel = strlen($TagString);
            $tagAttributeDel = strpos($TagString,'>');
          // end if
         }


         // Position des Trennzeichens zwischen Taglib und Klasse suchen
         $prefixDel = strpos($TagString,':');


         // Klasse extrahieren
         $class = substr($TagString,$prefixDel + 1,$tagAttributeDel - ($prefixDel +1));


         // Taglib extrahieren
         $prefix = substr($TagString,1,$prefixDel - 1);


         // Position der ersten schlie�enden Klammer nach dem Attribut-String finden
         $posEndAttrib = strpos($TagString,'>');


         // Restlichen String als Attributstring extrahieren
         // OLD: Machte Fehler, falls ein XML-Tag direkt im Anschluss an das Tag folgte
         //$attributesString = substr($TagString,$tagAttributeDel + 1,$posEndAttrib);
         $attributesString = substr($TagString,$tagAttributeDel + 1,$posEndAttrib - $tagAttributeDel);


         // Attribute des Strings auslesen
         $attributes = xmlParser::getAttributesFromString($attributesString);


         // Pr�fen ob Tag selbstschlie�end. Falls nicht Content einlesen
         if(substr($TagString,$posEndAttrib - 1,1) == '/'){
            $content = (string)'';
          // end if
         }
         else{

            // Content-Variable initialisieren
            $content = (string)'';

            // Pr�fen ob schlie�ender Tag vorhanden ist
            if(strpos($TagString,'</'.$prefix.':'.$class.'>') === false){
               trigger_error('[xmlParser::getTagAttributes()] No closing tag found for tag "&lt;'.$prefix.':'.$class.' /&gt;"! Tag string: "'.htmlentities($TagString).'".',E_USER_ERROR);
             // end if
            }
            else{

               // Ben�tigte Variablen initialisieren
               $found = true;
               $offset = 0;
               $posEndContent = 0;
               $Count = 0;
               $MaxCount = 10;
               $endTag = '</'.$prefix.':'.$class.'>';

               while($found == true){

                  // Alten Wert aufbewahren
                  $posEndContent = $offset;

                  // Neue Position evaluieren
                  $offset = strpos($TagString,$endTag,$offset + 1);

                  // Falls keine weitere Version gefunden -> aussteigen
                  if($offset === false){
                     $found = false;
                   // end if
                  }

                  // Falls mehr als $MaxCount Stellen gefunden -> aus Sicherheitsgr�nden aussteigen
                  if($Count > $MaxCount){
                     $found = false;
                   // end if
                  }

                  // Count erh�hen
                  $Count++;

                // end while
               }

               // Content des Tags extrahieren
               $content = substr($TagString,$posEndAttrib + 1,($posEndContent - $posEndAttrib) - 1);

             // end else
            }

          // end else
         }


         // Return-Array definieren
         $Attributes = array ();
         $Attributes['attributes'] = $attributes;
         $Attributes['class'] = $class;
         $Attributes['prefix'] = $prefix;
         $Attributes['content'] = $content;


         // Werte zur�ckgeben
         return $Attributes;

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Extrahiert die XML-Attribute aus einem String. Gibt ein Array der Form<br />
      *  <pre>
      *    $Array['ATTRIBUTE_NAME'] = 'ATTRIBUTE_VALUE';
      *  </pre>
      *  zur�ck.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 22.12.2006<br />
      *  Version 0.2, 30.12.2006 (Dokumentation erweitert)<br />
      *  Version 0.3, 14.01.2007 (Fehlermeldung verbessert)<br />
      *  Version 0.4, 14.11.2007 ($hasFound entfernt; siehe http://forum.adventure-php-framework.org/de/viewtopic.php?t=7)<br />
      */
      static function getAttributesFromString($attributesString){

         $Attributes = array ();
         $foundAtr = true;
         $Offset = 0;

         $ParserLoops = 0;
         $ParserMaxLoops = 20;


         // Attribute iterativ suchen
         while(true){

            // Parser-Durchl�ufe inkrementieren
            $ParserLoops++;


            // Pr�fen, om Maximum an Parser-Durchl�ufen schon erreicht ist
            if($ParserLoops == $ParserMaxLoops){
               trigger_error('[xmlParser::getAttributesFromString()] Error while parsing: "'.htmlentities($attributesString).'". Maximum number of loops exceeded!',E_USER_ERROR);
             // end if
            }


            // Attribute auslesen
            $foundAtr = strpos($attributesString, '=', $Offset);


            // Falls kein Attribut mehr gefunden wurde -> aussteigen
            if($foundAtr === false){
                break;
             // end if
            }


            // Werte auslesen
            $key = substr($attributesString, $Offset, $foundAtr - $Offset);
            $attrValueStart = strpos($attributesString, '"', $foundAtr);
            $attrValueStart++;
            $attrValueEnd = strpos($attributesString, '"', $attrValueStart);
            $attrValue = substr($attributesString, $attrValueStart, $attrValueEnd - $attrValueStart);
            $Offset = $attrValueEnd + 1;


            // Array mit Key => Value aufbauen
            $Attributes[trim($key)] = trim($attrValue);

          // end while
         }

         return $Attributes;

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Erzeugt eine eindeutige ID.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 22.12.2006<br />
      */
      static function generateUniqID($md5 = true){

         if($md5 == true){
            return md5(uniqid(rand(),true));
          // end if
         }
         else{
            return uniqid(rand(),true);
          // end else
         }

       // end function
      }

    // end class
   }


   /**
   *  @package core::pagecontroller
   *  @class coreObject
   *  @abstract
   *
   *  Repr�sentiert das Basis-Objekt aller weiteren Objekte des Page-Controllers<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 28.12.2006<br />
   *  Version 0.2, 11.02.2007 (Attribute Language und Context hinzugef�gt)<br />
   */
   class coreObject
   {

      /**
      *  @private
      *  Eindeutige ID des Objekts.
      */
      var $__ObjectID;

      /**
      *  @private
      *  Referenz auf das Eltern-Objekt.
      */
      var $__ParentObject;

      /**
      *  @private
      *  Kinder eines Objekts.
      */
      var $__Children;

      /**
      *  @private
      *  Attribute eines Objekts, die aus einem XML-Tag gelesen werden.
      */
      var $__Attributes;


      /**
      *  @private
      *  Kontext eines Objekts oder einer Applikation.
      */
      var $__Context;


      /**
      *  @private
      *  Sprache eines Objekts oder einer Applikation.
      */
      var $__Language = 'de';


      /**
      *  @public
      *
      *  Konstruktor des abstrakten Basis-Objekts.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function coreObject(){
      }


      /**
      *  @public
      *
      *  Abstrakte get()-Methode.<br />
      *
      *  @param string $Attribut; Name des Member-Attributes, dessen Wert zur�ckgegeben werden soll.
      *  @return void $this->{'__'.$Attribut}; Gibt das addressierte Member-Attribut zur�ck, oder null im Fehlerfall.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 02.02.2007 (Behandlung, falls Attribut nicht existiert hinzugef�gt)<br />
      */
      function get($Attribute){

         if(isset($this->{'__'.$Attribute})){
            return $this->{'__'.$Attribute};
          // end if
         }
         else{
            return null;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Abstrakte set()-Methode.<br />
      *
      *  @param string $Attribut; Name des Member-Attributes, dessen Wert gesetzt werden soll.
      *  @param void $Value; Wert des Member-Attributes, dessen Wert gesetzt werden soll.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function set($Attribute,$Value){
         $this->{'__'.$Attribute} = $Value;
       // end function
      }


      /**
      *  @public
      *
      *  Abstrakte add()-Methode. H�ngt ein weiteres Element an eine Liste an.<br />
      *
      *  @param string $Attribut; Name des Member-Attributes, dessen Wert gesetzt werden soll.
      *  @param void $Value; Wertdes Member-Attributes, dessen Wert gesetzt werden soll.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 11.11.2007<br />
      */
      function add($Attribute,$Value){
         $this->{'__'.$Attribute}[] = $Value;
       // end function
      }


      /**
      *  @public
      *
      *  Methode um ein Attribut aus einem von coreObject erbenden Objekts auszulesen.<br />
      *
      *  @param string $Name; Name des Attributes, dessen Wert zur�ckgeliefert werden soll.
      *  @return void $this->__Attributes[$Name]; Gibt das addressierte Attribut zur�ck, oder null im Fehlerfall.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 02.02.2007 (Behandlung, falls Attribut nicht existiert hinzugef�gt)<br />
      */
      function getAttribute($Name){

         if(isset($this->__Attributes[$Name])){
            return $this->__Attributes[$Name];
          // end if
         }
         else{
            return null;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Methode um ein Attribut aus einem von coreObject erbenden Objekts auszulesen.<br />
      *
      *  @param string $Name; Name des Attributes, dessen Wert zur�ckgeliefert werden soll.
      *  @param void $Value; Wert des Attributes, dessen Wert gesetzt werden soll.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function setAttribute($Name,$Value){
         $this->__Attributes[$Name] = $Value;
       // end function
      }


      /**
      *  @public
      *
      *  Methode um das Attribut-Array aus eines Objekts auslesen zu k�nnen.<br />
      *
      *  @return array $this->__Attributes; Gibt das Attributes-Array zur�ck
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function getAttributes(){
         return $this->__Attributes;
       // end function
      }


      /**
      *  @public
      *
      *  L�scht ein Attribut.<br />
      *
      *  @param string $Name; Name des zu l�schenden Attributes.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function deleteAttribute($Name){
         unset($this->__Attributes[$Name]);
       // end function
      }


      /**
      *  @public
      *
      *  Methode um das Attribut-Array aus eines Objekts auslesen zu k�nnen.<br />
      *
      *  @param array $Attributes; Attribut-Array
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function setAttributes($Attributes = array()){

         if(is_array($Attributes) && count($Attributes) > 0){

            if(!is_array($this->__Attributes)){
               $this->__Attributes = array();
             // end if
            }

            $this->__Attributes = array_merge($this->__Attributes,$Attributes);

          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Setzt einen Member per �bergabe einer Referenz auf ein Objekt.<br />
      *
      *  @param string $Attribute; Names des Attributes
      *  @parem object $Object; Referenz auf ein Objekt
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 16.01.2007<br />
      */
      function setByReference($Attribute,&$Value){
         $this->{'__'.$Attribute} = & $Value;
       // end function
      }


      /**
      *  @public
      *
      *  Holt sich die Referenz einer Member-Variable eines Objekts.<br />
      *
      *  @param string $Attribute; Names des Attributes
      *  @return object $Object; Referenz auf ein Objekt, oder null im Fehlerfall.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 16.01.2007<br />
      *  Version 0.2, 21.01.2007 (Bugfix: $Attribute war falsch geschreiben)<br />
      */
      function &getByReference($Attribute){

         if(isset($this->{'__'.$Attribute})){
            return $this->{'__'.$Attribute};
          // end if
         }
         else{
            $return = null;
            return $return;
          // end else
         }

       // end function
      }


      /**
      *  Abstrakte Methode transform(). Methode wird vom PageController zur Transformation eines Knotens aufgerufen. Muss von der erbenden Klasse implementiert werden.<br />
      *
      *  @public
      *  @abstract
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function transform(){
      }


      /**
      *  Abstrakte Methode init(). Methode, die zur Initialisierung einer Komponente vom ServiceManager aufgerufen wird. Muss von der erbenden Klasse implementiert werden.<br />
      *
      *  @public
      *  @abstract
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 30.03.2007<br />
      */
      function init(){
      }


      /**
      *  Abstrakte Methode onParseTime(). Wird nach dem Erstellen des DOM-Knotens auf diesen aufgerufen. Muss von der erbenden Klasse implementiert werden.<br />
      *
      *  @public
      *  @abstract
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function onParseTime(){
      }


      /**
      *  Abstrakte Methode onAfterAppend(). Wird nach dem Einh�ngen im Baum auf das Objekt aufgerufen. Muss von der erbenden Klasse implementiert werden.<br />
      *
      *  @public
      *  @abstract
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function onAfterAppend(){
      }


      /**
      *  Abstrakte Methode transformContent(). Muss von der erbenden DocumentController-Klasse implementiert werden.<br />
      *
      *  @public
      *  @abstract
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function transformContent(){
      }


      /**
      *  @private
      *
      *  Gibt ein Service-Object gem�� dem eigenen Context zur�ck.<br />
      *
      *  @param string $Namespace; Namespace des Moduls / der Konfiguration
      *  @param string $ServiceName; Name des Service Objekts
      *  @param string $Type; Typ der Initialisierung des ServiceObjekts
      *  @return object $ServiceObject; ServiceObject
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 07.03.2007<br />
      *  Version 0.2, 08.03.2007 (Context wird nun aus dem aktuellen Objekt gezogen)<br />
      *  Version 0.3, 10.03.2007 (Methode ist nun private)<br />
      *  Version 0.4, 22.04.2007 (Um �bergabe der Sprache erweitert)<br />
      *  Version 0.5, 24.02.2008 (Um weiteren Parameter $Type erweitert)<br />
      */
      function &__getServiceObject($Namespace,$ServiceName,$Type = 'SINGLETON'){

         // ServiceManager holen
         $serviceManager = &Singleton::getInstance('serviceManager');

         // Eigenen Context beim ServiceManager bekannt machen
         $serviceManager->setContext($this->__Context);

         // Sprache beim ServiceManager bekannt machen
         $serviceManager->setLanguage($this->__Language);

         // ServiceObject zur�ckgeben
         return $serviceManager->getServiceObject($Namespace,$ServiceName,$Type);

       // end function
      }


      /**
      *  @private
      *
      *  Gibt ein initialisiertes Service-Object gem�� dem eigenen Context zur�ck.<br />
      *
      *  @param string $Namespace; Namespace des Moduls / der Konfiguration
      *  @param string $ServiceName; Name des Service Objekts
      *  @param string $InitParam; Initialisierungs-Parameter
      *  @param string $Type; Typ der Initialisierung des ServiceObjekts*
      *  @return object $ServiceObject; ServiceObject
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 29.03.2007<br />
      *  Version 0.2, 22.04.2007 (Um �bergabe der Sprache erweitert)<br />
      *  Version 0.3, 24.02.2008 (Um weiteren Parameter $Type erweitert)<br />
      */
      function &__getAndInitServiceObject($Namespace,$ServiceName,$InitParam,$Type = 'SINGLETON'){

         // ServiceManager holen
         $serviceManager = &Singleton::getInstance('serviceManager');

         // Eigenen Context beim ServiceManager bekannt machen
         $serviceManager->setContext($this->__Context);

         // Sprache beim ServiceManager bekannt machen
         $serviceManager->setLanguage($this->__Language);

         // ServiceObject zur�ckgeben
         return $serviceManager->getAndInitServiceObject($Namespace,$ServiceName,$InitParam,$Type);

       // end function
      }


      /**
      *  @private
      *
      *  Gibt ein Configuration-Object gem�� den �bergebenen Parametern zur�ck.<br />
      *
      *  @param string $Namespace; Namespace des Moduls / der Konfiguration
      *  @param string $ConfigName; Name der Konfiguration
      *  @return object $Configuration; Konfigurations-Objekt
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 07.03.2007<br />
      *  Version 0.2, 08.03.2007 (Context wird nun aus dem aktuellen Objekt gezogen)<br />
      *  Version 0.3, 10.03.2007 (Methode ist nun private)<br />
      */
      function &__getConfiguration($Namespace,$ConfigName){

         // configurationManager holen
         $configurationManager = &Singleton::getInstance('configurationManager');

         // Configuration Objekt zur�ckgeben
         return $configurationManager->getConfiguration($Namespace,$this->__Context,$ConfigName);

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt einen Attribut-String an Hand eines Attribut-Arrays. Im ExcludeArray enthaltene Attribute<br />
      *  werden nicht beachtet.
      *
      *  @param array $AttributesArray; Array der Attribute
      *  @param array $ExclusionArray; Array mit Attributen, die ignoriert werden sollen
      *  @return string $AttributesString; HTML-Attribut-String, oder Leerstring, falls keine Attribute vorhanden sind
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 05.01.2007<br />
      *  Version 0.2, 07.01.2007 ($ExclusionArray-Verhalten hinzugef�gt)<br />
      *  Version 0.3, 02.06.2007 (Rechtschreibkorrektur und von "ui_element" nach "coreObject" verschoben)<br />
      */
      function __getAttributesAsString($AttributesArray,$ExclusionArray = array()){

         if(count($AttributesArray) > 0){

            // Attribute-Array initialisieren
            $Attributes = array();

            foreach($AttributesArray as $Offset => $Value){

               if(!in_array($Offset,$ExclusionArray)){
                  $Attributes[] = $Offset.'="'.$Value.'"';
                // end if
               }

             // end foreach
            }

            return implode(' ',$Attributes);

          // end if
         }
         else{
            return (string)'';
          // end else
         }

       // end function
      }

    // end class
   }


   /**
   *  @package core::pagecontroller
   *  @class TagLib
   *
   *  Repr�sentiert eine Tag-Library.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 28.12.2006<br />
   */
   class TagLib extends coreObject
   {

      /**
      *  @private
      */
      var $__Namespace;

      /**
      *  @private
      */
      var $__Prefix;

      /**
      *  @private
      */
      var $__Class;


      /**
      *  @public
      *
      *  Konstruktor einer Tag-Lib.<br />
      *
      *  @param string $Namespace; Namespace der Tag-Library
      *  @param string $Prefix; Tag-Prefix der Tag-Library
      *  @param string $Class; XML-Klasse der Tag-Library
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function TagLib($Namespace,$Prefix,$Class){

         $this->__Namespace = $Namespace;
         $this->__Class = $Class;
         $this->__Prefix = $Prefix;

       // end function
      }

    // end class
   }


   /**
   *  @package core::pagecontroller
   *  @class Page
   *
   *  Repr�sentiert eine Webseite. Bildet den root-Knoten derselben.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 28.12.2006<br />
   *  Version 0.2, 03.01.2007 (URL-Rewriting eingef�hrt)<br />
   *  Version 0.3, 08.06.2007 (URL-Rewriting in Filer ausgelagert, "__rewriteRequestURI()" entfernt)<br />
   */
   class Page extends coreObject
   {

      /**
      *  @private
      *  Name der "Page".
      */
      var $__Name;


      /**
      *  @private
      *  Komponiert das initiale "Document" einer "Page".
      */
      var $__Document;


      /**
      *  @private
      *  Speichert, ob URL-Rewriting aktiviert ist.
      */
      var $__URLRewrite;


      /**
      *  @public
      *
      *  Konstruktor der "Page".<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 03.01.2007 (URL-Rewriting eingef�hrt)<br />
      *  Version 0.3, 08.06.2007 (URL-Rewriting in Filter ausgelagert)<br />
      *  Version 0.4, 20.06.2008 (Registry f�r "APPS__URL_REWRITING" eingef�hrt)<br />
      */
      function Page($Name = '',$URLRewrite = null){

         // set URLRewrite
         if($URLRewrite === null){
            $Reg = &Singleton::getInstance('Registry');
            $this->__URLRewrite = $Reg->retrieve('apf::core','URLRewriting');
          // end if
         }
         else{
            $this->__URLRewrite = $URLRewrite;
          // end else
         }

         // Attribute setzen
         $this->__Name = $Name;
         $this->__ObjectID = xmlParser::generateUniqID();

         // GET-URI rewriten, wenn erw�nscht
         if($this->__URLRewrite == true){
            $pCF = filterFactory::getFilter('core::filter','pagecontrollerRewriteRequestFilter');
            $pCF->filter();
          // end if
         }
         else{
            $sRF = filterFactory::getFilter('core::filter','standardRequestFilter');
            $sRF->filter();
          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Erzeugt das initiale "Document" einer "Page" und l�d das initiale Template.<br />
      *
      *  @param string $Namespace; Namespace des initialen Templates
      *  @param string $Design; Name des initialen Designs
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 31.01.2007 (Context des Documents wird nun gesetzt)<br />
      *  Version 0.3, 04.03.2007 (Namespace wird als Context verwendet, falls kein Context vorhanden)<br />
      *  Version 0.4, 22.04.2007 (Sprache der Page wird nun in das Document �bernommen)<br />
      */
      function loadDesign($Namespace,$Design){

         $this->__Document = new Document();

         // Context setzen
         if(empty($this->__Context)){
            $this->__Document->set('Context',$Namespace);
          // end if
         }
         else{
            $this->__Document->set('Context',$this->__Context);
          // end else
         }

         // Sprache setzen
         $this->__Document->set('Language',$this->__Language);

         // Design laden
         $this->__Document->loadDesign($Namespace,$Design);
         $this->__Document->set('ObjectID',xmlParser::generateUniqID());
         $this->__Document->__ParentObject = & $this;

       // end function
      }


      /**
      *  @public
      *
      *  Transformiert den Objektbaum der Webseite und gibt Erzeugt das initiale "Document" einer "Page" und l�d das initiale Template.<br />
      *
      *  @param string $Namespace; Namespace des initialen Templates
      *  @param string $Design; Name des initialen Designs
      *  @return string $this->__Document->transform(); Gibt den XML-String der Seite zur�ck.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 03.01.2007 (URL-Rewriting eingef�hrt)<br />
      *  Version 0.3, 08.06.2007 (URL-Rewriting in Filter ausgelagert)<br />
      */
      function transform(){

         // Dokument transformieren
         $Content = $this->__Document->transform();

         // Links rewriten, wenn erw�nscht
         if($this->__URLRewrite == true){
            $hURF = filterFactory::getFilter('core::filter','htmlLinkRewriteFilter');
            $Content = $hURF->filter($Content);
          // end if
         }

         // HTML-Quelltext zur�ckgeben
         return $Content;

       // end function
      }

    // end class
   }


   /**
   *  @package core::pagecontroller
   *  @class Document
   *
   *  Repr�sentiert ein Dokument innerhalb einer HTML-Seite oder eines Dokuments.<br />
   *  Kann sich selbst wieder komponieren.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 28.12.2006<br />
   */
   class Document extends coreObject
   {

      /**
      *  @private
      */
      var $__Content;

      /**
      *  @private
      */
      var $__DocumentController;

      /**
      *  @private
      */
      var $__TagLibs;

      /**
      *  @private
      */
      var $__MaxLoops = 100;


      /**
      *  @public
      *
      *  Konstruktor des Objekts. Initialisiert Standard-TagLibs f�r den Aufbau der HTML-Seite.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 03.03.2007 ("&" vor dem "new" entfernt)<br />
      */
      function Document(){

         // Objekt-ID setzen
         $this->__ObjectID = xmlParser::generateUniqID();

         // Standard-TagLibs setzen
         $this->__TagLibs[] = new TagLib('core::pagecontroller','core','addtaglib');
         $this->__TagLibs[] = new TagLib('core::pagecontroller','core','importdesign');
         $this->__TagLibs[] = new TagLib('core::pagecontroller','html','template');
         $this->__TagLibs[] = new TagLib('core::pagecontroller','html','placeholder');

       // end function
      }


      /**
      *  @public
      *
      *  Methode zum Hinzuf�gen weitere Tag-Libs zu einem Dokument.<br />
      *
      *  @param string $Namespace; Namespace der Tag-Library
      *  @param string $Prefix; Tag-Prefix der Tag-Library
      *  @param string $Class; XML-Klasse der Tag-Library
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 03.03.2007 ("&" vor dem "new" entfernt)<br />
      */
      function addTagLib($Namespace,$Prefix,$Class){

         // TagLib-Objekt erzeugen
         $this->__TagLibs[] = new TagLib($Namespace,$Prefix,$Class);

         // Klasse importieren
         $ModuleName = $this->__getModuleName($Prefix,$Class);
         if(!class_exists($ModuleName)){
            import($Namespace,$ModuleName);
          // end if
         }

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt den Modul-Namen (=Klassen-Namen) einer Klasse, die durch eine TagLib eingebunden wurde.<br />
      *
      *  @param string $Prefix; Tag-Prefix der Tag-Library
      *  @param string $Class; XML-Klasse der Tag-Library
      *  @return string $ModuleName; Liefert den Modul-Namen zur�ck
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function __getModuleName($Prefix,$Class){
         return $Prefix.'_taglib_'.$Class;
       // end function
      }


      /**
      *  @public
      *
      *  L�d das initiale Template. Wird nur vom Objekt "Page" aufgerufen.<br />
      *
      *  @param string $Namespace; Namespace des initialen Templates
      *  @param string $Design; Name des initialen Designs
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 15.01.2007 (DocumentController werden nun zuerst extrahiert)<br />
      */
      function loadDesign($Namespace,$Design){

         // Content einlesen
         $this->__loadContentFromFile($Namespace,$Design);

         // DocumentController suchen
         $this->__extractDocumentController();

         // XML-Tags im Content parsen
         $this->__extractTagLibTags();

       // end function
      }


      /**
      *  @private
      *
      *  L�d ein Template "$Design" aus einem angegebenen Namespace.<br />
      *
      *  @param string $Namespace; Namespace des initialen Templates
      *  @param string $Design; Name des initialen Designs
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 01.01.2007 (Fehler behoben, dass Templates nicht sauber geladen wurden)<br />
      */
      function __loadContentFromFile($Namespace,$Design){

         $File = APPS__PATH.'/'.str_replace('::','/',$Namespace).'/'.$Design.'.html';

         if(!file_exists($File)){
            trigger_error('[Document::__loadContentFromFile()] Design "'.$Design.'" not existent in namespace "'.$Namespace.'"!',E_USER_ERROR);
            exit();
          // end if
         }
         else{
            $this->__Content = file_get_contents($File);
          // end else
         }

       // end function
      }


      /**
      *  @private
      *
      *  XML-Parser-Methode, die die registrierten TagLib-Tags aus dem Content extrahiert und den<br />
      *  Objektbaum aufbaut. Dabei werden f�r die jeweiligen Tags eigene Child-Objekte im "Document"<br />
      *  erzeugt und die Stellen mit einem Merker-Tag versehen, die bei der Transformation dann wieder<br />
      *  durch ihre Inhalts-Entsprechungen ersetzt werden.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 21.01.2007 (Bugfix: Parser �bersah erneut �ffnende Tokens im TagString, bei Mischungen aus selbst- und exklusiv-schlie�enden Tags)<br />
      *  Version 0.3, 31.01.2007 (Kontext-Behandlung hinzugef�gt)<br />
      *  Version 0.4, 09.04.2007 (Doppeltes Setzen der Attributes bereinigt, Language-Behandlung hinzugef�gt)<br />
      *  Version 0.5, 02.04.2008 (Bug behoben, dass Token nicht in der Fehlermeldung angezeigt wird)<br />
      */
      function __extractTagLibTags(){

         // Kopie des aktuellen Content erzeugen
         $Content = $this->__Content;


         // Hilfsvariable f�r Parser-Durchl�ufe (Extraktion der TagLib-Tags) initialiserien
         $TagLibLoops = 0;


         // Laufvariable initialisieren
         $i = 0;


         // TagLibs parsen. Hier wird ein while verwendet, da im Parser-Lauf auch weitere Tag-Libs
         // hinzukommen k�nnen. Siehe hierzu Klasse core_taglib_addTagLib!
         while($i < count($this->__TagLibs)){

            // Falls Parserl�ufe zu viel werden -> Fehler!
            if($TagLibLoops > $this->__MaxLoops){
               trigger_error('[Document::__extractTagLibTags()] Maximum numbers of parsing loops reached!',E_USER_ERROR);
               exit();
             // end if
            }


            // Attribute f�r Parser-Lauf initialisieren
            $Prefix = $this->__TagLibs[$i]->get('Prefix');
            $Class = $this->__TagLibs[$i]->get('Class');
            $Module = $this->__getModuleName($Prefix, $Class);
            $Token = $Prefix.':'.$Class;
            $TagLoops = 0;


            // TagLib-Tags suchen
            while(substr_count($Content,'<'.$Token) > 0){

               // Falls Parser-Durchl�ufe zu viele werden -> Fehler
               if($TagLoops > $this->__MaxLoops){
                  trigger_error('['.get_class($this).'::__extractTagLibTags()] Maximum numbers of parsing loops reached!',E_USER_ERROR);
                  exit();
                // end if
               }


               // Eindeutige ID holen
               $ObjectID = xmlParser::generateUniqID();


               // Start- und End-Position des Tags im Content finden.
               // Als End-Position wir immer ein schlie�ender Tag erwartet
               $TagStartPos = strpos($Content,'<'.$Token);
               $TagEndPos = strpos($Content,'</'.$Token.'>',$TagStartPos);
               $ClosingTagLength = strlen('</'.$Token.'>');


               // Falls ausf�hrlicher End-Tag nicht vorkommt nach einfachem suchen
               if($TagEndPos === false){

                  $TagEndPos = strpos($Content,'/>',$TagStartPos);
                  $ClosingTagLength = 2;


                  // Falls kein End-Tag vorhanden -> Fehler!
                  if($TagEndPos === false){
                     trigger_error('[Document::__extractTagLibTags()] No closing tag found for tag "&lt;'.$Token.' /&gt;"!',E_USER_ERROR);
                     exit();
                   // end if
                  }

                // end if
               }


               // Tag-String extrahieren
               $TagStringLength = ($TagEndPos - $TagStartPos) + $ClosingTagLength;
               $TagString = substr($Content,$TagStartPos,$TagStringLength);


               // NEU (Bugfix f�r Fehler bei Mischungen aus selbst- und exklusiv-schlie�enden Tags):
               // Pr�fen, ob ein �ffnender Tag im bisher angenommen Tag-String ist. Kommt
               // dieser vor, so muss der Tag-String neu definiert werden.
               if(substr_count($TagString,'<'.$Token) > 1){

                  // Position des selbsschli�enden Zeichens finden
                  $TagEndPos = strpos($Content,'/>',$TagStartPos);


                  // String-L�nge des selbst schlie�enden Tag-Zeichens f�r sp�tere Verwendung setzen
                  $ClosingTagLength = 2;


                  // L�nge des TagStrings f�r sp�tere Verwendung setzen
                  $TagStringLength = ($TagEndPos - $TagStartPos) + $ClosingTagLength;


                  // Neuen Tag-String exzerpieren
                  $TagString = substr($Content,$TagStartPos,$TagStringLength);

                // end if
               }


               // Attribute des Tag-Strings auslesen
               $Attributes = xmlParser::getTagAttributes($TagString);


               // Neues Objekt erzeugen
               $Object = new $Module();


               // Context setzen
               $Object->set('Context',$this->__Context);


               // Sprache setzen
               $Object->set('Language',$this->__Language);


               // Attribute einh�ngen
               $Object->setAttributes($Attributes['attributes']);


               // ObjectID setzen
               $Object->set('ObjectID',$ObjectID);


               // Token-String im Content durch <$ObjectID /> ersetzen
               $Content = substr_replace($Content,'<'.$ObjectID.' />',$TagStartPos,$TagStringLength);


               // Vater bekannt machen
               $Object->setByReference('ParentObject',$this);


               // Content einbinden
               $Object->set('Content',$Attributes['content']);


               // Standard-Methode onParseTime() aufrufen
               $Object->onParseTime();


               // Objekt einh�ngen (nicht per Referenz, da sonst Objekte nicht sauber in den Baum eingeh�ngt werden)
               $this->__Children[$ObjectID] = $Object;


               // Loops inkrementieren
               $TagLoops++;


               // Aktuelles Element (neues Objekt) l�schen, um �berlagerungen zu verhindern
               unset($Object);

             // end while
            }

            // Offset erh�hen
            $i++;

          // end while
         }


         // Content wieder in Membervariable speichern
         $this->__Content = $Content;


         // Methode onAfterAppend() auf alle Kinder ausf�hren
         if(count($this->__Children) > 0){

            // Timer starten
            $T = &Singleton::getInstance('benchmarkTimer');
            $T->start('('.get_class($this).') '.$this->__ObjectID.'::__Children[]::onAfterAppend()');


            foreach($this->__Children as $Offset => $Object){
               $this->__Children[$Offset]->onAfterAppend();
             // end for
            }


            // Timer stoppen
            $T->stop('('.get_class($this).') '.$this->__ObjectID.'::__Children[]::onAfterAppend()');

          // end if
         }

       // end function
      }


      /**
      *  @private
      *
      *  XML-Parser-Methode, die die im Content enthaltenen Document-Controller-Tags extrahiert.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function __extractDocumentController(){

         // Kopie ddes aktuellen Contents holen
         $Content = $this->__Content;

         // Tag-Berschreibung initialisieren
         $ControllerStartTag = '<@controller';
         $ControllerEndTag = '@>';

         // Tags suchen
         if(substr_count($Content,$ControllerStartTag) > 0){

            // Position des Tags suchen und Attribute extrahieren
            $TagStartPos = strpos($Content,$ControllerStartTag);
            $TagEndPos = strpos($Content,$ControllerEndTag,$TagStartPos);
            $ControllerTag = substr($Content,$TagStartPos + strlen($ControllerStartTag),($TagEndPos - $TagStartPos) - 1 - strlen($ControllerStartTag));
            $ControllerAttributes = xmlParser::getAttributesFromString($ControllerTag);


            // Document-Controller importieren
            if(!class_exists($ControllerAttributes['class'])){
               import($ControllerAttributes['namespace'],$ControllerAttributes['file']);
             // end if
            }


            // Document-Controller-Klasse bekannt machen
            $this->__DocumentController = $ControllerAttributes['class'];


            // DocumentController Tag ersetzen
            $Content = substr_replace($Content,'',$TagStartPos,($TagEndPos - $TagStartPos) + strlen($ControllerEndTag));


            // Content wieder einsetzen
            $this->__Content = $Content;

          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Implementierung der abstrakten Methode aus "coreObject". Transformiert ein "Document" und gibt den Inhalt dessen zur�ck.<br />
      *
      *  @return string $Content; XML-String des transformierten Inhalts des "Document"s
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 21.01.2007 (Attribute des aktuellen Objekts werden den DocumentController nun zur Transformation zur Verf�gung gestellt)<br />
      *  Version 0.3, 31.01.2007 (Kontext-Behandlung hinzugef�gt)<br />
      *  Version 0.4, 24.02.2007 (Zeitmessung f�r DocCon's auf den Standard umgestellt)<br />
      *  Version 0.5, 09.04.2007 (Sprache wird nun an den DocCon mitgegeben)<br />
      */
      function transform(){

         // Timer einbinden
         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('('.get_class($this).') '.$this->__ObjectID.'::transform()');


         // Kopie des Inhalts anlegen
         $Content = $this->__Content;


         // DocumentController des Documents ausf�hren (falls vorhanden)
         if(!empty($this->__DocumentController)){

            // Zeitmessung starten
            $ID = '('.$this->__DocumentController.') '.(xmlParser::generateUniqID()).'::transformContent()';
            $T->start($ID);


            // Pr�fen, ob Klasse existiert
            if(!class_exists($this->__DocumentController)){
               trigger_error('['.get_class($this).'::transform()] DocumentController "'.$this->__DocumentController.'" cannot be found! Maybe the class name is misspelt!',E_USER_ERROR);
               exit();
             // end if
            }


            // Document-Controller instanziieren
            $DocCon = new $this->__DocumentController;


            // Context des Documents mitgeben
            $DocCon->set('Context',$this->__Context);


            // Language des Documents mitgeben
            $DocCon->set('Language',$this->__Language);


            // Dem DocumentController eine Referent auf sein Document mitgeben
            $DocCon->setByReference('Document',$this);


            // Dem DocumenController den aktuellen Content �bergeben
            $DocCon->set('Content',$Content);


            // Dem DocumenController die aktuellen Attribute mitgeben
            if(is_array($this->__Attributes) && count($this->__Attributes) > 0){
               $DocCon->setAttributes($this->__Attributes);
             // end if
            }


            // Standard-Methode des DocumentControllers ausf�hren
            $DocCon->transformContent();


            // Transformierten Inhalt zur�ckgeben lassen
            $Content = $DocCon->get('Content');


            // Zeitmessung stoppen
            $T->stop($ID);

          // end if
         }


         // XML-Merker-Tags durch die Inhalte der transformierten Children-Objekte ersetzen.
         if(count($this->__Children) > 0){
            foreach($this->__Children as $ObjectID => $Child){
               $Content = str_replace('<'.$ObjectID.' />',$this->__Children[$ObjectID]->transform(),$Content);
             // end foreach
            }
          // end if
         }


         // Timer stoppen
         $T->stop('('.get_class($this).') '.$this->__ObjectID.'::transform()');


         // Content zur�ckgeben
         return $Content;

       // end function
      }

    // end class
   }


   /**
   *  @package core::pagecontroller
   *  @class core_taglib_importdesign
   *
   *  Repr�sentiert die Funktion des core::importdesign-Tags. Generiert einen weiteres Objekt<br />
   *  innerhalb des Objektbaums einer Seite. Kann sich selbst wieder komponieren.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 28.12.2006<br />
   */
   class core_taglib_importdesign extends Document
   {

      /**
      *  @public
      *
      *  Konstruktor der Klasse. Setzt Standart-TagLibs.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function core_taglib_importdesign(){
         parent::Document();
       // end function
      }


      /**
      *  @public
      *
      *  Implementierung der abstrakten Methode aus "coreObject". Bindet das Template, das in den
      *  Attributen beschreiben ist als neuen Objekt-Baum-Knoten ein.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 31.12.2006 (pagepart-Option hinzugef�gt)<br />
      *  Version 0.3, 15.01.2007 (DocumentController Tags werden nun zuerst extrahiert)<br />
      *  Version 0.4, 10.03.2007 (Context kann jetzt im core:importdesign-Tag neu gesetzt werden)<br />
      *  Version 0.5, 11.03.2007 (Attribut "incparam" eingef�hrt um Template-Parameter steuern zu k�nnen)<br />
      */
      function onParseTime(){

         // Timer starten
         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('(core_taglib_importdesign) '.$this->__ObjectID.'::onParseTime()');


         // Attribute auslesen
         $Namespace = trim($this->__Attributes['namespace']);
         $Template = trim($this->__Attributes['template']);


         // Context-Parameter einlesen, falls gesetzt
         if(isset($this->__Attributes['context'])){
            $this->__Context = trim($this->__Attributes['context']);
          // end if
         }


         // IncludeParameter vorgeben
         if(isset($this->__Attributes['incparam'])){
            $IncParam = $this->__Attributes['incparam'];
          // end if
         }
         else{
            $IncParam = 'pagepart';
          // end else
         }


         // Nach pagepart-Optionen suchen
         if(substr_count($Template,'[') > 0){

            if(isset($_REQUEST[$IncParam]) && !empty($_REQUEST[$IncParam])){

               // pagepart-Option aus URL lesen
               $Template = $_REQUEST[$IncParam];

             // end if
            }
            else{

               // IncludeParameter-Option aus "template"-Attribut lesen
               $PagepartStartPos = strpos($Template,'=');
               $PagepartEndPos = strlen($Template) - 1;
               $Template = trim(substr($Template,$PagepartStartPos + 1,($PagepartEndPos - $PagepartStartPos) - 1));

             // end else
            }

          // end if
         }


         // Content einlesen
         $this->__loadContentFromFile($Namespace,$Template);


         // Nach einem DocumentController suchen
         $this->__extractDocumentController();


         // XML-Tags im Content parsen
         $this->__extractTagLibTags();


         // Timer stoppen
         $T->stop('(core_taglib_importdesign) '.$this->__ObjectID.'::onParseTime()');

       // end function
      }

    // end class
   }


   /**
   *  @package core::pagecontroller
   *  @class core_taglib_addtaglib
   *
   *  Repr�sentiert die Funktion des core::addtaglib-Tags. Bindet eine weitere TagLib in den<br />
   *  G�ltigkeitsbereich eines "Document"s ein.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 28.12.2006<br />
   */
   class core_taglib_addtaglib extends Document
   {

      function core_taglib_addtaglib(){
      }


      /**
      *  @public
      *
      *  Implementierung der abstrakten Methode aus "coreObject". Bindet eine TagLib ein.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function onParseTime(){

         // Attribute auslesen
         $Namespace = $this->__Attributes['namespace'];
         $Prefix = $this->__Attributes['prefix'];
         $Class = $this->__Attributes['class'];

         // Neue TagLib in das Eltern-Objekt einh�ngen
         $this->__ParentObject->addTagLib($Namespace,$Prefix,$Class);

       // end function
      }


      /**
      *  @public
      *
      *  Implementierung der abstrakten Methode aus "coreObject".<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 21.04.2007<br />
      */
      function transform(){
         return (string)'';
       // end function
      }

    // end class
   }


   /**
   *  @package core::pagecontroller
   *  @class html_taglib_placeholder
   *
   *  Repr�sentiert einen HTML-Platzhalter innerhalb eines Objekts, das von "Document" erbt und<br />
   *  dort als Children-Objekt eingehangen ist. Dieser kann mit der Methode setPlaceHolder() <br />
   *  innerhalb eines DocumentControllers gesetzt werden, der von "baseController" ableitet.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 28.12.2006<br />
   */
   class html_taglib_placeholder extends Document
   {

      function html_taglib_placeholder(){
      }


      /**
      *  @public
      *
      *  Gibt bei der Transformierung des Platzhalters den Inhalt des Objekts zur�ck, damit dieser <br />
      *  mit dem entsprechenden Inhalt, der im DocumentController gesetzt worden ist, ersetzt wird.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function transform(){
         return $this->__Content;
       // end function
      }

    // end class
   }


   /**
   *  @package core::pagecontroller
   *  @class html_taglib_template
   *
   *  Repr�sentiert einen HTML-Template innerhalb eines Objekts, das von "Document" erbt und dort als Children-<br />
   *  Objekt eingehangen ist. Dieser kann mit der Methode setPlaceHolder() innerhalb eines DocumentControllers gesetzt<br />
   *  werden, der von "baseController" ableitet.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 28.12.2006<br />
   */
   class html_taglib_template extends Document
   {

      /**
      *  @private
      *  Enth�lt die TagLibModule, die beim Transformieren eingezogen werden
      */
      var $__IncludedTagLibModules;


      /**
      *  @private
      *  Indiziert, ob das Template an der Definitionsstelle transformiert und ausgegeben werden soll.
      */
      var $__TransformOnPlace = false;


      /**
      *  @public
      *
      *  Konstruktor der Klasse. F�gt verschiedene TagLibs hinzu.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 29.12.2006<br />
      *  Version 0.2, 30.12.2006 (Config-TagLib hinzugef�gt)<br />
      *  Version 0.3, 05.01.2007 (template:addTagLib-TagLib hinzugef�gt)<br />
      *  Version 0.4, 12.01.2007 (template:addTagLib-TagLib weggenommen)<br />
      *  Version 0.5, 03.03.2007 ("&" vor "new" entfernt)<br />
      *  Version 0.6, 21.04.2007 (template:addtaglib hinzugef�gt)<br />
      *  Version 0.7, 02.05.2007 (template:config entfernt)<br />
      */
      function html_taglib_template(){
         $this->__TagLibs[] = new TagLib('core::pagecontroller','template','placeholder');
         $this->__TagLibs[] = new TagLib('core::pagecontroller','template','addtaglib');
       // end function
      }


      /**
      *  @public
      *
      *  Implementierung der abstrakten Methode onParseTime(). Extrahiert die "template:placeHolder"- und <br />
      *  "template:config"-Tags aus einem Template.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 29.12.2006<br />
      *  Version 0.2, 31.12.2006 (XML-Merker-Tag aus dem Eltern-Objekt l�schen -> onAfterAppend())<br />
      */
      function onParseTime(){
         $this->__extractTagLibTags();
       // end function
      }


      /**
      *  @public
      *
      *  Registriert eine TagLib bei einem Template zur Transformation.<br />
      *
      *  @param string $Module; Name des Moduls
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 05.01.2007<br />
      */
      function registerTagLibModule($Module){
         $this->__IncludedTagLibModules[] = $Module;
       // end function
      }


      /**
      *  @public
      *
      *  Erm�glicht einem DocumentController bei einem Zugriff auf ein Template, dort Platzhalter zu f�llen.<br />
      *
      *  @param string $Name; Name des Platzhalters
      *  @param string $Value; Wert des Platzhalters
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 29.12.2006<br />
      */
      function setPlaceHolder($Name,$Value){

         // Deklariert das notwendige TagLib-Modul
         $TagLibModule = 'template_taglib_placeholder';


         // Falls TagLib-Modul nicht vorhanden -> Fehler!
         if(!class_exists($TagLibModule)){
            trigger_error('[html_taglib_template::setPlaceHolder()] TagLib module '.$TagLibModule.' is not loaded!',E_USER_ERROR);
            exit();
          // end if
         }


         // Anzahl der Platzhalter z�hlen
         $PlaceHolderCount = 0;


         // Pr�fen, ob Kinder vorhanden
         if(count($this->__Children) > 0){

            // Nachsehen, ob es Kinder der Klasse 'template_taglib_placeholder' gibt
            foreach($this->__Children as $ObjectID => $Child){

               // Pr�fen, ob Kind ein
               if(get_class($Child) == $TagLibModule){

                  // Pr�fen, ob das Attribut 'name' dem angegebenen Namen entspricht
                  // und Content einsetzen
                  //if($Child->__Attributes['name'] == $Name){
                  if($Child->getAttribute('name') == $Name){

                     $this->__Children[$ObjectID]->set('Content',$Value);
                     $PlaceHolderCount++;

                   // end if
                  }

                // end if
               }

             // end foreach
            }

          // end if
         }
         else{

            // Falls keine Kinder existieren -> Fehler!
            trigger_error('[html_taglib_template::setPlaceHolder()] No placeholder object with name "'.$Name.'" composed in current template for document controller "'.($this->__ParentObject->__DocumentController).'"! Perhaps tag library template:placeHolder is not loaded in template "'.$this->__Attributes['name'].'"!',E_USER_ERROR);
            exit();

          // end else
         }


         // Warnen, falls kein Platzhalter gefunden wurde
         if($PlaceHolderCount < 1){
            trigger_error('[html_taglib_template::setPlaceHolder()] There are no placeholders found for name "'.$Name.'" in template "'.($this->__Attributes['name']).'" in document controller "'.($this->__ParentObject->__DocumentController).'"!',E_USER_WARNING);
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Gibt bei der Transformierung den Inhalt des Objekts zur�ck, damit dieser mit dem entsprechenden<br />
      *  Inhalt, der im DocumentController gesetzt worden ist, ersetzt wird. Inhalt eines Template muss immer<br />
      *  mit transform() zur�ckgeholt werden. Beispiel aus einem DocumentController:
      *  <pre>
      *     $Template = & $this->__getContentTemplate('MyTemplate');
      *     $Template->setPlaceHolder('Google','http://www.google.de');
      *     $this->__Content = $Template->transformTemplate();
      *  </pre>
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 29.12.2006<br />
      *  Version 0.2, 31.12.2006 ($this->__isVisible wird nicht mehr abgefragt, da XML-Merker-Tag aus dem Eltern-Objekt gel�scht wurde; TEST!!!)<br />
      *  Version 0.3, 02.02.2007 (Methode in transformTemplate() umgenannt; visible-Markierung aus Klasse entfernt)<br />
      *  Version 0.4, 05.01.2007 (template:addTagLib-TagLib hinzugef�gt)<br />
      */
      function transformTemplate(){

         // Puffer f�r transformierten Puffer erzeugen
         $Content = (string)'';


         // Kopie des Contents holen
         $Content = $this->__Content;


         // Kinder durchiterieren und Objekte von registrierten Klasse (z.B. 'template_taglib_placeholder')
         // transformieren
         if(count($this->__Children) > 0){

            foreach($this->__Children as $ObjectID => $Child){

               // XML-Merker-Tags durch die Inhalte der Kinder ersetzen
               if(in_array(get_class($Child),$this->__IncludedTagLibModules)){
                  $Content = str_replace('<'.$ObjectID.' />',$Child->transform(),$Content);
                // end if
               }

             // end foreach
            }

          // end if
         }

         // Fertigen Inhalt zur�ckgeben
         return $Content;

       // end function
      }


      /**
      *  @public
      *
      *  Definiert, dass das Template an der exakten Definitionsstelle transformiert und<br />
      *  ausgegeben werden soll.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 19.05.2008<br />
      */
      function transformOnPlace(){
         $this->__TransformOnPlace = true;
       // end function
      }


      /**
      *  @public
      *
      *  Bei der Transformierung des Templates wird keine Aktion ausgef�hrt, da ein Template<br />
      *  bereits mit transformTemplate() innerhalb eines DocumentControllers transformiert wird /<br />
      *  werden kann. Falls zuvor transformOnPlace() auf das Template ausgef�hrt wurde, wird der<br />
      *  Inhalt des Templates an der exakten Definitionsstelle transformiert uns ausgegeben.<br />
      *
      *  @return string $Content; Leer-String oder Inhalt des Tags
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 02.01.2007<br />
      *  Version 0.2, 12.01.2007 (Es wird nun ein Leer-String zur�ckgegeben)<br />
      *  Version 0.3, 19.05.2008 (transformOnPlace()-Feature implementiert)<br />
      */
      function transform(){

         // Pr�fen, ob Template ausgegeben werden soll
         if($this->__TransformOnPlace === true){
            return $this->transformTemplate();
          // end if
         }

         // Leerstring zur�ckgeben
         return (string)'';

       // end function
      }

    // end class
   }


   /**
   *  @package core::pagecontroller
   *  @class template_taglib_placeholder
   *
   *  Erm�glicht Platzhalter in einem Template. Wird unter einem "html_taglib_template"-Objekt komponiert.<br />
   *  Hat keine weiteren Kinder.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 29.12.2006<br />
   */
   class template_taglib_placeholder extends Document
   {

      function template_taglib_placeholder(){
      }


      /**
      *  @public
      *
      *  Implementierung der abstrakten Methode aus "coreObject". Bindet eine TagLib ein.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 05.01.2007<br />
      */
      function onParseTime(){
         $this->__ParentObject->registerTagLibModule(get_class($this));
       // end function
      }


      /**
      *  @public
      *
      *  Gibt bei der Transformierung des Platzhalters den Inhalt des Objekts zur�ck, damit dieser mit dem entsprechenden<br />
      *  Inhalt, der im DocumentController gesetzt worden ist, ersetzt wird.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 29.12.2006<br />
      */
      function transform(){
         return $this->__Content;
       // end function
      }

    // end class
   }


   /**
   *  @package core::pagecontroller
   *  @class template_taglib_addtaglib
   *
   *  Repr�sentiert die Funktion des template::addtaglib-Tags. Bindet eine weitere TagLib in den G�ltigkeitsbereich<br />
   *  eines "Template"s ein.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 21.04.2007<br />
   */

   class template_taglib_addtaglib extends Document
   {

      function template_taglib_addtaglib(){
      }


      /**
      *  @public
      *
      *  Implementierung der abstrakten Methode aus "coreObject". Bindet eine TagLib ein.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 21.04.2007<br />
      */
      function onParseTime(){

         // Beim Template registrieren, damit das Objekt transformiert wird
         $this->__ParentObject->registerTagLibModule(get_class($this));

         // Attribute auslesen
         $Namespace = $this->__Attributes['namespace'];
         $Prefix = $this->__Attributes['prefix'];
         $Class = $this->__Attributes['class'];

         // Neue TagLib in das Eltern-Objekt einh�ngen
         $this->__ParentObject->addTagLib($Namespace,$Prefix,$Class);

       // end function
      }

    // end class
   }


   /**
   *  @package core::pagecontroller
   *  @class baseController
   *  @abstract
   *
   *  Implementiert einen abstrakten DocumentController.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 28.12.2006<br />
   *  Version 0.2, 04.11.2007 (isButtonPushed() entfernt)<br />
   */
   class baseController extends Document
   {

      /**
      *  @private
      *  Referenz auf das Document.
      */
      var $__Document;


      function baseController(){
      }


      /**
      *  @public
      *  @abstract
      *
      *  Abstrakte Methode, die beim Transformieren der Seite auf jeden DocumentController aufgerufen wird.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function transformContent(){
      }


      /**
      *  @public
      *
      *  Implementiert eine Methode, mit der Platzhalter innerhalb des Inhalts eines "Document"s gesetzt werden k�nnen.<br />
      *  Hierzu ist die TagLib-Klasse "html_taglib_placeHolder" notwendig.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function setPlaceHolder($Name,$Value){

         // Deklariert das notwendige TagLib-Modul
         $TagLibModule = 'html_taglib_placeholder';


         // Falls TagLib-Modul nicht vorhanden -> Fehler!
         if(!class_exists($TagLibModule)){
            trigger_error('['.get_class($this).'::setPlaceHolder()] TagLib module '.$TagLibModule.' is not loaded!',E_USER_ERROR);
          // end if
         }


         // Anzahl der Platzhalter z�hlen
         $PlaceHolderCount = 0;


         // Pr�fen, ob Kinder existieren
         if(count($this->__Document->__Children) > 0){

            // Platzhalter setzen
            foreach($this->__Document->__Children as $ObjectID => $Child){

               // Klassen mit dem Namen "$TagLibModule" aus den Child-Objekten des
               // aktuellen "Document"s aussuchen
               if(get_class($Child) == $TagLibModule){

                  // Klassen mit dem auf den Attribut Namen lautenden Namen suchen
                  // und den gew�nschten Inhalt einsetzen
                  if($Child->__Attributes['name'] == $Name){
                     $this->__Document->__Children[$ObjectID]->set('Content',$Value);
                     $PlaceHolderCount++;
                   // end if
                  }

                // end if
               }

             // end foreach
            }

          // end if
         }
         else{

            // Falls keine Kinder existieren -> Fehler!
            trigger_error('['.get_class($this).'::setPlaceHolder()] No placeholder object with name "'.$Name.'" composed in current document for document controller "'.get_class($this).'"! Perhaps tag library html:placeholder is not loaded in current template!',E_USER_ERROR);
            exit();

          // end else
         }

         // Warnen, falls kein Platzhalter gefunden wurde
         if($PlaceHolderCount < 1){
            trigger_error('['.get_class($this).'::setPlaceHolder()] There are no placeholders found for name "'.$Name.'" in document controller "'.get_class($this).'"!',E_USER_WARNING);
          // end if
         }

       // end function
      }


      /**
      *  @private
      *
      *  Implementiert eine Methode, mit der innerhalb des DocumentControllers auf eine Form zugegriffen<br />
      *  werden kann. Hierzu ist die TagLib-Klasse "html_taglib_form" notwendig.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 12.01.2007<br />
      *  Version 0.2, 14.06.2008 (Fehlermeldung verbessert)<br />
      */
      function &__getForm($Name){

         // Deklariert das notwendige TagLbib-Modul
         $TagLibModule = 'html_taglib_form';


         // Falls TagLib-Modul nicht vorhanden -> Fehler!
         if(!class_exists($TagLibModule)){
            trigger_error('['.get_class($this).'::__getForm()] TagLib module "'.$TagLibModule.'" is not loaded!',E_USER_ERROR);
          // end if
         }


         // Pr�fen, ob Kinder existieren
         if(count($this->__Document->__Children) > 0){

            // Templates aus dem aktuellen Document bereitstellen
            foreach($this->__Document->__Children as $ObjectID => $Child){

               // Klassen mit dem Namen "$TagLibModule" aus den Child-Objekten des
               // aktuellen "Document"s als Referenz zur�ckgeben
               if(get_class($Child) == $TagLibModule){

                  // Pr�fen, ob das gefundene Template $Name hei�t.
                  if($Child->getAttribute('name') == $Name){
                     return $this->__Document->__Children[$ObjectID];
                   // end if
                  }

                // end if
               }

             // end foreach
            }

          // end if
         }
         else{

            // Falls keine Kinder existieren -> Fehler!
            trigger_error('['.get_class($this).'::__getForm()] No form object with name "'.$Name.'" composed in current document for document controller "'.get_class($this).'"! Perhaps tag library html:form is not loaded in current document!',E_USER_ERROR);
            exit();

          // end else
         }


         // Falls das Template nicht gefunden werden kann -> Fehler!
         trigger_error('['.get_class($this).'::__getForm()] Form with name "'.$Name.'" cannot be found in document controller "'.get_class($this).'"!',E_USER_ERROR);
         exit();

       // end function
      }


      /**
      *  @private
      *
      *  Implementiert eine Methode, mit der innerhalb des DocumentControllers auf ein Content-Template zugegriffen<br />
      *  werden kann. Hierzu ist die TagLib-Klasse "html_taglib_template" notwendig.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 03.01.2007 (Bug behoben, dass immer erstes Template referenziert wurde)<br />
      *  Version 0.3, 12.01.2006 (Von "__getContentTemplate" nach "__getTemplate" umbenannt, wg. Einf�hrung von "__getForm")<br />
      */
      function &__getTemplate($Name){

         // Deklariert das notwendige TagLib-Modul
         $TagLibModule = 'html_taglib_template';


         // Falls TagLib-Modul nicht vorhanden -> Fehler!
         if(!class_exists($TagLibModule)){
            trigger_error('['.get_class($this).'::__getTemplate()] TagLib module "'.$TagLibModule.'" is not loaded!',E_USER_ERROR);
          // end if
         }


         // Pr�fen, ob Kinder existieren
         if(count($this->__Document->__Children) > 0){

            // Templates aus dem aktuellen Document bereitstellen
            foreach($this->__Document->__Children as $ObjectID => $Child){

               // Klassen mit dem Namen "$TagLibModule" aus den Child-Objekten des
               // aktuellen "Document"s als Referenz zur�ckgeben
               if(get_class($Child) == $TagLibModule){

                  // Pr�fen, ob das gefundene Template $Name hei�t.
                  if($Child->getAttribute('name') == $Name){
                     return $this->__Document->__Children[$ObjectID];
                   // end if
                  }

                // end if
               }

             // end foreach
            }

          // end if
         }
         else{

            // Falls keine Kinder existieren -> Fehler!
            trigger_error('['.get_class($this).'::__getTemplate()] No template object with name "'.$Name.'" composed in current document for document controller "'.get_class($this).'"! Perhaps tag library html:template is not loaded in current template!',E_USER_ERROR);
            exit();

          // end else
         }


         // Falls das Template nicht gefunden werden kann -> Fehler!
         trigger_error('['.get_class($this).'::__getTemplate()] Template with name "'.$Name.'" cannot be found!',E_USER_ERROR);
         exit();

       // end function
      }


      /**
      *  @private
      *
      *  Pr�ft, ob ein Platzhalter im aktuellen Template vorhanden ist.<br />
      *
      *  @param string $Name; Name des Platzhalters
      *  @return bool $Exists; true | false
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 11.03.2007<br />
      */
      function __placeholderExists($Name){

         // Kinder des aktuellen Dokuments holen
         $Children = &$this->__Document->getByReference('Children');

         // Kinder pr�fen
         foreach($Children as $Key => $Child){

            // Auf Platzhalter-Kinder pr�fen
            if(get_class($Child) == 'html_taglib_placeholder'){

               // Auf Namen pr�fen
               if($Child->getAttribute('name') == $Name){
                  return true;
                // end if
               }

             // end if
            }

          // end foreach
         }

         // False zur�ckgeben
         return false;

       // end function
      }


      /**
      *  @private
      *
      *  Pr�ft, ob ein Platzhalter im aktuellen Template vorhanden ist.<br />
      *
      *  @param string $Name; Name des Platzhalters
      *  @return bool $Exists; true | false
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 11.03.2007<br />
      */
      function __templatePlaceholderExists(&$Template,$Name){

         // Kinder des Templates holen
         $Children = &$Template->getByReference('Children');

         // Kinder pr�fen
         foreach($Children as $Key => $Child){

            // Auf Platzhalter-Kinder pr�fen
            if(get_class($Child) == 'template_taglib_placeholder'){

               // Auf Namen pr�fen
               if($Child->getAttribute('name') == $Name){
                  return true;
                // end if
               }

             // end if
            }

          // end foreach
         }

         // False zur�ckgeben
         return false;

       // end function
      }

    // end class
   }
?>
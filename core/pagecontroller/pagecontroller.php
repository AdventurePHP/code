<?php
   /**
   *  @file pagecontroller.php
   *
   *  Setups the framework's core environment. Initializes the Registry, that stores parameters,
   *  that are used within the complete framework. These are
   *
   *  - Environment      : environment, the application is executed in. The value is 'DEFAULT' in common
   *  - URLRewriting     : indicates, is url rewriting should be used
   *  - LogDir           : path, where logfiles are stored. The value is './logs' by default.
   *  - URLBasePath      : absolute url base path of the application (not really necessary)
   *  - LibPath          : path, where the framework and your own libraries reside. This path can be used
   *                       to adress files with in the lib path directly (e.g. images or other ressources)
   *  - CurrentRequestURL: the fully qualified request url
   *
   *  The file also contains the pagecontroller core implementation with the classes Page,
   *  Document, TagLib, coreObject, xmlParser and baseController (the basic MVC document controller).
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 20.06.2008<br />
   *  Version 0.2, 16.07.2008 (added the LibPath to the registry namespace apf::core)
   *  Version 0.3, 07.08.2008 (Made LibPath readonly)<br />
   *  Version 0.4, 13.08.2008 (Fixed some timing problems with the registry initialisation)<br />
   *  Version 0.5, 14.08.2008 (Changed LogDir initialisation to absolute paths)<br />
   *  Version 0.6, 05.11.2008 (Added the 'CurrentRequestURL' attribute to the 'apf::core' namespace of the registry)<br />
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

   // define the APPS__PATH constant to be used in the import() function
   define('APPS__PATH',implode($AppsPath,'/'));

   /////////////////////////////////////////////////////////////////////////////////////////////////


   // include core libraries for the basic configuration
   import('core::singleton','Singleton');
   import('core::registry','Registry');


   // define base parameters of the framework's core and tools layer
   $Reg = &Singleton::getInstance('Registry');
   $Reg->register('apf::core','Environment','DEFAULT');
   $Reg->register('apf::core','URLRewriting',false);
   $Reg->register('apf::core','LogDir',str_replace('\\','/',getcwd()).'/logs');
   $Reg->register('apf::core','URLBasePath',$_SERVER['HTTP_HOST']);
   $Reg->register('apf::core','LibPath',APPS__PATH,true);


   // define current request url entry
   if($_SERVER['SERVER_PORT'] == '443'){
      $protocol = 'https://';
    // end if
   }
   else{
      $protocol = 'http://';
    // end else
   }
   $Reg->register('apf::core','CurrentRequestURL',$protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],true);


   // include necessary core libraries for the pagecontroller
   import('core::errorhandler','errorHandler');
   import('core::service','serviceManager');
   import('core::configuration','configurationManager');
   import('core::benchmark','benchmarkTimer');
   import('core::filter','filterFactory');


   /**
   *  @package core::pagecontroller
   *
   *  Imports classes or modules from a given namespace. If the php5 support is enabled, files with
   *  the extension ".php5" are included. If no php5 file is present, the ".php" file is included
   *  as a fallback scenario. This is also done, that the files, that can be used in both versions
   *  do not have to be renamed or stored twice.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 03.12.2005<br />
   *  Version 0.2, 14.04.2006<br />
   *  Version 0.3, 14.01.2007 (Proprietären Support für PHP 5 implementiert)<br />
   *  Version 0.4, 03.03.2007 (Schönheitskorrekturen am Code)<br />
   *  Version 0.5, 03.03.2007 (Support für den Betrieb unter PHP 4 und PHP 5 hinzugefügt)<br />
   *  Version 0.6, 24.03.2008 (Aus Performance-Gründen Cache für bereits inportierte Dateien eingeführt)<br />
   *  Version 0.7, 20.06.2008 (Wegen Einführung Registry in den pagecontroller verlagert)<br />
   */
   function import($Namespace,$File,$ActivatePHP5Support = true){

      // Dateinamen zusammenbauen
      $File = APPS__PATH.'/'.str_replace('::','/',$Namespace).'/'.$File;

      // Prüfen, ob Datei bereits inkludiert ist
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

         // Dateiendung anhängen
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

         // Dateiendung anhängen
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
   *  @see http://php.net/print_r
   *
   *  Creates a print_r() output of the given object, array, string or integer.
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 04.02.3006<br />
   *  Version 0.2, 23.04.2006 (The output is now returned instead of printed directly)<br />
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
         $buffer .= htmlentities(print_r($o,true));
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
   *  Static parser for XML / XSL Strings.
   *
   *  @author Christian Schäfer
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
      *  Extracts the attributes from an XML attributes string.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 22.12.2006<br />
      *  Version 0.2, 30.12.2006 (Fehler beim Parsen von Tags ohne Attribute behoben (setzen von $tagAttributeDel))<br />
      *  Version 0.3, 03.01.2007 (Fehler beim Bestimmen des Attribut-Strings behoben)<br />
      *  Version 0.4, 13.01.2007 (Fehlerausgabe beim Parse-Fehler verbessert)<br />
      *  Version 0.5, 16.11.2007 (Fehler bei Fehlerausgabe von Tags verbessert)<br />
      *  Version 0.6, 03.11.2008 (Fixed the issue, that a TAB character is no valid token to attributes delimiter)<br />
      *  Version 0.7, 04.11.2008 (Fixed issue, that a combination of TAB and SPACE characters leads to wrong attributes parsing)<br />
      *  Version 0.8, 05.11.2008 (Removed the TAB support due to performance and fault tolerance problems)<br />
      */
      function getTagAttributes($TagString){

         // search for taglib to attributes string delimiter
         $tagAttributeDel = strpos($TagString,' ');

         // search for the closing sign
         $posTagClosingSign = strpos($TagString,'>');

         // Falls Trennposition zwischen Tag und Attributen nicht gefunden wurden, oder das
         // TagEnde-Zeichen vor dem Delimiter zwischen Tag und Attributen liegt, wird
         // das "Ende-Zeichen" ('>') als Trennzeichen gesetzt. So können Tags ohne
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

         // Position der ersten schließenden Klammer nach dem Attribut-String finden
         $posEndAttrib = strpos($TagString,'>');

         // Restlichen String als Attributstring extrahieren
         // OLD: Machte Fehler, falls ein XML-Tag direkt im Anschluss an das Tag folgte
         //$attributesString = substr($TagString,$tagAttributeDel + 1,$posEndAttrib);
         $attributesString = substr($TagString,$tagAttributeDel + 1,$posEndAttrib - $tagAttributeDel);

         // Attribute des Strings auslesen
         $attributes = xmlParser::getAttributesFromString($attributesString);

         // Prüfen ob Tag selbstschließend. Falls nicht Content einlesen
         if(substr($TagString,$posEndAttrib - 1,1) == '/'){
            $content = (string)'';
          // end if
         }
         else{

            // Content-Variable initialisieren
            $content = (string)'';

            // Prüfen ob schließender Tag vorhanden ist
            if(strpos($TagString,'</'.$prefix.':'.$class.'>') === false){
               trigger_error('[xmlParser::getTagAttributes()] No closing tag found for tag "&lt;'.$prefix.':'.$class.' /&gt;"! Tag string: "'.htmlentities($TagString).'".',E_USER_ERROR);
             // end if
            }
            else{

               // Benötigte Variablen initialisieren
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

                  // Falls mehr als $MaxCount Stellen gefunden -> aus Sicherheitsgründen aussteigen
                  if($Count > $MaxCount){
                     $found = false;
                   // end if
                  }

                  // Count erhöhen
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

         // Werte zurückgeben
         return $Attributes;

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Extracts XML attributes from an attributes string. Returns an associative array with the attributes as keys and the values.
      *  <pre>
      *    $Array['ATTRIBUTE_NAME'] = 'ATTRIBUTE_VALUE';
      *  </pre>
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 22.12.2006<br />
      *  Version 0.2, 30.12.2006 (Enhanced the documentation)<br />
      *  Version 0.3, 14.01.2007 (Improved the error message)<br />
      *  Version 0.4, 14.11.2007 (Removed $hasFound; see http://forum.adventure-php-framework.org/de/viewtopic.php?t=7)<br />
      */
      function getAttributesFromString($attributesString){

         $Attributes = array ();
         $foundAtr = true;
         $Offset = 0;

         $ParserLoops = 0;
         $ParserMaxLoops = 20;

         // Attribute iterativ suchen
         while(true){

            // Parser-Durchläufe inkrementieren
            $ParserLoops++;

            // Prüfen, om Maximum an Parser-Durchläufen schon erreicht ist
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
      *  Generates a uniqe id, that is used as the object id for the APF DOM tree.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 22.12.2006<br />
      */
      function generateUniqID($md5 = true){

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
   *  Represents the base objects of (nearly) all APF classes. Especially all GUI classes derive
   *  from this class.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.12.2006<br />
   *  Version 0.2, 11.02.2007 (Attribute Language und Context hinzugefügt)<br />
   *  Version 0.3, 28.10.2008 (Added the __ServiceType member to indicate the service manager creation type)<br />
   *  Version 0.4, 03.11.2008 (Added initializing values to some of the class members)<br />
   */
   class coreObject
   {

      /**
      *  @private
      *  Eindeutige ID des Objekts.
      */
      var $__ObjectID = null;

      /**
      *  @private
      *  Referenz auf das Eltern-Objekt.
      */
      var $__ParentObject = null;

      /**
      *  @private
      *  Kinder eines Objekts.
      */
      var $__Children = array();

      /**
      *  @private
      *  Attribute eines Objekts, die aus einem XML-Tag gelesen werden.
      */
      var $__Attributes = array();


      /**
      *  @private
      *  Kontext eines Objekts oder einer Applikation.
      */
      var $__Context = null;


      /**
      *  @private
      *  Sprache eines Objekts oder einer Applikation.
      */
      var $__Language = 'de';


      /**
      *  @since 0.3
      *  @private
      *  Contains the service type, if the object was created with the serviceManager.
      */
      var $__ServiceType = null;


      /**
      *  @public
      *
      *  Konstruktor des abstrakten Basis-Objekts.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function coreObject(){
      }


      /**
      *  @public
      *
      *  Implements an abstract get() method.
      *
      *  @param string $Attribut; Name des Member-Attributes, dessen Wert zurückgegeben werden soll.
      *  @return void $this->{'__'.$Attribut}; Gibt das addressierte Member-Attribut zurück, oder null im Fehlerfall.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 02.02.2007 (Behandlung, falls Attribut nicht existiert hinzugefügt)<br />
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
      *  Implements an abstract set() method.
      *
      *  @param string $Attribut; Name des Member-Attributes, dessen Wert gesetzt werden soll.
      *  @param void $Value; Wert des Member-Attributes, dessen Wert gesetzt werden soll.
      *
      *  @author Christian Schäfer
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
      *  Implements an abstract add() method. Appends a value to a given list.
      *
      *  @param string $Attribut; Name des Member-Attributes, dessen Wert gesetzt werden soll.
      *  @param void $Value; Wertdes Member-Attributes, dessen Wert gesetzt werden soll.
      *
      *  @author Christian Schäfer
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
      *  Returns the object's attribute.
      *
      *  @param string $Name; Name des Attributes, dessen Wert zurückgeliefert werden soll.
      *  @return void $this->__Attributes[$Name]; Gibt das addressierte Attribut zurück, oder null im Fehlerfall.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 02.02.2007 (Behandlung, falls Attribut nicht existiert hinzugefügt)<br />
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
      *  Sets an object's attribute.
      *
      *  @param string $Name; Name des Attributes, dessen Wert zurückgeliefert werden soll.
      *  @param void $Value; Wert des Attributes, dessen Wert gesetzt werden soll.
      *
      *  @author Christian Schäfer
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
      *  Returns an object's attributes.
      *
      *  @return array $this->__Attributes; Gibt das Attributes-Array zurück
      *
      *  @author Christian Schäfer
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
      *  Deletes an attribute.
      *
      *  @param string $Name; Name des zu löschenden Attributes.<br />
      *
      *  @author Christian Schäfer
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
      *  Sets an object's attributes.
      *
      *  @param array $Attributes; Attribut-Array
      *
      *  @author Christian Schäfer
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
      *  Sets an object's member by reference. This is used to guarantee php4 support.
      *
      *  @param string $Attribute; Names des Attributes
      *  @parem object $Object; Referenz auf ein Objekt
      *
      *  @author Christian Schäfer
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
      *  Returns the content of a member by reference. This is used to guarantee php4 support.
      *
      *  @param string $Attribute; Names des Attributes
      *  @return object $Object; Referenz auf ein Objekt, oder null im Fehlerfall.
      *
      *  @author Christian Schäfer
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
      *  Interface definition of the transform() method. This function is used to transform a
      *  DOM node within the page controller. It must be implemented by derived classes.
      *
      *  @public
      *  @abstract
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function transform(){
      }


      /**
      *  Interface definition of the init() method. This function is used to initialize a service
      *  object with the service manager. It must be implemented by derived classes.
      *
      *  @public
      *  @abstract
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 30.03.2007<br />
      */
      function init(){
      }


      /**
      *  Interface definition of the onParseTime() method. This function is called after the creation
      *  of a new DOM node. It must be implemented by derived classes.
      *
      *  @public
      *  @abstract
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function onParseTime(){
      }


      /**
      *  Interface definition of the onAfterAppend() method. This function is called after the DOM
      *  node is appended to the DOM tree. It must be implemented by derived classes.
      *
      *  @public
      *  @abstract
      *
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function onAfterAppend(){
      }


      /**
      *  Interface definition of the transformContent() method. This function is applied to a
      *  document controller during the transformation of a DOM node. It must be implemented by
      *  each document controller to influence content generation.
      *
      *  @public
      *  @abstract
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function transformContent(){
      }


      /**
      *  @private
      *
      *  Returns a service object according to the current application context.
      *
      *  @param string $Namespace; Namespace des Moduls / der Konfiguration
      *  @param string $ServiceName; Name des Service Objekts
      *  @param string $Type; Typ der Initialisierung des ServiceObjekts
      *  @return object $ServiceObject; ServiceObject
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 07.03.2007<br />
      *  Version 0.2, 08.03.2007 (Context wird nun aus dem aktuellen Objekt gezogen)<br />
      *  Version 0.3, 10.03.2007 (Methode ist nun private)<br />
      *  Version 0.4, 22.04.2007 (Um Übergabe der Sprache erweitert)<br />
      *  Version 0.5, 24.02.2008 (Um weiteren Parameter $Type erweitert)<br />
      */
      function &__getServiceObject($Namespace,$ServiceName,$Type = 'SINGLETON'){

         // ServiceManager holen
         $serviceManager = &Singleton::getInstance('serviceManager');

         // Eigenen Context beim ServiceManager bekannt machen
         $serviceManager->setContext($this->__Context);

         // Sprache beim ServiceManager bekannt machen
         $serviceManager->setLanguage($this->__Language);

         // ServiceObject zurückgeben
         return $serviceManager->getServiceObject($Namespace,$ServiceName,$Type);

       // end function
      }


      /**
      *  @private
      *
      *  Returns a initialized service object according to the current application context.
      *
      *  @param string $Namespace; Namespace des Moduls / der Konfiguration
      *  @param string $ServiceName; Name des Service Objekts
      *  @param string $InitParam; Initialisierungs-Parameter
      *  @param string $Type; Typ der Initialisierung des ServiceObjekts*
      *  @return object $ServiceObject; ServiceObject
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.03.2007<br />
      *  Version 0.2, 22.04.2007 (Um Übergabe der Sprache erweitert)<br />
      *  Version 0.3, 24.02.2008 (Um weiteren Parameter $Type erweitert)<br />
      */
      function &__getAndInitServiceObject($Namespace,$ServiceName,$InitParam,$Type = 'SINGLETON'){

         // ServiceManager holen
         $serviceManager = &Singleton::getInstance('serviceManager');

         // Eigenen Context beim ServiceManager bekannt machen
         $serviceManager->setContext($this->__Context);

         // Sprache beim ServiceManager bekannt machen
         $serviceManager->setLanguage($this->__Language);

         // ServiceObject zurückgeben
         return $serviceManager->getAndInitServiceObject($Namespace,$ServiceName,$InitParam,$Type);

       // end function
      }


      /**
      *  @private
      *
      *  Returns a configuration object according to the current application context and the given
      *  parameters.
      *
      *  @param string $Namespace; Namespace des Moduls / der Konfiguration
      *  @param string $ConfigName; Name der Konfiguration
      *  @return object $Configuration; Konfigurations-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 07.03.2007<br />
      *  Version 0.2, 08.03.2007 (Context wird nun aus dem aktuellen Objekt gezogen)<br />
      *  Version 0.3, 10.03.2007 (Methode ist nun private)<br />
      */
      function &__getConfiguration($Namespace,$ConfigName){

         // configurationManager holen
         $configurationManager = &Singleton::getInstance('configurationManager');

         // Configuration Objekt zurückgeben
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
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.01.2007<br />
      *  Version 0.2, 07.01.2007 ($ExclusionArray-Verhalten hinzugefügt)<br />
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
   *  Repräsentiert eine Tag-Library.<br />
   *
   *  @author Christian Schäfer
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
      *  @author Christian Schäfer
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
   *  Repräsentiert eine Webseite. Bildet den root-Knoten derselben.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 28.12.2006<br />
   *  Version 0.2, 03.01.2007 (URL-Rewriting eingeführt)<br />
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
      *  Constructor of the page class. The class is the root node of the APF DOM tree..
      *
      *  @param string $Name optional name of the page
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 03.01.2007 (URL-Rewriting eingeführt)<br />
      *  Version 0.3, 08.06.2007 (URL-Rewriting in Filter ausgelagert)<br />
      *  Version 0.4, 20.06.2008 (Registry für "APPS__URL_REWRITING" eingeführt)<br />
      *  Version 0.5, 20.10.2008 (Removed second parameter due to registry introduction in 1.7-beta)<br />
      */
      function Page($Name = ''){

         // get URLRewrite option{
         $Reg = &Singleton::getInstance('Registry');
         $this->__URLRewrite = $Reg->retrieve('apf::core','URLRewriting');

         // Attribute setzen
         $this->__Name = $Name;
         $this->__ObjectID = xmlParser::generateUniqID();

         // GET-URI rewriten, wenn erwünscht
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
      *  Erzeugt das initiale "Document" einer "Page" und läd das initiale Template.<br />
      *
      *  @param string $Namespace; Namespace des initialen Templates
      *  @param string $Design; Name des initialen Designs
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 31.01.2007 (Context des Documents wird nun gesetzt)<br />
      *  Version 0.3, 04.03.2007 (Namespace wird als Context verwendet, falls kein Context vorhanden)<br />
      *  Version 0.4, 22.04.2007 (Sprache der Page wird nun in das Document übernommen)<br />
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
      *  Transforms the APF DOM tree of the current page. Returns the content of the transformed document.
      *
      *  @return string $Content the content of the transformed page
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 03.01.2007 (URL-Rewriting eingeführt)<br />
      *  Version 0.3, 08.06.2007 (URL-Rewriting in Filter ausgelagert)<br />
      */
      function transform(){

         // Dokument transformieren
         $Content = $this->__Document->transform();

         // Links rewriten, wenn erwünscht
         if($this->__URLRewrite == true){
            $hURF = filterFactory::getFilter('core::filter','htmlLinkRewriteFilter');
            $Content = $hURF->filter($Content);
          // end if
         }

         // HTML-Quelltext zurückgeben
         return $Content;

       // end function
      }

    // end class
   }


   /**
   *  @package core::pagecontroller
   *  @class Document
   *
   *  Repräsentiert ein Dokument innerhalb einer HTML-Seite oder eines Dokuments.<br />
   *  Kann sich selbst wieder komponieren.<br />
   *
   *  @author Christian Schäfer
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
      *  Konstruktor des Objekts. Initialisiert Standard-TagLibs für den Aufbau der HTML-Seite.<br />
      *
      *  @author Christian Schäfer
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
      *  Methode zum Hinzufügen weitere Tag-Libs zu einem Dokument.<br />
      *
      *  @param string $Namespace; Namespace der Tag-Library
      *  @param string $Prefix; Tag-Prefix der Tag-Library
      *  @param string $Class; XML-Klasse der Tag-Library
      *
      *  @author Christian Schäfer
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
      *  @return string $ModuleName; Liefert den Modul-Namen zurück
      *
      *  @author Christian Schäfer
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
      *  Läd das initiale Template. Wird nur vom Objekt "Page" aufgerufen.<br />
      *
      *  @param string $Namespace; Namespace des initialen Templates
      *  @param string $Design; Name des initialen Designs
      *
      *  @author Christian Schäfer
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
      *  Läd ein Template "$Design" aus einem angegebenen Namespace.<br />
      *
      *  @param string $Namespace; Namespace des initialen Templates
      *  @param string $Design; Name des initialen Designs
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 01.01.2007 (Fehler behoben, dass Templates nicht sauber geladen wurden)<br />
      *  Version 0.3, 03.11.2008 (Added code of the responsible template to the error message to ease debugging)<br />
      */
      function __loadContentFromFile($Namespace,$Design){

         $File = APPS__PATH.'/'.str_replace('::','/',$Namespace).'/'.$Design.'.html';

         if(!file_exists($File)){

            // get template code from parent object, if the parent exists
            $code = (string)'';
            if($this->__ParentObject !== null){
               $code = ' Please check your template code ('.htmlentities($this->__ParentObject->get('Content')).').';
             // end if
            }

            // throw error
            trigger_error('[Document::__loadContentFromFile()] Design "'.$Design.'" not existent in namespace "'.$Namespace.'"!'.$code,E_USER_ERROR);
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
      *  Objektbaum aufbaut. Dabei werden für die jeweiligen Tags eigene Child-Objekte im "Document"<br />
      *  erzeugt und die Stellen mit einem Merker-Tag versehen, die bei der Transformation dann wieder<br />
      *  durch ihre Inhalts-Entsprechungen ersetzt werden.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 21.01.2007 (Bugfix: Parser übersah erneut öffnende Tokens im TagString, bei Mischungen aus selbst- und exklusiv-schließenden Tags)<br />
      *  Version 0.3, 31.01.2007 (Kontext-Behandlung hinzugefügt)<br />
      *  Version 0.4, 09.04.2007 (Doppeltes Setzen der Attributes bereinigt, Language-Behandlung hinzugefügt)<br />
      *  Version 0.5, 02.04.2008 (Bug behoben, dass Token nicht in der Fehlermeldung angezeigt wird)<br />
      */
      function __extractTagLibTags(){

         // Kopie des aktuellen Content erzeugen
         $Content = $this->__Content;


         // Hilfsvariable für Parser-Durchläufe (Extraktion der TagLib-Tags) initialiserien
         $TagLibLoops = 0;


         // Laufvariable initialisieren
         $i = 0;


         // TagLibs parsen. Hier wird ein while verwendet, da im Parser-Lauf auch weitere Tag-Libs
         // hinzukommen können. Siehe hierzu Klasse core_taglib_addTagLib!
         while($i < count($this->__TagLibs)){

            // Falls Parserläufe zu viel werden -> Fehler!
            if($TagLibLoops > $this->__MaxLoops){
               trigger_error('[Document::__extractTagLibTags()] Maximum numbers of parsing loops reached!',E_USER_ERROR);
               exit();
             // end if
            }


            // Attribute für Parser-Lauf initialisieren
            $Prefix = $this->__TagLibs[$i]->get('Prefix');
            $Class = $this->__TagLibs[$i]->get('Class');
            $Module = $this->__getModuleName($Prefix, $Class);
            $Token = $Prefix.':'.$Class;
            $TagLoops = 0;


            // TagLib-Tags suchen
            while(substr_count($Content,'<'.$Token) > 0){

               // Falls Parser-Durchläufe zu viele werden -> Fehler
               if($TagLoops > $this->__MaxLoops){
                  trigger_error('['.get_class($this).'::__extractTagLibTags()] Maximum numbers of parsing loops reached!',E_USER_ERROR);
                  exit();
                // end if
               }


               // Eindeutige ID holen
               $ObjectID = xmlParser::generateUniqID();


               // Start- und End-Position des Tags im Content finden.
               // Als End-Position wir immer ein schließender Tag erwartet
               $TagStartPos = strpos($Content,'<'.$Token);
               $TagEndPos = strpos($Content,'</'.$Token.'>',$TagStartPos);
               $ClosingTagLength = strlen('</'.$Token.'>');


               // Falls ausführlicher End-Tag nicht vorkommt nach einfachem suchen
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


               // NEU (Bugfix für Fehler bei Mischungen aus selbst- und exklusiv-schließenden Tags):
               // Prüfen, ob ein öffnender Tag im bisher angenommen Tag-String ist. Kommt
               // dieser vor, so muss der Tag-String neu definiert werden.
               if(substr_count($TagString,'<'.$Token) > 1){

                  // Position des selbsschlißenden Zeichens finden
                  $TagEndPos = strpos($Content,'/>',$TagStartPos);


                  // String-Länge des selbst schließenden Tag-Zeichens für spätere Verwendung setzen
                  $ClosingTagLength = 2;


                  // Länge des TagStrings für spätere Verwendung setzen
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


               // Attribute einhängen
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


               // Objekt einhängen (nicht per Referenz, da sonst Objekte nicht sauber in den Baum eingehängt werden)
               $this->__Children[$ObjectID] = $Object;


               // Loops inkrementieren
               $TagLoops++;


               // Aktuelles Element (neues Objekt) löschen, um Überlagerungen zu verhindern
               unset($Object);

             // end while
            }

            // Offset erhöhen
            $i++;

          // end while
         }


         // Content wieder in Membervariable speichern
         $this->__Content = $Content;


         // Methode onAfterAppend() auf alle Kinder ausführen
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
      *  @author Christian Schäfer
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
      *  Implementierung der abstrakten Methode aus "coreObject". Transformiert ein "Document" und gibt den Inhalt dessen zurück.<br />
      *
      *  @return string $Content; XML-String des transformierten Inhalts des "Document"s
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 21.01.2007 (Attribute des aktuellen Objekts werden den DocumentController nun zur Transformation zur Verfügung gestellt)<br />
      *  Version 0.3, 31.01.2007 (Kontext-Behandlung hinzugefügt)<br />
      *  Version 0.4, 24.02.2007 (Zeitmessung für DocCon's auf den Standard umgestellt)<br />
      *  Version 0.5, 09.04.2007 (Sprache wird nun an den DocCon mitgegeben)<br />
      */
      function transform(){

         // Timer einbinden
         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('('.get_class($this).') '.$this->__ObjectID.'::transform()');


         // Kopie des Inhalts anlegen
         $Content = $this->__Content;


         // DocumentController des Documents ausführen (falls vorhanden)
         if(!empty($this->__DocumentController)){

            // Zeitmessung starten
            $ID = '('.$this->__DocumentController.') '.(xmlParser::generateUniqID()).'::transformContent()';
            $T->start($ID);


            // Prüfen, ob Klasse existiert
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


            // Dem DocumenController den aktuellen Content übergeben
            $DocCon->set('Content',$Content);


            // Dem DocumenController die aktuellen Attribute mitgeben
            if(is_array($this->__Attributes) && count($this->__Attributes) > 0){
               $DocCon->setAttributes($this->__Attributes);
             // end if
            }


            // Standard-Methode des DocumentControllers ausführen
            $DocCon->transformContent();


            // Transformierten Inhalt zurückgeben lassen
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


         // Content zurückgeben
         return $Content;

       // end function
      }

    // end class
   }


   /**
   *  @package core::pagecontroller
   *  @class core_taglib_importdesign
   *
   *  This class implements the functionality of the core::importdesign tag. It generates a sub node
   *  from the template specified by the tag's attributes within the current APF DOM tree. Each
   *  importdesign tag can compose further tags.
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 28.12.2006<br />
   */
   class core_taglib_importdesign extends Document
   {

      /**
      *  @public
      *
      *  Constructor of the class. Sets the known taglibs.
      *
      *  @author Christian Schäfer
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
      *  Implements the onParseTime() method from the Document class. Includes the desired template
      *  as a new DOM node into the current APF DOM tree.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 31.12.2006 (Added pagepart option)<br />
      *  Version 0.3, 15.01.2007 (Now DocumentController tags are extracted first)<br />
      *  Version 0.4, 10.03.2007 (The Context can now be manipulated in the core:importdesign tag)<br />
      *  Version 0.5, 11.03.2007 (Introduced the "incparam" attribute to be able to control the template param via url)<br />
      *  Version 0.6, 26.10.2008 (Made the benchmark id generation more generic)<br />
      */
      function onParseTime(){

         // start timer
         $T = &Singleton::getInstance('benchmarkTimer');
         $id = '('.get_class($this).') '.$this->__ObjectID.'::onParseTime()';
         $T->start($id);

         // get attributes
         $Namespace = trim($this->__Attributes['namespace']);
         $Template = trim($this->__Attributes['template']);

         // read context
         if(isset($this->__Attributes['context'])){
            $this->__Context = trim($this->__Attributes['context']);
          // end if
         }

         // manager inc param
         if(isset($this->__Attributes['incparam'])){
            $IncParam = $this->__Attributes['incparam'];
          // end if
         }
         else{
            $IncParam = 'pagepart';
          // end else
         }

         // check, if the inc param is present in the current request
         if(substr_count($Template,'[') > 0){

            if(isset($_REQUEST[$IncParam]) && !empty($_REQUEST[$IncParam])){
               $Template = $_REQUEST[$IncParam];
             // end if
            }
            else{

               // read template attribute from inc param
               $PagepartStartPos = strpos($Template,'=');
               $PagepartEndPos = strlen($Template) - 1;
               $Template = trim(substr($Template,$PagepartStartPos + 1,($PagepartEndPos - $PagepartStartPos) - 1));

             // end else
            }

          // end if
         }

         // get content
         $this->__loadContentFromFile($Namespace,$Template);

         // parse document controller statements
         $this->__extractDocumentController();

         // extract further xml tags
         $this->__extractTagLibTags();

         // stop timer
         $T->stop($id);

       // end function
      }

    // end class
   }


   /**
   *  @package core::pagecontroller
   *  @class core_taglib_addtaglib
   *
   *  Represents the functionality of the core:addtaglib tag. Adds a further taglib to the known
   *  taglibs of the tag's parent object. This can be used to enhance the known tag list if a
   *  desired APF DOM node.
   *
   *  @author Christian Achatz
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
      *  Implements the onParseTime() method of the Document class. Adds the desired taglib to the
      *  parent object.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 10.11.2008 (Changed implementation. We now use getAttribute() instead of direct internal attribute addressing)<br />
      */
      function onParseTime(){
         $this->__ParentObject->addTagLib($this->getAttribute('namespace'),$this->getAttribute('prefix'),$this->getAttribute('class'));
       // end function
      }


      /**
      *  @public
      *
      *  Implements the Document's transform() method. Returns an empty string, because the addtaglib
      * tag should not generate output.
      *
      *  @author Christian Schäfer
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
   *  Represents a place holder within a template file. Can be filled within a documen controller
   *  using the setPlaceHolder() method.
   *
   *  @author Christian Achatz
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
      *  Implements the transform() method. Returns the content of the tag, that is set by a
      *  document controller using the baseController's setPlaceHolder() method.
      *
      *  @author Christian Schäfer
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
   *  Represents a reusable html fragment (template) within a template file. The tag's functionality
   *  can be extended by the &lt;template:addtaglib /&gt; tag. Use setPlaceHolder() to set a place
   *  holder's value and transformOnPlace() or transformTemplate() to generate the output.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.12.2006<br />
   *  Version 0.2, 10.11.2008 (Removed the IncludedTagLib behavior, because this lead to errors when including new taglibs with template:addtaglib.)<br />
   */
   class html_taglib_template extends Document
   {

      /**
      *  @private
      *  Indicates, if the template should be transformed on the place of definition. Default is false.
      */
      var $__TransformOnPlace = false;


      /**
      *  @public
      *
      *  Constructor of the class. Inituializes the known taglibs.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.12.2006<br />
      *  Version 0.2, 30.12.2006 (Added the template:config tag)<br />
      *  Version 0.3, 05.01.2007 (Added the template:addtaglib tag)<br />
      *  Version 0.4, 12.01.2007 (Removed the template:addtaglib tag)<br />
      *  Version 0.5, 03.03.2007 (Removed the "&" before the "new" operator)<br />
      *  Version 0.6, 21.04.2007 (Added the template:addtaglib tag again)<br />
      *  Version 0.7, 02.05.2007 (Removed the template:config tag)<br />
      */
      function html_taglib_template(){
         $this->__TagLibs[] = new TagLib('core::pagecontroller','template','placeholder');
         $this->__TagLibs[] = new TagLib('core::pagecontroller','template','addtaglib');
       // end function
      }


      /**
      *  @public
      *
      *  Implements the onParseTime() method from the coreObject class. Uses the __extractTagLibTags()
      *  function to parse the known taglibs.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.12.2006<br />
      *  Version 0.2, 31.12.2006<br />
      */
      function onParseTime(){
         $this->__extractTagLibTags();
       // end function
      }


      /**
      *  @public
      *
      *  API method to set a place holder's content within a document controller.
      *
      *  @param string $Name name of the place holder
      *  @param string $Value value of the place holder
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.12.2006<br />
      *  Version 0.2, 10.11.2008 (Removed check, if taglib class exists)<br />
      */
      function setPlaceHolder($Name,$Value){

         // declare the name of the place holder taglib to be flexible to future changes
         $TagLibModule = 'template_taglib_placeholder';

         // initialize place holder count
         $PlaceHolderCount = 0;

         // check, if tag has children
         if(count($this->__Children) > 0){

            // check, if template place holder exists within the children list
            foreach($this->__Children as $ObjectID => $Child){

               // check, if current child is a plece holder
               if(get_class($Child) == $TagLibModule){

                  // check, if current child is the desired place holder
                  if($Child->getAttribute('name') == $Name){

                     // set content of the placeholder
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

            // trow error, if no place holder with the desired name was found
            trigger_error('[html_taglib_template::setPlaceHolder()] No placeholder object with name "'.$Name.'" composed in current template for document controller "'.($this->__ParentObject->__DocumentController).'"! Perhaps tag library template:placeHolder is not loaded in template "'.$this->__Attributes['name'].'"!',E_USER_ERROR);
            exit();

          // end else
         }

         // thorw error, if no children are composed under the current tag
         if($PlaceHolderCount < 1){
            trigger_error('[html_taglib_template::setPlaceHolder()] There are no placeholders found for name "'.$Name.'" in template "'.($this->__Attributes['name']).'" in document controller "'.($this->__ParentObject->__DocumentController).'"!',E_USER_WARNING);
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Returns the content of the template. Can be used ti generate the template output within a
      *  document controller. Usage:
      *  <pre>
      *  $Template = &$this->__getTemplate('MyTemplate');
      *  $Template->setPlaceHolder('URL','http://adventure-php-framework.org');
      *  echo = $Template->transformTemplate();
      *  </pre>
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.12.2006<br />
      *  Version 0.2, 31.12.2006 (Removed parameter $this->__isVisible, because the parent object automatically removes the XML positioning tag on ransformation now)<br />
      *  Version 0.3, 02.02.2007 (Renamed method to transformTemplate() umgenannt. Removed visible marking finally from the class)<br />
      *  Version 0.4, 05.01.2007 (Added the template:addtaglib tag)<br />
      */
      function transformTemplate(){

         // create buffer for transformation
         $Content = (string)'';

         // create copy of the tag's content
         $Content = $this->__Content;

         // transform children
         if(count($this->__Children) > 0){

            foreach($this->__Children as $ObjectID => $Child){
               $Content = str_replace('<'.$ObjectID.' />',$Child->transform(),$Content);
             // end foreach
            }

          // end if
         }

         // return transformed content
         return $Content;

       // end function
      }


      /**
      *  @public
      *
      *  Indicates, that the template should be displayed on the place of definition.
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
      *  By default, the content of the template is returned as an empty string. This is because the
      *  html:template tag normally is used as a reusable fragment. If the transformOnPlace() function
      *  is called before, the content of the template is returned instead.
      *
      *  @return string $Content empty string or content of the tag
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 02.01.2007<br />
      *  Version 0.2, 12.01.2007 (An empty string is now returned)<br />
      *  Version 0.3, 19.05.2008 (Implemented the transformOnPlace() feature)<br />
      */
      function transform(){

         // checks, if transformOnPlace is activated
         if($this->__TransformOnPlace === true){
            return $this->transformTemplate();
          // end if
         }

         // return empty string
         return (string)'';

       // end function
      }

    // end class
   }


   /**
   *  @package core::pagecontroller
   *  @class template_taglib_placeholder
   *
   *  Implements the place holder tag with in a html:template tag. The tag does not hav further children.
   *
   *  @author Christian Achatz
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
      *  Implements the transform() method. Returns the content of the tag, that is set by a
      *  document controller using the html_taglib_template's setPlaceHolder() method.
      *
      *  @author Christian Achatz
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
   *  Represents the core:addtaglib functionality for the html:template tag. Includes further
   *  tag libs into the scope. Please see class core_taglib_addtaglib for more details.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 21.04.2007<br />
   *  Version 0.2, 10.11.2008 (Removed the registerTagLibModule() logic of the templates. Now the functionality is the same as core_taglib_addtaglib)<br />
   */
   class template_taglib_addtaglib extends core_taglib_addtaglib
   {

      function template_taglib_addtaglib(){
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
   *  @author Christian Schäfer
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
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      */
      function transformContent(){
      }


      /**
      *  @public
      *
      *  Implementiert eine Methode, mit der Platzhalter innerhalb des Inhalts eines "Document"s gesetzt werden können.<br />
      *  Hierzu ist die TagLib-Klasse "html_taglib_placeHolder" notwendig.<br />
      *
      *  @author Christian Schäfer
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


         // Anzahl der Platzhalter zählen
         $PlaceHolderCount = 0;


         // Prüfen, ob Kinder existieren
         if(count($this->__Document->__Children) > 0){

            // Platzhalter setzen
            foreach($this->__Document->__Children as $ObjectID => $Child){

               // Klassen mit dem Namen "$TagLibModule" aus den Child-Objekten des
               // aktuellen "Document"s aussuchen
               if(get_class($Child) == $TagLibModule){

                  // Klassen mit dem auf den Attribut Namen lautenden Namen suchen
                  // und den gewünschten Inhalt einsetzen
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


         // Prüfen, ob Kinder existieren
         if(count($this->__Document->__Children) > 0){

            // Templates aus dem aktuellen Document bereitstellen
            foreach($this->__Document->__Children as $ObjectID => $Child){

               // Klassen mit dem Namen "$TagLibModule" aus den Child-Objekten des
               // aktuellen "Document"s als Referenz zurückgeben
               if(get_class($Child) == $TagLibModule){

                  // Prüfen, ob das gefundene Template $Name heißt.
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
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.12.2006<br />
      *  Version 0.2, 03.01.2007 (Bug behoben, dass immer erstes Template referenziert wurde)<br />
      *  Version 0.3, 12.01.2006 (Von "__getContentTemplate" nach "__getTemplate" umbenannt, wg. Einführung von "__getForm")<br />
      */
      function &__getTemplate($Name){

         // Deklariert das notwendige TagLib-Modul
         $TagLibModule = 'html_taglib_template';


         // Falls TagLib-Modul nicht vorhanden -> Fehler!
         if(!class_exists($TagLibModule)){
            trigger_error('['.get_class($this).'::__getTemplate()] TagLib module "'.$TagLibModule.'" is not loaded!',E_USER_ERROR);
          // end if
         }


         // Prüfen, ob Kinder existieren
         if(count($this->__Document->__Children) > 0){

            // Templates aus dem aktuellen Document bereitstellen
            foreach($this->__Document->__Children as $ObjectID => $Child){

               // Klassen mit dem Namen "$TagLibModule" aus den Child-Objekten des
               // aktuellen "Document"s als Referenz zurückgeben
               if(get_class($Child) == $TagLibModule){

                  // Prüfen, ob das gefundene Template $Name heißt.
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
      *  Prüft, ob ein Platzhalter im aktuellen Template vorhanden ist.<br />
      *
      *  @param string $Name; Name des Platzhalters
      *  @return bool $Exists; true | false
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 11.03.2007<br />
      */
      function __placeholderExists($Name){

         // Kinder des aktuellen Dokuments holen
         $Children = &$this->__Document->getByReference('Children');

         // Kinder prüfen
         foreach($Children as $Key => $Child){

            // Auf Platzhalter-Kinder prüfen
            if(get_class($Child) == 'html_taglib_placeholder'){

               // Auf Namen prüfen
               if($Child->getAttribute('name') == $Name){
                  return true;
                // end if
               }

             // end if
            }

          // end foreach
         }

         // False zurückgeben
         return false;

       // end function
      }


      /**
      *  @private
      *
      *  Prüft, ob ein Platzhalter im aktuellen Template vorhanden ist.<br />
      *
      *  @param string $Name; Name des Platzhalters
      *  @return bool $Exists; true | false
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 11.03.2007<br />
      */
      function __templatePlaceholderExists(&$Template,$Name){

         // Kinder des Templates holen
         $Children = &$Template->getByReference('Children');

         // Kinder prüfen
         foreach($Children as $Key => $Child){

            // Auf Platzhalter-Kinder prüfen
            if(get_class($Child) == 'template_taglib_placeholder'){

               // Auf Namen prüfen
               if($Child->getAttribute('name') == $Name){
                  return true;
                // end if
               }

             // end if
            }

          // end foreach
         }

         // False zurückgeben
         return false;

       // end function
      }

    // end class
   }
?>
<?php
   import('core::singleton','Singleton');
   import('core::frontcontroller','Frontcontroller');


   /**
   *  @package tools::link
   *  @class frontcontrollerLinkHandler
   *
   *  Implementiert den linkHandler für den Gebrauch in Frontcontroller-Anwendungen neu.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 10.02.2007<br />
   *  Version 0.2, 24.02.2007 (Neue Methode 'generateActionLink' hinzugefügt)<br />
   *  Version 0.3, 08.07.2007 (Vollständige Neuimplementierung auf Grund des Umbaus der Request-Filter)<br />
   *  Version 0.4, 29.10.2007 (Methode generateURLParams() hinzugefügt)<br />
   */
   class frontcontrollerLinkHandler
   {

      function frontcontrollerLinkHandler(){
      }


      /**
      *  @public
      *  @static
      *  @since 0.4
      *
      *  Erzeugt ein Array, das der Methode generateLink() mitgegeben werden kann um eine FC-URL<br />
      *  zu manipulieren.<br />
      *
      *  @param string $ActionNamespace; Namespace der Action
      *  @param string $ActionName; Name der Action
      *  @param array $ActionParams; Parameter, die der Action mitgegeben werden sollen
      *  @param bool $RewriteLink; Definiert, ob die URL in rewriteter Form zurückgegeben werden soll (true | false)
      *  @return array $ActionURLParams; Array mit den Parametern, um eine Action in der URL zu manipulieren
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.10.2007<br />
      *  Version 0.2, 21.06.2008 (Introduced the Registry to retrieve the URLRewriting information)<br />
      */
      function generateURLParams($ActionNamespace,$ActionName,$ActionParams = array(),$URLRewriting = null){

         //
         // Timer starten
         //
         $T = &Singleton::getInstance('benchmarkTimer');
         $ID = 'frontcontrollerLinkHandler::generateURLParamsByAction('.xmlParser::generateUniqID().')';
         $T->start($ID);


         //
         // Parameter konfigurieren
         //

         // Instanz des Frontcontrollers holen
         $fC = &Singleton::getInstance('Frontcontroller');


         // Action-URL-Keyword
         $Config__ActionKeyword = $fC->get('ActionKeyword');


         // Namespace-Keyword-Trenner
         $Config__NamespaceKeywordDelimiter = $fC->get('NamespaceKeywordDelimiter');


         // Namespace-URL-Trenner
         $Config__NamespaceURLDelimiter = $fC->get('NamespaceURLDelimiter');


         // set URLRewrite
         if($URLRewriting === null){
            $Reg = &Singleton::getInstance('Registry');
            $URLRewriting = $Reg->retrieve('apf::core','URLRewriting');
          // end if
         }


         // URL-Keyword-Class-Trenner
         if($URLRewriting == true){
            $Config__KeywordClassDelimiter = $fC->get('URLRewritingKeywordClassDelimiter');
          // end if
         }
         else{
            $Config__KeywordClassDelimiter = $fC->get('KeywordClassDelimiter');
          // end else
         }


         // Standard Keyword-Klasse-Trenner definieren
         $Config__NormalKeywordClassDelimiter = $fC->get('KeywordClassDelimiter');


         // URL-Trenner für Actions im URL-Rewriting-Fall
         $Config__RewriteURLDelimiter = '/~/';


         // URL-Trenner für KEY und VALUE
         if($URLRewriting == true){
            $Config__KeyValueDelimiter = $fC->get('URLRewritingKeyValueDelimiter');
          // end if
         }
         else{
            $Config__KeyValueDelimiter = $fC->get('KeyValueDelimiter');
          // end else
         }


         // URL-Trenner zwischen verschiedenen KEY-VALUE-Paaren
         if($URLRewriting == true){
            $Config__InputDelimiter = $fC->get('URLRewritingInputDelimiter');
          // end if
         }
         else{
            $Config__InputDelimiter = $fC->get('InputDelimiter');
          // end else
         }


         //
         // Link zusammensetzen
         //

         // Offset generieren
         $Offset = str_replace('::','_',$ActionNamespace).$Config__NamespaceKeywordDelimiter.$Config__ActionKeyword.$Config__KeywordClassDelimiter.$ActionName;


         // Params
         $Params = array();

         if(count($ActionParams) > 0){

            foreach($ActionParams as $Key => $Value){
               $Params[] = $Key.$Config__KeyValueDelimiter.$Value;
             // end foreach
            }

          // end if
         }


         //
         // Array erzeugen
         //
         $ActionURLParams = array(
                                  $Offset => implode($Config__InputDelimiter,$Params)
         );


         //
         // Timer stoppen
         //
         $T->stop($ID);


         //
         // Array zurückgeben
         //
         return $ActionURLParams;

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Implementiert die Link-Creator-Funktion für Frontcontroller-Anwendungen. Bezieht alle<br />
      *  Actions vom Typ 'pre' und 'post', bei denen der Parameter KeepInURL = true ist, mit ein.<br />
      *  <br />
      *  Die Option $NewParams bestimmt, welche Parameter der URL gelöscht, welche anders gesetzt,<br />
      *  und welche belassen oder hinzugefügt werden. Aus einer URL der Form<br />
      *  <br />
      *    http://adventure-php-framework.org/Seite/ChangeLog/benchmarkreport/true/param1/value1/param2/value2<br />
      *  <br />
      *  wird durch Übergabe des Arrays<br />
      *  <br />
      *  array(
      *        'modules_guestbook_biz-action:LoadEntryList' => 'pagesize:20|pager:false|adminview:true',
      *        'Seite' => 'Guestbook'
      *       );
      *  <br />
      *  im Rewrite-Modus die URL<br />
      *  <br />
      *    http://adventure-php-framework.org/Seite/Guestbook/benchmarkreport/true/param1/value1/param2/value2/~/sites_demosite_biz-action/LoadModel/test/test/test2/test2/~/modules_guestbook_biz-action/LoadEntryList/pagesize/20/pager/false/adminview/true<br />
      *  <br />
      *  erzeugt, im "normalen" Modus die URL
      *  <br />
      *    http://adventure-php-framework.org/?Seite=Guestbook&benchmarkreport=true&param1=value1&param2=value2&sites_demosite_biz-action:LoadModel=test:test|test2:test2&modules_guestbook_biz-action:LoadEntryList=pagesize:20|pager:false|adminview:true.
      *  <br />
      *
      *  @param string $URL; Aktuelle URL
      *  @param array $NewParams; URL-Parameter, zur Veränderung der URL
      *  @param bool $RewriteLink; Definiert, ob die URL in rewriteter Form zurückgegeben werden soll (true | false)
      *  @return string $FinishedURL; Fertige URL
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 24.02.2007<br />
      *  Version 0.2, 08.07.2007 (Vollständige Neuimplementierung auf Grund des Umbaus der Request-Filter)<br />
      *  Version 0.3, 26.08.2007 (URL wird nun auf is_string() geprüft; URL-Parameter akzeptieren keine mehrdimensionales Arrays!)<br />
      *  Version 0.4, 09.11.2007 (Fix für Problem mit DUMMY-Actions und Filterung für nicht in der URL gewollte Actions)<br />
      *  Version 0.5, 10.01.2008 (Fix für Problem mit DUMMY-Action bei APPS__URL_REWRITING = false)<br />
      *  Version 0.6, 21.06.2008 (Introduced the Registry to retrieve the URLRewriting information)<br />
      */
      function generateLink($URL,$NewParams = array(),$URLRewriting = null){

         //
         // Timer starten
         //
         $T = &Singleton::getInstance('benchmarkTimer');
         $ID = 'frontcontrollerLinkHandler::generateLink('.md5($URL).')';
         $T->start($ID);


         //
         // Prüfen, ob $URL ein String ist
         //
         if(!is_string($URL)){
            trigger_error('[frontcontrollerLinkHandler::generateLink()] Given url is not a string!',E_USER_WARNING);
            $URL = strval($URL);
          // end if
         }


         //
         // Ampersands decodieren
         //
         $URL = str_replace('&amp;','&',$URL);


         //
         // Parameter konfigurieren
         //

         // Instanz des Frontcontrollers holen
         $fC = &Singleton::getInstance('Frontcontroller');


         // Action-URL-Keyword
         $Config__ActionKeyword = $fC->get('ActionKeyword');


         // Namespace-Keyword-Trenner
         $Config__NamespaceKeywordDelimiter = $fC->get('NamespaceKeywordDelimiter');


         // Namespace-URL-Trenner
         $Config__NamespaceURLDelimiter = $fC->get('NamespaceURLDelimiter');


         // set URLRewrite
         if($URLRewriting === null){
            $Reg = &Singleton::getInstance('Registry');
            $URLRewriting = $Reg->retrieve('apf::core','URLRewriting');
          // end if
         }


         // URL-Keyword-Class-Trenner
         if($URLRewriting == true){
            $Config__KeywordClassDelimiter = $fC->get('URLRewritingKeywordClassDelimiter');
          // end if
         }
         else{
            $Config__KeywordClassDelimiter = $fC->get('KeywordClassDelimiter');
          // end else
         }


         // Standard Keyword-Klasse-Trenner definieren
         $Config__NormalKeywordClassDelimiter = $fC->get('KeywordClassDelimiter');


         // URL-Trenner für Actions im URL-Rewriting-Fall
         $Config__RewriteURLDelimiter = '/~/';


         //
         // Paramater-Array initialisieren
         //
         $Params = array();


         //
         // URL parsen
         //
         $ParsedURL = parse_url($URL);

         // Query-String zerlegen
         if(!isset($ParsedURL['query'])){
            $ParsedURL['query'] = (string)'';
          // end if
         }

         // Path vorgeben, falls nicht vorhanden
         if(!isset($ParsedURL['path'])){
            $ParsedURL['path'] = (string)'';
          // end if
         }


         //
         // URL zerlegen
         //
         if($URLRewriting == true){

            //
            // Prüfen, ob Actions im Path enthalten sind
            //
            if(substr_count($ParsedURL['path'],$Config__NamespaceKeywordDelimiter.$Config__ActionKeyword.$Config__KeywordClassDelimiter) > 0){

               // Prüfen ob mehrere URL-Teile enthalten sind
               if(substr_count($ParsedURL['path'],$Config__RewriteURLDelimiter) > 0){

                  // URL nach /~/ trennen und parsen
                  $URLPathParts = explode($Config__RewriteURLDelimiter,$ParsedURL['path']);

                  for($i = 0; $i < count($URLPathParts); $i++){

                     // Nur in Parameter-Array aufnehmen, wenn keine Frontcontroller-Action enthalten ist
                     if(substr_count($URLPathParts[$i],$Config__NamespaceKeywordDelimiter.$Config__ActionKeyword.$Config__KeywordClassDelimiter) < 1){

                        // URLPath zerlegen
                        $Params = array_merge($Params,frontcontrollerLinkHandler::createArrayFromRequestString($URLPathParts[$i]));

                      // end if
                     }
                     else{

                        // Action-Anweisung erkennen und in Params-Array als Dummy einsetzen (wichtig für Merge!)
                        $ActionURLParts = explode('/',$URLPathParts[$i]);
                        $Params = array_merge($Params,array(trim($ActionURLParts[0].$Config__NormalKeywordClassDelimiter.$ActionURLParts[1]) => ''));

                      // end else
                     }

                   // end for
                  }

                // end if
               }

             // end if
            }
            else{

               // URLPath zerlegen
               $Params = array_merge($Params,frontcontrollerLinkHandler::createArrayFromRequestString($ParsedURL['path']));

             // end else
            }

          // end if
         }
         else{

            // URL anhand von & und = zerlegen
            $SplitURL = explode('&',$ParsedURL['query']);

            // Parameter der Query zerlegen
            $SplitParameters = array();

            for($i = 0; $i < count($SplitURL); $i++){

               // Nur Parameter größer 3 Zeichen (z.B. a=b) beachten
               if(strlen($SplitURL[$i]) > 3){

                  // Position des '=' suchen
                  $EqualSign = strpos($SplitURL[$i],'=');

                  // Array mit den Parametern als Key => Value - Paar erstellen, falls URL-Teil keine Action-Anweisung ist
                  if(substr_count($SplitURL[$i],$Config__NamespaceKeywordDelimiter.$Config__ActionKeyword.$Config__KeywordClassDelimiter) < 1){
                     $Params[substr($SplitURL[$i],0,$EqualSign)] = substr($SplitURL[$i],$EqualSign+1,strlen($SplitURL[$i]));
                   // end if
                  }
                  else{

                     // Action-Anweisung als Dummy im Parameter-Satz lassen (DUMMY in Version > 0.4 entfernt!)
                     $Params[substr($SplitURL[$i],0,$EqualSign)] = '';

                   // end else
                  }

                // end if
               }

             // end for
            }

          // end else
         }

         //echo '<br />$Params: <br />'.printObject($Params);


         //
         // Actions holen
         //
         $Actions = &$fC->getActions();
         //echo '<br />Actions count: '.count($Actions);

         // Result-Array initialisieren
         $ActionParams = array();


         // Array mit relevanten ActionLinks erzeugen
         foreach($Actions as $Key => $DUMMY){

            $Input = &$Actions[$Key]->getInput();
            /*echo '<br />lang: '.*/$Input->getAttribute('lang');
            /*echo '<br />$Array_Value: "'.*/$Input->getAttributesAsString(false)/*.'"'*/;


            // ActionNamespace von Standard-Pfad "actions" befreien
            $ActionNamespace = str_replace('::actions','',$Actions[$Key]->get('ActionNamespace'));

            // Action-Offset erzeugen
            $Array_Key = str_replace('::',$Config__NamespaceURLDelimiter,$ActionNamespace).$Config__NamespaceKeywordDelimiter.$Config__ActionKeyword.str_replace($Config__KeywordClassDelimiter,$Config__NormalKeywordClassDelimiter,$Config__KeywordClassDelimiter).($Actions[$Key]->get('ActionName'));


            // Prüfen, ob Action in der URL erhalten bleiben soll
            if($Actions[$Key]->get('KeepInURL') == true){

               //echo '<br />Action '.get_class($Actions[$Key]).' ('.$Array_Key.') should be kept in url';

               // Input als String erzeugen
               $Input = &$Actions[$Key]->getInput();
               $Array_Value = $Input->getAttributesAsString(false);

               // Parameter mergen
               $ActionParams = array_merge_recursive($ActionParams,array($Array_Key => $Array_Value));

             // end if
            }
            else{

               //echo printObject($ActionParams);
               //echo '<br />Action '.get_class($Actions[$Key]).' ('.$Array_Key.') should be removed in url<br />';
               //echo printObject($ActionParams);


               // Lösche Platzhalte in der URL, da Action nicht nachgeführt werden soll
               unset($Params[$Array_Key]);

             // end else
            }

          // end foreach
         }

         //echo '<br />$ActionParams: <br />'.printObject($ActionParams);


         //
         // Actions zu Parameter mergen
         //
         $Params = array_merge($Params,$ActionParams);
         //echo '<br />Merged Params:<br />'.printObject($Params);


         //
         // Erzeugtes und übergebenes Parameter-Set zusammenführen (dadurch können Löschungen realisiert werden)
         //
         $FinalParams = array_merge($Params,$NewParams);
         //echo '<br />Final Params:<br />'.printObject($FinalParams);


         //
         // Query-String an Hand der gemergten Parameter erzeugen
         //
         $Query = (string)'';

         if($URLRewriting == true){

            // Anzahl an Parameter ermitteln
            $FinalParamsCount = count($FinalParams);
            $CurrentOffset = 1;


            // URL zusammensetzen
            foreach($FinalParams as $Key => $Value){

               // Nur Keys mit einer Länge > 1 und Values mit einer Länge > 0 betrachten, damit
               // ein array('Test' => '') eine Löschung bedeutet.
               // Prüfen, ob $Value ein Array ist und dieses ablehnen!
               if(!is_array($Value)){

                  if(strlen($Key) > 1 && strlen($Value) > 0){

                     // Im Rewrite-Fall Action-Delimiter einfügen
                     if(substr_count($Key,$Config__NamespaceKeywordDelimiter.$Config__ActionKeyword) > 0){

                        //echo '<br />$CurrentOffset: '.$CurrentOffset;
                        //echo '<br />$FinalParamsCount: '.$FinalParamsCount;

                        // Prüfen, wo der URL-Teil eingesetzt werden soll
                        if($CurrentOffset < $FinalParamsCount){
                           $Query .= $Config__RewriteURLDelimiter.trim($Key).'/'.trim($Value).$Config__RewriteURLDelimiter;
                         // end if
                        }
                        else{
                           $Query .= $Config__RewriteURLDelimiter.trim($Key).'/'.trim($Value);
                         // end else
                        }

                      // end if
                     }
                     else{
                        $Query .= '/'.trim($Key).'/'.trim($Value);
                      // end else
                     }

                   // end if
                  }

                // end if
               }

               // Nummer des aktuellen Offsets inkrementieren
               $CurrentOffset++;

             // end foreach
            }


            // Query rewriten und Vorkommen von /~// ersetzen
            $Replace = array(
                             $Config__RewriteURLDelimiter.$Config__RewriteURLDelimiter => $Config__RewriteURLDelimiter,
                             $Config__RewriteURLDelimiter.'/' => $Config__RewriteURLDelimiter,
                             ':' => '/',
                             '|' => '/'
                            );
            $Query = strtr($Query,$Replace);

          // end if
         }
         else{

            // URL zusammensetzen
            foreach($FinalParams as $Key => $Value){

               // Nur Keys mit einer Länge > 1 und Values mit einer Länge > 0 betrachten, damit
               // ein array('Test' => '') eine Löschung bedeutet.
               // Prüfen, ob $Value ein Array ist und dieses ablehnen!
               if(!is_array($Value)){
                  if(strlen($Key) > 1 && strlen($Value) > 0){

                     // '?' als erstes Bindezeichen setzen
                     if(strlen($Query) == 0){
                        $Query .= '?';
                      // end if
                     }
                     else{
                        $Query .= '&';
                      // end else
                     }

                     // 'Key' => 'Value' - Paar zusammensetzen
                     $Query .= trim($Key).'='.trim($Value);

                   // end if
                  }

                // end if
               }

             // end foreach
            }


            // Ampersands codieren
            $Query = str_replace('&','&amp;',$Query);

          // end else
         }


         // URL generieren
         $HostPart = (string)'';

         // Falls Schema und Host gegeben, diese einbinden
         if(isset($ParsedURL['scheme']) && isset($ParsedURL['host'])){
            $HostPart .= $ParsedURL['scheme'].'://'.$ParsedURL['host'];
          // end if
         }

         // Falls nur Host gegeben, diesen einsetzen
         if(!isset($ParsedURL['scheme']) && isset($ParsedURL['host'])){
            $HostPart .= '/'.$ParsedURL['host'];
          // end if
         }


         // URL final zusammensetzen
         if($URLRewriting == true){

            // Führenden / entfernen
            if(substr($Query,0,1) == '/'){
               $Query = substr($Query,1);
             // end if
            }

            // URL zusammensetzen
            $FinishedURL = $HostPart.'/'.$Query;

          // end if
         }
         else{
            $FinishedURL = $HostPart.$ParsedURL['path'].$Query;
          // end else
         }


         // Timer stoppen
         $T->stop($ID);


         // Manipulierte URL zurückgeben
         return $FinishedURL;

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Erzeugt ein Array aus einem Rewrite-URL-String.<br />
      *
      *  @param string $RequestString; URL-String oder Teil-String
      *  @return array $URLParams; Array mit URL-Parametern
      *
      *  @author Christian W. Schäfer
      *  @version
      *  Version 0.1, 07.07.2007<br />
      */
      function createArrayFromRequestString($RequestString){

         // Ergebnis-Array initialisieren
         $URLParams = array();


         // Führenden Slash entfernen
         if(substr($RequestString,0,1) == '/'){
            $RequestString = substr($RequestString,1);
          // end if
         }


         // Parameter zerlegen
         $ParamsArray = explode('/',strip_tags($RequestString));


         // Parameter aus String extrahieren
         if(count($ParamsArray) > 0){

            // Zählvariable auf Null setzen
            $x = 0;

            // Parameter generieren
            while($x <= (count($ParamsArray) - 1)){

               if(isset($ParamsArray[$x + 1])){
                  $URLParams[$ParamsArray[$x]] = $ParamsArray[$x + 1];
                // end if
               }

               // Offset-Zähler um 2 erhöhen
               $x = $x + 2;

             // end while
            }

          // end if
         }


         // Parameter zurückgeben
         return $URLParams;

       // end function
      }

    // end class
   }
?>
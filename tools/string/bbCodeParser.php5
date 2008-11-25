<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   /**
   *  @namespace tools::string
   *  @class bbCodeParser
   *  @deprecated
   *
   *  <pre>Stellt Methoden der Text-Formatierungen im bbCode-Style zur Verfügung.
   *  Es werden folgende Möglichkeiten unterstützt:
   *
   *    1) Schriftformatierungen
   *       - [f](..)[/f]: Text in fett darstellen.
   *       - [k](..)[/k]: Text in kursiv darstellen.
   *       - [u](..)[/u]: Text in unterstrichen darstellen.
   *
   *    2) Schriftgrößen
   *       - [x](..)[/x]: Text in Schriftgröße x darstellen.
   *
   *    3) Schriftfarbe
   *       - [abc](..)[/abc]: Text in Schriftfarbe abc darstellen.
   *
   *    4) Absatzformatierungen
   *       - [mitte](..)[/mitte]: Text wird mittig dargestellt.
   *       - [links](..)[/links]: Text wird linksbündig dargestellt (standard).
   *       - [rechts](..)[/rechts]: Text wird rechtsbündig dargestellt.
   *       - [block](..)[/block]: Text wird in einem Text-Block, der zentriert ist,
   *         links und rechts jeweils gleiche Ränder vom Seitenrand aufweist angezeigt.
   *       - [tab]: Text-Anfang wird mit einem Tab (Einrückung) versehen.
   *       - [ftrechts](..)[/ftrechts]: Eingeschlossenes Objekt (meist Bilder) werden
   *         vom Text rechts umflossen.
   *       - [ftlinks](..)[/ftlinks]: Eingeschlossenes Objekt (meist Bilder) werden
   *         vom Text links umflossen.
   *
   *    5) Grafikelemente
   *       - [Bild=abc.gif]: Hier wird das Bild abc.gif aus der Standard-Media-Library eingesetzt.
   *       - [RMBild=abc.gif,Groesse=50]: Hier wird das Bild abc.gif eingesetzt und vom Resizer auf
   *         50% der ursprünglichen Größe verkleinert. Die Größe ist in % anzugeben. Werte > 100 sind
   *         demnach Vergrößerungen, Werte < 100 Verkleinerungen.
   *
   *    6) Verweise
   *       - [Link=(..),Hilfe=(..)](..)[/Link]: Generiert einen seiteninternen Link, der im aktuellen
   *         Fenster und innerhalb des Rahmens ausgeführt wird.
   *       - [Linkext=(..),Hilfe=(..)](..)[/Linkext]: Generiert einen Link mit externem Ziel, der in
   *         einem neuen Fenster ausgeführt wird.
   *       - [Download=(..),Hilfe=(..)](..)[/Download]: Generiert einen Download-Link, der die angegebene
   *         Datei (aus der Standard-Media-Library) zum Download anbietet. Der Link wird in einem neuen
   *         Fenster angeboten
   *
   *    7) Listen
   *       - [LP](..)[/LP]: Generiert einen Listenpunkt innerhalb einer Liste
   *       - [Liste Bild=(..)](..)[/Liste]: Generiert mit den enthaltenen Listenpunkten eine Liste mit
   *         Aufzählungsbild.
   *       - [Liste Typ=Strich](..)[/Liste]: Generiert mit den enthaltenen Listenpunkten eine Liste mit
   *         Aufzählungsstrichen.
   *       - [Liste Typ=Kreis](..)[/Liste]: Generiert mit den enthaltenen Listenpunkten eine Liste mit
   *         Aufzählungs-Kreisen.
   *       - [Liste Typ=Zahl](..)[/Liste]: Generiert mit den enthaltenen Listenpunkten eine Liste mit
   *         Gliederungspunkten.
   *       - [Liste](..)[/Liste]: Generiert mit den enthaltenen Listenpunkten eine einfache Liste.
   *
   *    8) Extensions
   *       Der bbCodeParser ermöglicht es Erweiterungen einzubinden. Dies sind in der Konfigurations-Datei
   *       "{ENVIRONMENT}_bbcpext.txt" im Namespace "config::modules::string::{CONTEXT}::iniconfig"
   *       in getrennten Sektionen zu definieren.
   *
   *       Beispiel:
   *          [News]
   *          Namespace = "sites::apfexample::biz::bbcpext"
   *          Modul = "NewsTag"
   *          Klasse = "NewsTagParser"
   *          Methode = "parseNewsTag"
   *
   *       Die Implementierungen der Extensions sollten im zugehörigen Sites-Namespace liegen. Im Fall von globalen
   *       Extensions können diese auch im string-Namespace unter dem Ordner "bbcpext" abgelegt werden.
   *  </pre>
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 2004<br />
   *  Version 0.2, 2004<br />
   *  Version 0.3, 10.01.2005<br />
   *  Version 0.4, 18.01.2005<br />
   *  Version 0.5, 31.01.2005<br />
   *  Version 0.6, 20.02.2005<br />
   *  Version 0.7, 14.03.2005<br />
   *  Version 0.8, 01.12.2005<br />
   *  Version 0.9, 06.02.2006<br />
   *  Version 1.0, 12.03.2006<br />
   *  Version 1.1, 01.04.2007 (iniHandler bereinigt)<br />
   *  Version 1.2, 03.01.2008 (Auf FrontController umgestellt)<br />
   */
   class bbCodeParser extends coreObject
   {

      function bbCodeParser(){
      }


      /**
      *  @public
      *
      *  Parst einen übergebenen Text.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 06.02.2006<br />
      *  Version 0.2, 10.02.2006<br />
      *  Version 0.3, 12.03.2006<br />
      */
      function parseText($Text){

         $Text = $this->__parseLetterFormat($Text);
         $Text = $this->__parseFontSizeFormat($Text);
         $Text = $this->__parseNewLine($Text);
         $Text = $this->__parseFontColorFormat($Text);
         $Text = $this->__parseArticleFormat($Text);
         $Text = $this->__parsePictureTags($Text);
         $Text = $this->__parseListTags($Text);
         $Text = $this->__parseLinkTags($Text);
         $Text = $this->__parseBlanks($Text);
         //$Text = $this->__parseSpecialCharacters($Text);
         $Text = $this->__parseExtensionTags($Text);

         return $Text;

       // end function
      }


      /**
      *  @private
      *
      *  Wandelt Sonderzeichen in ihre HTML-Entsprechungen um.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 06.02.2006<br />
      */
      function __parseSpecialCharacters($Text){

         $Text = str_replace('&amp;','&',$Text);  // '&amp;' durch '&' ersetzen um Links zu ermöglichen
         $Text = htmlentities($Text);
         return $Text;

       // end function
      }


      /**
      *  @private
      *
      *  Parst Standard-Formatierungen (Fett, Kursiv, Unterstrichen).<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 06.02.2006<br />
      */
      function __parseLetterFormat($Text){

         $Formate = array('[f]' => '<strong>',
                          '[F]' => '<strong>',
                          '[/f]' => '</strong>',
                          '[/F]' => '</strong>',
                          '[k]' => '<em>',
                          '[K]' => '<em>',
                          '[/k]' => '</em>',
                          '[/K]' => '</em>',
                          '[u]' => '<u>',
                          '[U]' => '<u>',
                          '[/u]' => '</u>',
                          '[/U]' => '</u>'
                         );
         return strtr($Text,$Formate);

       // end function
      }


      /**
      *  @private
      *
      *  Parst Schrift-Größen-Formate gemäß der Konfiguration.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 06.02.2006<br />
      *  Version 0.2, 23.12.2007 (Konfigurationssektionsnamen angepasst)<br />
      */
      function __parseFontSizeFormat($Text){

         $Config = &$this->__getConfiguration('tools::string','fonttags');
         $FontSizes = $Config->getSection('FontSize');

         foreach($FontSizes as $Key => $Value){
            $Text = strtr($Text,array('['.$Key.']' => '<font style="font-size: '.$Value.';">', '[/'.$Key.']' => '</font>'));
          // end forech
         }

         return $Text;

       // end function
      }


      /**
      *  @private
      *
      *  Parst Schrift-Größen-Formate gemäß der Konfiguration.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 06.02.2006<br />
      *  Version 0.2, 23.12.2007 (Konfigurationssektionsnamen angepasst)<br />
      */
      function __parseFontColorFormat($Text){

         $Config = &$this->__getConfiguration('tools::string','fonttags');
         $Colors = $Config->getSection('Color');

         foreach($Colors as $Key => $Value){
            $Text = strtr($Text,array('['.$Key.']' => '<font style="color: '.$Value.';">', '[/'.$Key.']' => '</font>'));
          // end forech
         }

         return $Text;

       // end function
      }


      /**
      *  @private
      *
      *  Parst Zeilen-Umbrüche.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 06.02.2006<br />
      */
      function __parseNewLine($Text){
         //return str_replace("\n","<br />\n",$Text);
         return nl2br($Text);
       // end function
      }


      /**
      *  @private
      *
      *  Parst Absatz-Formate.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 06.02.2006<br />
      */
      function __parseArticleFormat($Text){

         $Formate = array('[links]' => "<div style=\"width: 100%; text-align: left;\">",
                          '[/links]' => '</div>',
                          '[rechts]' => "<div style=\"width: 100%; text-align: right;\">",
                          '[/rechts]' => '</div>',
                          '[mitte]' => '<center>',
                          '[/mitte]' => '</center>',
                          '[block]' => "<span style=\"width: 100%; text-align: left; padding-left: 40px; padding-right: 40px;\">",
                          '[/block]' => '</span>',
                          '[tab]' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                          '[ftrechts]' => "<span style=\"text-align: left; float: left; padding-top: 5px; padding-right: 10px; padding-bottom: 5px;\">",
                          '[/ftrechts]' => '</span>',
                          '[ftlinks]' => "<span style=\"text-align: right; float: right; padding-top: 5px; padding-left: 10px; padding-bottom: 5px;\">",
                          '[/ftlinks]' => '</span>'
                         );
         return strtr($Text,$Formate);

       // end function
      }


      /**
      *  @private
      *
      *  Parst Leerzeichen.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 06.02.2006<br />
      */
      function __parseBlanks($Text){
         return strtr($Text,array('  ' => '&nbsp;&nbsp;'));
       // end function
      }


      /**
      *  @private
      *
      *  Parst Bild-Formatierungen.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 06.02.2006<br />
      *  Version 0.2, 17.03.2006<br />
      *  Version 0.3, 03.01.2008 (Bildauslieferung auf FrontController umgestellt)<br />
      *  Version 0.4, 21.06.2008 (Removed APPS__URL_REWRITING and APPS__URL_PATH)<br />
      *  Version 0.5, 21.07.2008 (Extracted the image tag generation to an extra callback function due to changes to the image resizer module)<br />
      */
      function __parsePictureTags($Text){
         $Text = preg_replace_callback('=\[[B|b]ild\=([^\[]*?)\]=',array('bbCodeParser','imageCallback'),$Text);
         return preg_replace_callback('=\[[R|r][M|m][B|b]ild\=([^\[]*?),[G|g]roesse\=([^\[]*?)\]=',array('bbCodeParser','imageCallback'),$Text);
       // end function
      }


      /**
      *  @private
      *
      *  Parst Link-Tags.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 06.02.2006<br />
      *  Version 0.2, 13.03.2006  (linkint-Tag entfernt, link-Tag wurde ohne callback realisiert)<br />
      *  Version 0.3, 14.03.2006  (link-Tag wird jetzt im aktuellen Browser-Fenster ausgeführt)<br />
      */
      function __parseLinkTags($Text){

         $Text = preg_replace('=\[[l|L]ink\=([^\[]*?),[H|h]ilfe\=([^\[]*?)\]([^\[]*?)\[\/[l|L]ink\]=',"<a href=\"\\1\" title=\"\\2\" linkrewrite=\"false\">\\3</a>",$Text);
         $Text = preg_replace('=\[[l|L]inkext\=([^\[]*?),[H|h]ilfe\=([^\[]*?)\]([^\[]*?)\[\/[l|L]inkext\]=',"<a href=\"\\1\" title=\"\\2\" target=\"_blank\" linkrewrite=\"false\">\\3</a>",$Text);
         return preg_replace_callback('=\[[d|D]ownload\=([^\[]*?),[H|h]ilfe\=([^\[]*?)\]([^\[]*?)\[\/[d|D]ownload\]=',array('bbCodeParser','linkDLCallback'),$Text);

       // end function
      }


      /**
      *  @private
      *
      *  Parst Link-Tags.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 06.02.2006<br />
      *  Version 0.2, 22.12.2006 (Listen-Parsing geändert, dass Listen immer sauber geparst werden)<br />
      *  Version 0.3, 21.06.2008 (Removed APPS__URL_PATH and replaced it with a value from the Registry)<br />
      */
      function __parseListTags($Text){

         // define formats
         $Formate = array('[LP]' => '<li>',
                          '[/LP]' => '</li>',
                          '[lp]' => '<li>',
                          '[/lp]' => '</li>',
                          '[Lp]' => '<li>',
                          '[/Lp]' => '</li>',
                          '[lP]' => '<li>',
                          '[/lP]' => '</li>'
                         );
         $Text = strtr($Text,$Formate);

         // retrieve values from Registry
         $Reg = &Singleton::getInstance('Registry');
         $URLBasePath = $Reg->retrieve('apf::core','URLBasePath');

         // Bild-Liste parsen
         $Text = preg_replace('=\[[L|l]iste [B|b]ild\=([^\[]*?)\]=',"<ul style=\"list-style-image: url(".$URLBasePath."/bild.php?Bild=\\1);\">",$Text);

         // Strich-Liste parsen
         $Text = preg_replace('=\[[L|l]iste [T|t]yp\=[S|s]trich\]=','<ul style="list-style-type: circle;">',$Text);

         // Kreis-Liste parsen
         $Text = preg_replace('=\[[L|l]iste [T|t]yp\=[K|k]reis\]=','<ul style="list-style-type: disk;">',$Text);

         // Zahl-Liste parsen
         $Text = preg_replace('=\[[L|l]iste [T|t]yp\=[Z|z]ahl\]=','<ul style="list-style-type: decimal;">',$Text);

         // Einfache Liste parsen
         $Text = preg_replace('=\[[L|l]iste\]=','<ul>',$Text);

         // Listen-Ende parsen
         $Text = preg_replace('=\[\/[L|l]iste\]=','</ul>',$Text);

         // Formatierten Text zurückgeben
         return $Text;

       // end function
      }


      /**
      *  @private
      *
      *  Bindet Extensions ein und führt diese aus. Diese muss durch einen Namespace, ein Modul,
      *  eine Klasse und eine Methode in der Konfiguration spezifiziert werden. Die Konfiguration
      *  muss in der Datei {ENVIRONMENT}_bbcpext.ini im Namespace tools::string::{CONTEXT}
      *  abgelegt werden. Die Parameter je Sektion sind:
      *  <ul>
      *    <li>Namespace: Namespace des Formatters</li>
      *    <li>Module: Name der Datei</li>
      *    <li>Class: Name der Klasse</li>
      *    <li>Method: Methode des Formatters</li>
      *  </ul>
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 12.03.2006<br />
      *  Version 0.2, 23.12.2007 (Definition der Parameter geändert, Dokumentation ergänzt)<br />
      */
      function __parseExtensionTags($Text){

         $Config = &$this->__getConfiguration('tools::string','bbcpext');
         $Extensions = $Config->getConfiguration();

         foreach($Extensions as $Extension => $Konfiguration){

            if(isset($Konfiguration['Namespace']) && isset($Konfiguration['Module']) && isset($Konfiguration['Class']) && isset($Konfiguration['Method'])){
               import($Konfiguration['Namespace'],$Konfiguration['Module']);
               $Ext = new $Konfiguration['Class'];
               $Text = $Ext->{$Konfiguration['Method']}($Text);
               unset($Ext);
             // end if
            }
            else{
               trigger_error('[bbCodeParser->__parseExtensionTags()] Configuration is not defined properly in section "'.$Extension.'"!');
             // end else
            }

          // end foreach
         }

         return $Text;

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Helper-Funktion für __parseLinkTags() um Downloads sauber<br />
      *  darstellen zu können.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 06.02.2006<br />
      *  Version 0.2, 10.08.2006 (Ausgabe-Datei auf 'datei.php' angepasst)<br />
      *  Version 0.3, 17.08.2006 (Ausgabe einer Datei zum Download wurde wegen URL-Rewriting auf ein neues Fenster verlegt;!!!BETA!!!)<br />
      *  Version 0.4, 16.08.2007 (Ausgabe wieder auf normalen Link mit linkrewrite="false" zurückgesetzt)
      *  Version 0.5, 21.06.2008 (Removed APPS__URL_PATH and replaced it with a value from the Registry)<br />
      */
      static function linkDLCallback($Matches){

         // retrieve values from Registry
         $Reg = &Singleton::getInstance('Registry');
         $URLBasePath = $Reg->retrieve('apf::core','URLBasePath');

         // return image tag
         return '<a href="'.$URLBasePath.'/datei.php?Datei='.$Matches[1].'" title="'.$Matches[2].'" target="_blank" linkrewrite="false">'.$Matches[3].'</a>';

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Helper funktion for the __parsePictureTags() method.
      *
      *  @param array $Matches matches from the reg exp applied to the text
      *  @return string $ImageString desired image string to be placed in the parsed text
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 21.07.2008<br />
      */
      static function imageCallback($Matches){

         // retrieve values from Registry
         $Reg = &Singleton::getInstance('Registry');
         $URLRewriting = $Reg->retrieve('apf::core','URLRewriting');
         $URLBasePath = $Reg->retrieve('apf::core','URLBasePath');

         // extract image name and extension
         $ExtPos = strrpos($Matches[1],'.');
         $Ext = substr($Matches[1],$ExtPos + 1);
         $Image = str_replace('.'.$Ext,'',$Matches[1]);

         // create image strings
         if(!isset($Matches[3])){

            if($URLRewriting === true){
               return '<img src="'.$URLBasePath.'/~/modules_imageresizer-action/showImage/image/'.$Image.'/ext/'.$Ext.'" align="absmiddle" galleryimg="no" border="0" alt="" />';
             // end if
            }
            else{
               return '<img src="'.$URLBasePath.'/?modules_imageresizer-action:showImage=image:'.$Image.'|ext:'.$Ext.'" align="absmiddle" galleryimg="no" border="0" alt="" />';
             // end else
            }

          // end if
         }
         else{

            if($URLRewriting === true){
               return '<img src="'.$URLBasePath.'/~/modules_imageresizer-action/showImage/image/'.$Image.'/ext/'.$Ext.'/size/'.$Matches[2].'" align="absmiddle" galleryimg="no" border="0" alt="" />';
             // end if
            }
            else{
               return '<img src="'.$URLBasePath.'/?modules_imageresizer-action:showImage=image:'.$Image.'|ext:'.$Ext.'|size:'.$Matches[2].'" align="absmiddle" galleryimg="no" border="0" alt="" />';
             // end else
            }

          // end else
         }

       // end function
      }

    // end class
   }
?>
<?php
   import('modules::webstat::data','webStatMapper');


   /**
   *  @package modules::webstat
   *  @module webStatManager
   *
   *  Implementiert einen Statistik-Inserter-Manager, der bei Aufruf einer Seite die<br />
   *  aufgerufenen Seiten loggt.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 21.12.2005<br />
   */
   class webStatManager extends coreObject
   {

      function webStatManager(){
      }


      /**
      *  @module schreibeStatistik()
      *  @public
      *
      *  Schreibt Statistikeinträge gemäß der gegebenen Parameter.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 22.12.2005<br />
      */
      function writeStatistic($Page,$BenutzerName,$RequestURI,$SessionID,$Referrer){

         // Datum
         $Tag = date('d');
         $Monat = date('m');
         $Jahr = date('Y');

         // Uhrzeit
         $Minute = date('i');
         $Stunde = date('H');
         $Sekunde = date('s');

         // Browser
         $Browser = $this->getBrowser();

         // Sprache
         $Sprache = $this->getLanguage();

         // Betriebssystem
         $Betriebssystem = $this->getOS();

         // IP/DNS
         $DNSIP = $this->getHostInfo();
         $IPAdresse = $DNSIP['IP'];
         $DNSAdresse = $DNSIP['DNS'];

         // Herkunft
         $Herkunft = $this->getReferrer();

         // UserAgent
         $UserAgent = $_SERVER['HTTP_USER_AGENT'];

         // Zählung für Seiten vornehmen
         $wSM = &$this->__getServiceObject('modules::webstat::data','webStatMapper');

         if(ereg('[0-9]',$Page)){

            $Pages = split('[.]',$Page);

            for($i = 0; $i < count($Pages); $i++){
               $Name = $wSM->showPageName($Pages[$i]);
               $wSM->createStatEntry($Name,$RequestURI,$Tag,$Monat,$Jahr,$Stunde,$Minute,$Sekunde,$BenutzerName,$SessionID,$Browser,$Sprache,$Betriebssystem,$IPAdresse,$DNSAdresse,$Herkunft,$UserAgent);
             // end for
            }

          // end if
         }
         else{
            $Name = ucfirst($Page);
            $wSM->createStatEntry($Name,$RequestURI,$Tag,$Monat,$Jahr,$Stunde,$Minute,$Sekunde,$BenutzerName,$SessionID,$Browser,$Sprache,$Betriebssystem,$IPAdresse,$DNSAdresse,$Herkunft,$UserAgent);
          // end else
         }

       // end function
      }


      /**
      *  @module getUserName()
      *  @public
      *
      *  Ermittelt den Benutzernamen des angemeldeten Clients.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, xxx<br />
      *  Version 0.2, 22.12.2005<br />
      */
      function getUserName(){

          if(empty($_SERVER['REMOTE_USER']) || $_SERVER['REMOTE_USER'] == '' || $_SERVER['REMOTE_USER'] == ' '){
            $UserName = '*';
          // end if
         }
         else{
            $UserName = $_SERVER['REMOTE_USER'];
          // end else
         }

         return $UserName;

       // end function
      }


      /**
      *  @module getReferrer()
      *  @public
      *
      *  Ermittelt den Referrer der Seite. Sollte keiner gegeben sein (Client-Firewall),<br />
      *  so wird '*' ausgegeben.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, xxx<br />
      *  Version 0.2, 22.12.2005<br />
      */
      function getReferrer(){

         if(isset($_SERVER['HTTP_REFERER'])){
            $Herkunft = $_SERVER['HTTP_REFERER'];
          // end if
         }
         else{
            $Herkunft = (string)'*';
          // end else
         }

         return $Herkunft;

       // end function
      }


      /**
      *  @module getLanguage()
      *  @public
      *
      *  Ermittelt die Sprache des Browsers.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, xxx<br />
      */
      function getLanguage(){

         if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && !empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
            $Sprache = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
          // end if
         }
         else{
            $Sprache = '*';
          // end else
         }

         return $Sprache;

       // end function
      }


      /**
      *  @module getHostInfo()
      *  @public
      *
      *  Gibt Client IP und DNS zurück.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 22.03.2005<br />
      */
      function getHostInfo(){

         $DNSIP = trim($_SERVER['REMOTE_ADDR']);

         if(ereg("[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}",$DNSIP)){
            $Return['DNS'] = gethostbyaddr($DNSIP);
            $Return['IP'] = $DNSIP;
          // end if
         }
         else{
            $Return['DNS'] = $DNSIP;
            $Return['IP'] = gethostbyname($DNSIP);
          // end if
         }

         return $Return;

       // end function
      }


      /**
      *  @module getOS()
      *  @public
      *
      *  Ermittelt das Betriebssystem des Clients.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, xxx<br />
      */
      function getOS(){

         $BetriebssystemZuordnung = array(
                                          'Windows 98' => 'Windows 98',
                                          'Win98' => 'Windows 98',
                                          'Windows NT 3' => 'Windows NT',
                                          'Windows NT 4.0' => 'Windows NT SP6a',
                                          'Windows NT 5.0' => 'Windows 2000',
                                          'Windows NT 5.1' => 'Windows XP',
                                          'Windows NT 5.2' => 'Windows XP SP2',
                                          'Mac' => 'Macintosh',
                                          'Linux' => 'Linux',
                                          'Googlebot' => 'Google-Crawler-Server',
                                          'Gecko' => 'Gecko-Crawler-Server'
                                         );

         foreach($BetriebssystemZuordnung as $Key => $Wert){
            if(substr_count($_SERVER['HTTP_USER_AGENT'],$Key) > 0){
               $OS = $Wert;
             // end if
            }
          // end foreach
         }

         if(empty($OS) || $OS == ' '){
            $OS = '*';
          // end if
         }

         return $OS;

       // end function
      }


      /**
      *  @module getBrowser()
      *  @public
      *
      *  Ermittelt den Browser, mit dem die Seite aufgerufen wird.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, xxx<br />
      */
      function getBrowser(){

         $BrowserZuordnung = array(
                                   'Konqueror' => '[BROWSER] Konqueror',
                                   'Konqueror/3.4' => '[BROWSER] Konqueror 3.4',
                                   'Konqueror/3.3' => '[BROWSER] Konqueror 3.3',
                                   'MSIE 5' => '[BROWSER] Internet Explorer 5',
                                   'MSIE 5.5' => '[BROWSER] Internet Explorer 5.5',
                                   'MSIE 6' => '[BROWSER] Internet Explorer 6',
                                   'MSIE 3' => '[BROWSER] Internet Explorer 3',
                                   'MSIE 4' => '[BROWSER] Internet Explorer 4',
                                   'Opera' => '[BROWSER] Opera',
                                   'Firefox' => '[BROWSER] Firefox',
                                   'iCab' => '[BROWSER] iCab'
                                  );

         //
         //   Suchabfrage für Spider und Bots:
         //   SELECT UserAgent, COUNT(UserAgent) AS Anzahl FROM statistiken_live WHERE NOT INSTR(UserAgent,'Mozilla') GROUP BY UserAgent
         //

         $BotZuordnung = array(
                               'Yahoo! Slurp' => '[BOT] Yahoo!-Spider Browseransicht',
                               'Gecko' => '[BOT] Gecko-Crawler-Server',
                               'appie 1.1 (www.walhello.com)' => '[BOT] walhello.com',
                               'contype' => '[BOT] ConType Spider',
                               'curl/7.9.2' => '[BOT] Linux Spider',
                               'findlinks/0.87' => '[BOT] Wortschatzspider Uni Leipzig',
                               'getRAX' => '[BOT] getRAX Spider',
                               'Gigabot/2.0' => '[BOT] Gibagot',
                               'Googlebot/2.1' => '[BOT] Google.de',
                               'HeinrichderMiragoRobot' => '[BOT] MiragoRobot',
                               'ia_archiver' => '[BOT] Alexa Websearch',
                               'iCab' => '[BOT] Macintosh',
                               'Jetbot/1.0' => '[BOT] JetBot',
                               'larbin-mb' => '[BOT] Larbin Webspider',
                               'libwww-perl' => '[BOT] libperl www-client',
                               'LinkWalker' => '[BOT] LinkWalker',
                               'lwp-trivial/1.36' => '[BOT] LWP-Trivial Web Crawler',
                               'MJ12bot/v0.9.0' => '[BOT] majestic12.co.uk',
                               'msnbot/0.3' => '[BOT] MSNBot',
                               'msnbot/1.0' => '[BOT] MSNBot',
                               'psbot/0.1' => '[BOT] PicSearch Crawler',
                               'Seekbot/1.0' => '[BOT] seekbot.net',
                               'suchbaer.de' => '[BOT] suchbaer.de',
                               'SVN/1.1.0' => '[BOT] SVN/1.1.0',
                               'TurnitinBot/2.0' => '[BOT] turnitin.com',
                               'Wildsoft Surfer' => '[BOT] Wildsoft Surfer / dlman Crawler',
                               'WMP/1.0' => '[BOT] webmasterplan.de CrawlTool',
                               'Zao-Crawler' => '[BOT] kototoi.org/zao'
                              );

         $Browser = (string)'';

         foreach($BrowserZuordnung as $Key => $Wert){
            if(substr_count($_SERVER['HTTP_USER_AGENT'],$Key) > 0){
               $Browser = $Wert;
             // end if
            }
          // end foreach
         }
         foreach($BotZuordnung as $Key => $Wert){
            if(substr_count($_SERVER['HTTP_USER_AGENT'],$Key) > 0){
               $Browser = $Wert;
             // end if
            }
          // end foreach
         }

         if(empty($Browser) || $Browser == ' '){
            $Browser = $_SERVER['HTTP_USER_AGENT'];
          // end if
         }

         return $Browser;

       // end function
      }

    // end class
   }
?>
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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('modules::kontakt4::biz','oFormData');
   import('modules::kontakt4::biz','oRecipient');


   /**
   *  @package modules::kontakt4::data
   *  @class contactMapper
   *
   *  Implementiert die Datenschicht des Kontaktformulars<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 03.06.2006<br />
   *  Version 0.2, 04.06.2006<br />
   */
   class contactMapper extends coreObject
   {

      function contactMapper(){
      }


      /**
      *  @public
      *
      *  L�d die gesamte Empf�nger-Liste.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 03.06.2006<br />
      *  Version 0.2, 04.06.2006<br />
      *  Version 0.3, 04.03.2007 (Auf ConfigurationManager umgestellt)<br />
      */
      function loadRecipients(){

         // Config einlesen
         $Config = $this->__getConfiguration('modules::kontakt4','empfaenger');


         // Section lesen
         $Sections = $Config->getConfiguration();


         // ReturnArray initialisieren
         $Recipients = array();


         // Recipients auslesen
         foreach($Config->getConfiguration() as $Key => $Values){

            $Count = count($Recipients);
            $Recipients[$Count] = new oRecipient();

            // Nummer
            preg_match("/Kontakt ([0-9]+)/i",$Key,$Matches);
            $Recipients[$Count]->set('oID',$Matches[1]);

            // Name
            $Recipients[$Count]->set('Name',$Values['EmpfaengerName']);

            // Adresse
            $Recipients[$Count]->set('Adresse',$Values['EmpfaengerAdresse']);

          // end foreach
         }


         // Recipients zur�ckgeben
         return $Recipients;

       // end function
      }


      /**
      *  @public
      *
      *  L�d ein Emp�ngerobjekt f�r einen gegebene Id.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 03.06.2006<br />
      *  Version 0.2, 04.06.2006<br />
      *  Version 0.3, 04.03.2007 (Kleinere Korrekturen)<br />
      */
      function loadRecipientPerId($Id){

         $Rec = $this->loadRecipients();

         if(!is_array($Rec)){
            return array();
          // end if
         }
         else{

            for($i = 0; $i < count($Rec); $i++){
               if($Rec[$i]->get('oID') == $Id){
                  return $Rec[$i];
                // end if
               }
             // end for
            }

          // end else
         }

       // end function
      }

    // end class
   }
?>
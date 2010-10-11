<?php
   /**
    * <!--
    * This file is part of the adventure php framework (APF) published under
    * http://adventure-php-framework.org.
    *
    * The APF is free software: you can redistribute it and/or modify
    * it under the terms of the GNU Lesser General Public License as published
    * by the Free Software Foundation, either version 3 of the License, or
    * (at your option) any later version.
    *
    * The APF is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    * GNU Lesser General Public License for more details.
    *
    * You should have received a copy of the GNU Lesser General Public License
    * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
    * -->
    */

   import('modules::kontakt4::biz','ContactFormRecipient');

   /**
    * @package modules::kontakt4::data
    * @class ContactMapper
    *
    * Implementiert die Datenschicht des Kontaktformulars<br />
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 03.06.2006<br />
    * Version 0.2, 04.06.2006<br />
    */
   class ContactMapper extends APFObject {

      /**
       * @public
       *
       * Loads the list of recipients
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 03.06.2006<br />
       * Version 0.2, 04.06.2006<br />
       * Version 0.3, 04.03.2007 (Auf ConfigurationManager umgestellt)<br />
       */
      public function loadRecipients(){

         $config = $this->getConfiguration('modules::kontakt4','recipients.ini');

         $recipients = array();
         foreach($config->getSectionNames() as $name){

            $section = $config->getSection($name);

            $count = count($recipients);
            $recipients[$count] = new ContactFormRecipient();

            preg_match('/Kontakt ([0-9]+)/i',$name,$matches);
            $recipients[$count]->setId($matches[1]);

            $recipients[$count]->setName($section->getValue('EmpfaengerName'));
            $recipients[$count]->setEmailAddress($section->getValue('EmpfaengerAdresse'));

          // end foreach
         }

         return $recipients;

       // end function
      }

      /**
       * @public
       *
       * Loads an recipient by a given id.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 03.06.2006<br />
       * Version 0.2, 04.06.2006<br />
       * Version 0.3, 04.03.2007<br />
       */
      public function loadRecipientPerId($Id){

         $recipients = $this->loadRecipients();

         if(!is_array($recipients)){
            return array();
         }

         for($i = 0; $i < count($recipients); $i++){
            if($recipients[$i]->getId() == $Id){
               return $recipients[$i];
            }
         }

       // end function
      }

    // end class
   }
?>
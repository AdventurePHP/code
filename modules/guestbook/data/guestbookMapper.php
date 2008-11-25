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

   import('modules::guestbook::biz','Guestbook');
   import('modules::guestbook::biz','Entry');
   import('modules::guestbook::biz','Comment');
   import('core::database','MySQLHandler');


   /**
   *  @namespace modules::guestbook::data
   *  @class guestbookMapper
   *
   *  DataMapper des Gästebuchs.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 12.04.2007<br />
   *  Version 0.2, 07.01.2008 (Werte werden beim Inserten/Updaten quotiert)<br />
   */
   class guestbookMapper extends coreObject
   {

      function guestbookMapper(){
      }


      /**
      *  @public
      *
      *  Läd ein Entry-Objekt per EntryID.<br />
      *
      *  @param string $EntryID; ID des Eintrags
      *  @return Entry $Entry; Entry-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      */
      function loadEntryByID($EntryID){

         // Instanz des MySQLHandlers holen
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');

         // Daten lesen und zurückgeben
         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('loadEntryByID('.$EntryID.')');
         $select = 'SELECT * FROM entry WHERE EntryID = \''.$EntryID.'\';';
         $result = $SQL->executeTextStatement($select);

         $T->start('__mapEntry2DomainObject('.$EntryID.')');
         $Entry = $this->__mapEntry2DomainObject($SQL->fetchData($result));
         $T->stop('__mapEntry2DomainObject('.$EntryID.')');

         $T->stop('loadEntryByID('.$EntryID.')');

         return $Entry;

       // end function
      }


      /**
      *  @public
      *
      *  Läd ein Comment-Objekt per CommentID.<br />
      *
      *  @param string $CommentID; ID des Eintrags
      *  @return Comment $Comment; Comment-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      */
      function loadCommentByID($CommentID){

         // Instanz des MySQLHandlers holen
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');

         // Daten lesen und zurückgeben
         $select = 'SELECT * FROM comment WHERE CommentID = \''.$CommentID.'\';';
         $result = $SQL->executeTextStatement($select);
         return $this->__mapComment2DomainObject($SQL->fetchData($result));

       // end function
      }


      /**
      *  @public
      *
      *  Läd ein Guestbook-Objekt per GuestbookID.<br />
      *
      *  @param string $GuestbookID; ID des Eintrags
      *  @return Guestbook $Guestbook; Guestbook-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      */
      function loadGuestbookByID($GuestbookID){

         // Instanz des MySQLHandlers holen
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');

         // Daten lesen und zurückgeben
         $select = 'SELECT * FROM guestbook WHERE GuestbookID = \''.$GuestbookID.'\';';
         $result = $SQL->executeTextStatement($select);
         return $this->__mapGuestbook2DomainObject($SQL->fetchData($result));

       // end function
      }


      /**
      *  @public
      *
      *  Läd einen Eintrag mit allen seinen Kommentaren.<br />
      *
      *  @param string $EntyID; ID des Eintrags
      *  @return Entry $Entry; Eintrags-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      */
      function loadEntryWithComments($EntyID){

         // Instanz des MySQLHandlers holen
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');


         // Eintrag laden
         $Entry = $this->loadEntryByID($EntyID);


         // Kommentare hinzuladen
         $select = 'SELECT comment.CommentID AS ID FROM comment
                    INNER JOIN comp_entry_comment ON comment.CommentID = comp_entry_comment.CommentID
                    INNER JOIN entry ON comp_entry_comment.EntryID = entry.EntryID
                    WHERE entry.EntryID = \''.$Entry->get('ID').'\';';
         $result = $SQL->executeTextStatement($select);

         while($data = $SQL->fetchData($result)){
            $Comment = $this->loadCommentByID($data['ID']);
            $Entry->addComment($Comment);
            $Comment = null;
          // end while
         }


         // Eintrag zurückgeben
         return $Entry;

       // end function
      }


      /**
      *  @public
      *
      *  Läd ein Gästebuch mit allen Einträgen.<br />
      *
      *  @param string $GuestbookID; ID des Eintrags
      *  @return Guestbook $Guestbook; Guestbook-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      */
      function loadGuestbookWithEntries($GuestbookID){

         // Instanz des MySQLHandlers holen
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');


         // Gästebuch laden
         $Guestbook = $this->loadGuestbookByID($GuestbookID);


         // Kommentare hinzuladen
         $select = 'SELECT entry.EntryID AS ID FROM entry
                    INNER JOIN comp_guestbook_entry ON entry.EntryID = comp_guestbook_entry.EntryID
                    INNER JOIN guestbook ON comp_guestbook_entry.GuestbookID = guestbook.GuestbookID
                    WHERE guestbook.GuestbookID = \''.$Guestbook->get('ID').'\';';
         $result = $SQL->executeTextStatement($select);

         while($data = $SQL->fetchData($result)){
            $Entry = $this->loadEntryWithComments($data['ID']);
            $Guestbook->addEntry($Entry);
            $Entry = null;
          // end while
         }


         // Gästebuch zurückgeben
         return $Guestbook;

       // end function
      }


      /**
      *  @public
      *
      *  Speichert ein Gästebuch.<br />
      *
      *  @param Guestbook $Guestbook; Ein Guestbook-Objekt
      *  @return string $GuestbookID; ID des Eintrags
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      *  Version 0.2, 14.04.2007 (Bug beim Update und Speichern behoben)<br />
      */
      function saveGuestbook($Guestbook){

         // Instanz des MySQLHandlers holen
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');

         // Entry speichern oder updaten
         $GuestbookID = $Guestbook->get('ID');

         if($GuestbookID != null){

            $update = 'UPDATE guestbook SET
                       Name = \''.$SQL->escapeValue($Guestbook->get('Name')).'\',
                       Description = \''.$SQL->escapeValue($Guestbook->get('Description')).'\',
                       Admin_Username = \''.$SQL->escapeValue($Guestbook->get('Admin_Username')).'\',
                       Admin_Password = \''.$SQL->escapeValue($Guestbook->get('Admin_Password')).'\'
                       WHERE GuestbookID = \''.$GuestbookID.'\';';
            $SQL->executeTextStatement($update);

          // end if
         }
         else{

            $insert = 'INSERT INTO guestbook

                       (
                          Name,
                          Description,
                          Admin_Username,
                          Admin_Password
                       )

                       VALUES
                       (
                          \''.$SQL->escapeValue($Guestbook->get('Name')).'\',
                          \''.$SQL->escapeValue($Guestbook->get('Description')).'\',
                          \''.$SQL->escapeValue($Guestbook->get('Admin_Username')).'\',
                          \''.$SQL->escapeValue($Guestbook->get('Admin_Password')).'\'
                       );';
            $SQL->executeTextStatement($insert);
            $GuestbookID = $SQL->getLastID();

          // end else
         }

         // Prüfen, ob Eintrag Kommentare hat, wenn ja speichern
         $Entries = $Guestbook->getEntries();
         if(count($Entries) > 0){

            for($i = 0; $i < count($Entries); $i++){

               // Entries selbst speichern
               $EntryID = $this->saveEntry($Entries[$i]);

               // Beziehung zum Eintrag speichern
               $select = 'SELECT * FROM comp_guestbook_entry WHERE GuestbookID  = \''.$GuestbookID.'\' AND EntryID = \''.$EntryID.'\';';
               $result = $SQL->executeTextStatement($select);

               if($SQL->getNumRows($result) > 0){
                // end if
               }
               else{

                  // Beziehung speichern
                  $insert = 'INSERT INTO comp_guestbook_entry (GuestbookID,EntryID) VALUES (\''.$GuestbookID.'\',\''.$EntryID.'\');';
                  $SQL->executeTextStatement($insert);

                // end if
               }

             // end for
            }

          // end if
         }


         // Entry-ID zurückgeben
         return $GuestbookID;

       // end function
      }


      /**
      *  @public
      *
      *  Speichert einen Eintrag.<br />
      *
      *  @param Entry $Entry; Ein Entry-Objekt
      *  @return string $EntryID; ID des Eintrags
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      */
      function saveEntry($Entry){

         // Instanz des MySQLHandlers holen
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');

         // Entry speichern oder updaten
         $EntryID = $Entry->get('ID');

         if($EntryID != null){

            $update = 'UPDATE entry SET
                       Name = \''.$SQL->escapeValue($Entry->get('Name')).'\',
                       EMail = \''.$SQL->escapeValue($Entry->get('EMail')).'\',
                       City = \''.$SQL->escapeValue($Entry->get('City')).'\',
                       Website = \''.$SQL->escapeValue($Entry->get('Website')).'\',
                       ICQ = \''.$SQL->escapeValue($Entry->get('ICQ')).'\',
                       MSN = \''.$SQL->escapeValue($Entry->get('MSN')).'\',
                       Skype = \''.$SQL->escapeValue($Entry->get('Skype')).'\',
                       AIM = \''.$SQL->escapeValue($Entry->get('AIM')).'\',
                       Yahoo = \''.$SQL->escapeValue($Entry->get('Yahoo')).'\',
                       Text = \''.$SQL->escapeValue($Entry->get('Text')).'\'
                       WHERE EntryID = \''.$EntryID.'\';';
            $SQL->executeTextStatement($update);

          // end if
         }
         else{

            $insert = 'INSERT INTO entry

                       (
                          Name,
                          EMail,
                          City,
                          Website,
                          ICQ,
                          MSN,
                          Skype,
                          AIM,
                          Yahoo,
                          Text,
                          Date,
                          Time
                       )

                       VALUES
                       (
                          \''.$SQL->escapeValue($Entry->get('Name')).'\',
                          \''.$SQL->escapeValue($Entry->get('EMail')).'\',
                          \''.$SQL->escapeValue($Entry->get('City')).'\',
                          \''.$SQL->escapeValue($Entry->get('Website')).'\',
                          \''.$SQL->escapeValue($Entry->get('ICQ')).'\',
                          \''.$SQL->escapeValue($Entry->get('MSN')).'\',
                          \''.$SQL->escapeValue($Entry->get('Skype')).'\',
                          \''.$SQL->escapeValue($Entry->get('AIM')).'\',
                          \''.$SQL->escapeValue($Entry->get('Yahoo')).'\',
                          \''.$SQL->escapeValue($Entry->get('Text')).'\',
                          CURDATE(),
                          CURTIME()
                       );';
            $SQL->executeTextStatement($insert);
            $EntryID = $SQL->getLastID();

          // end else
         }

         // Prüfen, ob Eintrag Kommentare hat, wenn ja speichern
         $Comments = $Entry->getComments();
         if(count($Comments) > 0){

            for($i = 0; $i < count($Comments); $i++){

               // Comment selbst speichern
               $CommentID = $this->saveComment($Comments[$i]);

               // Beziehung zum Eintrag speichern
               $select = 'SELECT * FROM comp_entry_comment WHERE EntryID = \''.$EntryID.'\' AND CommentID = \''.$CommentID.'\';';
               $result = $SQL->executeTextStatement($select);

               if($SQL->getNumRows($result) > 0){
                // end if
               }
               else{

                  // Beziehung speichern
                  $insert = 'INSERT INTO comp_entry_comment (EntryID,CommentID) VALUES (\''.$EntryID.'\',\''.$CommentID.'\');';
                  $SQL->executeTextStatement($insert);

                // end if
               }

             // end for
            }

          // end if
         }


         // Entry-ID zurückgeben
         return $EntryID;

       // end function
      }


      /**
      *  @public
      *
      *  Speichert einen Kommentar.<br />
      *
      *  @param Comment $Comment; Ein Comment-Objekt
      *  @return string $CommentID; ID des Eintrags
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      */
      function saveComment($Comment){

         // Instanz des MySQLHandlers holen
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');

         // Entry speichern oder updaten
         $CommentID = $Comment->get('ID');

         if($CommentID != null){

            $update = 'UPDATE comment SET
                       Title = \''.$SQL->escapeValue($Comment->get('Title')).'\',
                       Text = \''.$SQL->escapeValue($Comment->get('Text')).'\'
                       WHERE CommentID = \''.$CommentID.'\';';
            $SQL->executeTextStatement($update);

          // end if
         }
         else{

            $insert = 'INSERT INTO comment

                       (
                          Title,
                          Text,
                          Date,
                          Time
                       )

                       VALUES
                       (
                          \''.$SQL->escapeValue($Comment->get('Title')).'\',
                          \''.$SQL->escapeValue($Comment->get('Text')).'\',
                          CURDATE(),
                          CURTIME()
                       );';
            $SQL->executeTextStatement($insert);
            $CommentID = $SQL->getLastID();

          // end else
         }


         // CommentID zurückgeben
         return $CommentID;

       // end function
      }


      /**
      *  @public
      *
      *  Löscht einen Entry eines Gästebuchs.<br />
      *
      *  @param Entry $Entry; Eintrags-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      */
      function deleteEntry($Entry){

         // Instanz des MySQLHandlers holen
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');

         // Kommentare zum Eintrag selektieren und ggf. löschen
         $select_commment = 'SELECT comment.CommentID AS ID FROM comment
                             INNER JOIN comp_entry_comment ON comment.CommentID = comp_entry_comment.CommentID
                             INNER JOIN entry ON comp_entry_comment.EntryID = entry.EntryID
                             WHERE entry.EntryID = \''.$Entry->get('ID').'\';';
         $result_comment = $SQL->executeTextStatement($select_commment);

         while($data_comment = $SQL->fetchData($result_comment)){

            // Kommentar löschen
            $delete_comment = 'DELETE FROM comment WHERE CommentID = \''.$data_comment['ID'].'\';';
            $SQL->executeTextStatement($delete_comment);

            // Beziehung zum Eintrag löschen
            $delete_comp_comment = 'DELETE FROM comp_entry_comment WHERE CommentID = \''.$data_comment['ID'].'\';';
            $SQL->executeTextStatement($delete_comp_comment);

            // Variablen zurücksetzen
            $delete_comment = null;
            $delete_comp_comment = null;

          // end while
         }

         // Eintrag selbst löschen
         $delete_entry = 'DELETE FROM entry WHERE EntryID = \''.$Entry->get('ID').'\';';
         $SQL->executeTextStatement($delete_entry);

         // Komposition zum Gästebuch löschen
         $delete_comp_entry = 'DELETE FROM comp_guestbook_entry WHERE EntryID = \''.$Entry->get('ID').'\';';
         $SQL->executeTextStatement($delete_comp_entry);

       // end function
      }


      /**
      *  @public
      *
      *  Löscht einen Kommentar eines Eintrags.<br />
      *
      *  @param Comment $Comment; Kommentar-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 19.05.2007<br />
      */
      function deleteComment($Comment){

         // Instanz des MySQLHandlers holen
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');

         // Beziehung zum Eintrag löschen
         $delete_comp_comment = 'DELETE FROM comp_entry_comment WHERE CommentID = \''.$Comment->get('ID').'\';';
         $SQL->executeTextStatement($delete_comp_comment);

         // Kommentar löschen
         $delete_comment = 'DELETE FROM comment WHERE CommentID = \''.$Comment->get('ID').'\';';
         $SQL->executeTextStatement($delete_comment);

       // end function
      }


      /**
      *  @private
      *
      *  Mappt ein ResultSet in ein Entry-Objekt.<br />
      *
      *  @param array $EntryResultSet; Datenbank-ResultSet
      *  @return Entry $Entry; Entry-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      */
      function __mapEntry2DomainObject($EntryResultSet){

         // Neues Entry-Objekt erstellen
         $Entry = new Entry();

         // Variablen in das Objekt einsetzen
         if(isset($EntryResultSet['EntryID'])){
            $Entry->set('ID',$EntryResultSet['EntryID']);
          // end if
         }
         if(isset($EntryResultSet['Name'])){
            $Entry->set('Name',$EntryResultSet['Name']);
          // end if
         }
         if(isset($EntryResultSet['EMail'])){
            $Entry->set('EMail',$EntryResultSet['EMail']);
          // end if
         }
         if(isset($EntryResultSet['City'])){
            $Entry->set('City',$EntryResultSet['City']);
          // end if
         }
         if(isset($EntryResultSet['Website'])){
            $Entry->set('Website',$EntryResultSet['Website']);
          // end if
         }
         if(isset($EntryResultSet['ICQ'])){
            $Entry->set('ICQ',$EntryResultSet['ICQ']);
          // end if
         }
         if(isset($EntryResultSet['MSN'])){
            $Entry->set('MSN',$EntryResultSet['MSN']);
          // end if
         }
         if(isset($EntryResultSet['Skype'])){
            $Entry->set('Skype',$EntryResultSet['Skype']);
          // end if
         }
         if(isset($EntryResultSet['AIM'])){
            $Entry->set('AIM',$EntryResultSet['AIM']);
          // end if
         }
         if(isset($EntryResultSet['Yahoo'])){
            $Entry->set('Yahoo',$EntryResultSet['Yahoo']);
          // end if
         }
         if(isset($EntryResultSet['Text'])){
            $Entry->set('Text',$EntryResultSet['Text']);
          // end if
         }
         if(isset($EntryResultSet['Date'])){
            $Entry->set('Date',$EntryResultSet['Date']);
          // end if
         }
         if(isset($EntryResultSet['Time'])){
            $Entry->set('Time',$EntryResultSet['Time']);
          // end if
         }

         // Entry zurückgeben
         return $Entry;

       // end function
      }


      /**
      *  @private
      *
      *  Mappt ein ResultSet in ein Guestbook-Objekt.<br />
      *
      *  @param array $GuestbookResultSet; Datenbank-ResultSet
      *  @return Guestbook $Guestbook; Guestbook-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      */
      function __mapGuestbook2DomainObject($GuestbookResultSet){

         // Neues Guestbook-Objekt erstellen
         $Guestbook = new Guestbook();

         // Variablen in das Objekt einsetzen
         if(isset($GuestbookResultSet['GuestbookID'])){
            $Guestbook->set('ID',$GuestbookResultSet['GuestbookID']);
          // end if
         }
         if(isset($GuestbookResultSet['Name'])){
            $Guestbook->set('Name',$GuestbookResultSet['Name']);
          // end if
         }
         if(isset($GuestbookResultSet['Description'])){
            $Guestbook->set('Description',$GuestbookResultSet['Description']);
          // end if
         }
         if(isset($GuestbookResultSet['Admin_Username'])){
            $Guestbook->set('Admin_Username',$GuestbookResultSet['Admin_Username']);
          // end if
         }
         if(isset($GuestbookResultSet['Admin_Password'])){
            $Guestbook->set('Admin_Password',$GuestbookResultSet['Admin_Password']);
          // end if
         }

         // Guestbook zurückgeben
         return $Guestbook;

       // end function
      }


      /**
      *  @private
      *
      *  Mappt ein ResultSet in ein Comment-Objekt.<br />
      *
      *  @param array $CommentResultSet; Datenbank-ResultSet
      *  @return Comment $Comment Comment-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      */
      function __mapComment2DomainObject($CommentResultSet){

         // Neues Guestbook-Objekt erstellen
         $Comment = new Comment();

         // Variablen in das Objekt einsetzen
         if(isset($CommentResultSet['CommentID'])){
            $Comment->set('ID',$CommentResultSet['CommentID']);
          // end if
         }
         if(isset($CommentResultSet['Title'])){
            $Comment->set('Title',$CommentResultSet['Title']);
          // end if
         }
         if(isset($CommentResultSet['Text'])){
            $Comment->set('Text',$CommentResultSet['Text']);
          // end if
         }
         if(isset($CommentResultSet['Date'])){
            $Comment->set('Date',$CommentResultSet['Date']);
          // end if
         }
         if(isset($CommentResultSet['Time'])){
            $Comment->set('Time',$CommentResultSet['Time']);
          // end if
         }

         // Comment zurückgeben
         return $Comment;

       // end function
      }

    // end class
   }
?>
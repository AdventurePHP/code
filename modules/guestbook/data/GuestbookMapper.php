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

   import('modules::guestbook::biz','Guestbook');
   import('modules::guestbook::biz','Entry');
   import('modules::guestbook::biz','Comment');

   /**
    * @package modules::guestbook::data
    * @class GuestbookMapper
    *
    * DataMapper of the guestbook.
    *
    * @author Christian Sch�fer
    * @version
    * Version 0.1, 12.04.2007<br />
    * Version 0.2, 07.01.2008 (Values are now quoted during insert/update)<br />
    */
   class GuestbookMapper extends APFObject {

      public function GuestbookMapper(){
      }

      /**
      *  @private
      *
      *  Returns the desired database connection for the guestbook.
      *
      *  @return $databaseConnection The guestbook database connection
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.03.2009<br />
      */
      private function &__getConnection(){

         // get configuration
         $config = &$this->__getConfiguration('modules::guestbook','guestbook');
         $connectionKey = $config->getValue('Default','Database.ConnectionKey');

         // create database
         $cM = &$this->__getServiceObject('core::database','ConnectionManager');
         $this->__DatabaseConnection = &$cM->getConnection($connectionKey);
         return $this->__DatabaseConnection;

       // end function
      }


      /**
      *  @public
      *
      *  Loads an entry by id.
      *
      *  @param string $entryID Id of the desired entry
      *  @return Entry $entry The entry object
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      *  Version 0.2, 26.03.2009 (Refactoring)<br />
      */
      public function loadEntryByID($entryID){

         $SQL = &$this->__getConnection();
         $select = 'SELECT * FROM entry WHERE EntryID = \''.$entryID.'\';';
         $result = $SQL->executeTextStatement($select);
         return $this->__mapEntry2DomainObject($SQL->fetchData($result));

       // end function
      }


      /**
      *  @public
      *
      *  Loads a comment by id.
      *
      *  @param string $commentID Id of the desired comment
      *  @return Comment $comment The comment object
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      *  Version 0.2, 26.03.2009 (Refactoring)<br />
      */
      public function loadCommentByID($commentID){

         $SQL = &$this->__getConnection();
         $select = 'SELECT * FROM comment WHERE CommentID = \''.$commentID.'\';';
         $result = $SQL->executeTextStatement($select);
         return $this->__mapComment2DomainObject($SQL->fetchData($result));

       // end function
      }


      /**
      *  @public
      *
      *  Loads a guestbook by id.
      *
      *  @param string $guestbookID Id of the desired guestbook
      *  @return Guestbook $guestbook The guestbook object
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      *  Version 0.2, 26.03.2009 (Refactoring)<br />
      */
      public function loadGuestbookByID($guestbookID){

         $SQL = &$this->__getConnection();
         $select = 'SELECT * FROM guestbook WHERE GuestbookID = \''.$guestbookID.'\';';
         $result = $SQL->executeTextStatement($select);
         return $this->__mapGuestbook2DomainObject($SQL->fetchData($result));

       // end function
      }


      /**
      *  @public
      *
      *  Loads an entry with it's comments.
      *
      *  @param string $entryID Id of the desired entry
      *  @return Entry $entry The entry id with it's comments
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      *  Version 0.2, 26.03.2009 (Refactoring)<br />
      */
      public function loadEntryWithComments($entryID){

         $SQL = &$this->__getConnection();
         $entry = $this->loadEntryByID($entryID);
         $select = 'SELECT comment.CommentID AS ID FROM comment
                    INNER JOIN comp_entry_comment ON comment.CommentID = comp_entry_comment.CommentID
                    INNER JOIN entry ON comp_entry_comment.EntryID = entry.EntryID
                    WHERE entry.EntryID = \''.$entry->get('ID').'\';';
         $result = $SQL->executeTextStatement($select);

         while($data = $SQL->fetchData($result)){
            $entry->addComment($this->loadCommentByID($data['ID']));
          // end while
         }

         return $entry;

       // end function
      }


      /**
      *  @public
      *
      *  Loads a guestbook with it's entries.
      *
      *  @param string $guestbookID Id of the desired guestbook
      *  @return Guestbook $guestbook The guestbook object
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      *  Version 0.2, 26.03.2009 (Refactoring)<br />
      */
      public function loadGuestbookWithEntries($guestbookID){

         $guestbook = $this->loadGuestbookByID($guestbookID);

         $SQL = &$this->__getConnection();
         $select = 'SELECT entry.EntryID AS ID FROM entry
                    INNER JOIN comp_guestbook_entry ON entry.EntryID = comp_guestbook_entry.EntryID
                    INNER JOIN guestbook ON comp_guestbook_entry.GuestbookID = guestbook.GuestbookID
                    WHERE guestbook.GuestbookID = \''.$guestbook->get('ID').'\';';
         $result = $SQL->executeTextStatement($select);

         while($data = $SQL->fetchData($result)){
            $guestbook->addEntry($this->loadEntryWithComments($data['ID']));
          // end while
         }

         return $guestbook;

       // end function
      }


      /**
      *  @public
      *
      *  Saves a guestbook.
      *
      *  @param Guestbook $guestbook The guestbook object
      *  @return string $guestbookID Id of the guestbook
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      *  Version 0.2, 14.04.2007 (Fixed bug during saving)<br />
      *  Version 0.3, 26.03.2009 (Refactoring)<br />
      */
      public function saveGuestbook($guestbook){

         $SQL = &$this->__getConnection();

         // save the guestbook itself
         $guestbookID = $guestbook->get('ID');

         if($guestbookID != null){

            $update = 'UPDATE guestbook SET
                       Name = \''.$SQL->escapeValue($guestbook->get('Name')).'\',
                       Description = \''.$SQL->escapeValue($guestbook->get('Description')).'\',
                       Admin_Username = \''.$SQL->escapeValue($guestbook->get('Admin_Username')).'\',
                       Admin_Password = \''.$SQL->escapeValue($guestbook->get('Admin_Password')).'\'
                       WHERE GuestbookID = \''.$guestbookID.'\';';
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
                          \''.$SQL->escapeValue($guestbook->get('Name')).'\',
                          \''.$SQL->escapeValue($guestbook->get('Description')).'\',
                          \''.$SQL->escapeValue($guestbook->get('Admin_Username')).'\',
                          \''.$SQL->escapeValue($guestbook->get('Admin_Password')).'\'
                       );';
            $SQL->executeTextStatement($insert);
            $guestbookID = $SQL->getLastID();

          // end else
         }

         // check, if an entry has comments and save them, too
         $entries = $guestbook->getEntries();
         if(count($entries) > 0){

            for($i = 0; $i < count($entries); $i++){

               $entryID = $this->saveEntry($entries[$i]);

               // save relation
               $select = 'SELECT * FROM comp_guestbook_entry WHERE GuestbookID  = \''.$guestbookID.'\' AND EntryID = \''.$entryID.'\';';
               $result = $SQL->executeTextStatement($select);

               if($SQL->getNumRows($result) > 0){
                // end if
               }
               else{

                  // save relation
                  $insert = 'INSERT INTO comp_guestbook_entry (GuestbookID,EntryID) VALUES (\''.$guestbookID.'\',\''.$entryID.'\');';
                  $SQL->executeTextStatement($insert);

                // end if
               }

             // end for
            }

          // end if
         }

         // return guestbook id
         return $guestbookID;

       // end function
      }


      /**
      *  @public
      *
      *  Save a entry object.
      *
      *  @param Entry $entry The entry object
      *  @return string $entryID Id of the entry
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      *  Version 0.2, 26.03.2009 (Refactoring)<br />
      */
      public function saveEntry($entry){

         $SQL = &$this->__getConnection();
         $entryID = $entry->get('ID');

         if($entryID != null){

            $update = 'UPDATE entry SET
                       Name = \''.$SQL->escapeValue($entry->get('Name')).'\',
                       EMail = \''.$SQL->escapeValue($entry->get('EMail')).'\',
                       City = \''.$SQL->escapeValue($entry->get('City')).'\',
                       Website = \''.$SQL->escapeValue($entry->get('Website')).'\',
                       ICQ = \''.$SQL->escapeValue($entry->get('ICQ')).'\',
                       MSN = \''.$SQL->escapeValue($entry->get('MSN')).'\',
                       Skype = \''.$SQL->escapeValue($entry->get('Skype')).'\',
                       AIM = \''.$SQL->escapeValue($entry->get('AIM')).'\',
                       Yahoo = \''.$SQL->escapeValue($entry->get('Yahoo')).'\',
                       Text = \''.$SQL->escapeValue($entry->get('Text')).'\'
                       WHERE EntryID = \''.$entryID.'\';';
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
                          \''.$SQL->escapeValue($entry->get('Name')).'\',
                          \''.$SQL->escapeValue($entry->get('EMail')).'\',
                          \''.$SQL->escapeValue($entry->get('City')).'\',
                          \''.$SQL->escapeValue($entry->get('Website')).'\',
                          \''.$SQL->escapeValue($entry->get('ICQ')).'\',
                          \''.$SQL->escapeValue($entry->get('MSN')).'\',
                          \''.$SQL->escapeValue($entry->get('Skype')).'\',
                          \''.$SQL->escapeValue($entry->get('AIM')).'\',
                          \''.$SQL->escapeValue($entry->get('Yahoo')).'\',
                          \''.$SQL->escapeValue($entry->get('Text')).'\',
                          CURDATE(),
                          CURTIME()
                       );';
            $SQL->executeTextStatement($insert);
            $entryID = $SQL->getLastID();

          // end else
         }

         // check, whether the entry has comments
         $comments = $entry->getComments();
         if(count($comments) > 0){

            for($i = 0; $i < count($comments); $i++){

               // save comment itself
               $commentID = $this->saveComment($comments[$i]);

               // save relations
               $select = 'SELECT * FROM comp_entry_comment WHERE EntryID = \''.$entryID.'\' AND CommentID = \''.$commentID.'\';';
               $result = $SQL->executeTextStatement($select);

               if($SQL->getNumRows($result) > 0){
                // end if
               }
               else{

                  $insert = 'INSERT INTO comp_entry_comment (EntryID,CommentID) VALUES (\''.$entryID.'\',\''.$commentID.'\');';
                  $SQL->executeTextStatement($insert);

                // end if
               }

             // end for
            }

          // end if
         }

         // return entry id
         return $entryID;

       // end function
      }


      /**
      *  @public
      *
      *  Saves a comment.
      *
      *  @param Comment $comment The comment object
      *  @return string $commentID Id of the comment
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      *  Version 0.2, 26.03.2009 (Refactoring)<br />
      */
      public function saveComment($comment){

         $SQL = &$this->__getConnection();
         $commentID = $comment->get('ID');

         if($commentID != null){

            $update = 'UPDATE comment SET
                       Title = \''.$SQL->escapeValue($comment->get('Title')).'\',
                       Text = \''.$SQL->escapeValue($comment->get('Text')).'\'
                       WHERE CommentID = \''.$commentID.'\';';
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
                          \''.$SQL->escapeValue($comment->get('Title')).'\',
                          \''.$SQL->escapeValue($comment->get('Text')).'\',
                          CURDATE(),
                          CURTIME()
                       );';
            $SQL->executeTextStatement($insert);
            $commentID = $SQL->getLastID();

          // end else
         }

         // return comment if
         return $commentID;

       // end function
      }


      /**
      *  @public
      *
      *  Deletes a guestbook entry.
      *
      *  @param Entry $entry The entry object
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      *  Version 0.2, 26.03.2009 (Refactoring)<br />
      */
      public function deleteEntry($entry){

         $SQL = &$this->__getConnection();
         $select_commment = 'SELECT comment.CommentID AS ID FROM comment
                             INNER JOIN comp_entry_comment ON comment.CommentID = comp_entry_comment.CommentID
                             INNER JOIN entry ON comp_entry_comment.EntryID = entry.EntryID
                             WHERE entry.EntryID = \''.$entry->get('ID').'\';';
         $result_comment = $SQL->executeTextStatement($select_commment);

         while($data_comment = $SQL->fetchData($result_comment)){

            // delete comment
            $delete_comment = 'DELETE FROM comment WHERE CommentID = \''.$data_comment['ID'].'\';';
            $SQL->executeTextStatement($delete_comment);

            // delete relation
            $delete_comp_comment = 'DELETE FROM comp_entry_comment WHERE CommentID = \''.$data_comment['ID'].'\';';
            $SQL->executeTextStatement($delete_comp_comment);

            // reset variables
            $delete_comment = null;
            $delete_comp_comment = null;

          // end while
         }

         // delete entry itself
         $delete_entry = 'DELETE FROM entry WHERE EntryID = \''.$entry->get('ID').'\';';
         $SQL->executeTextStatement($delete_entry);

         // return composition
         $delete_comp_entry = 'DELETE FROM comp_guestbook_entry WHERE EntryID = \''.$entry->get('ID').'\';';
         $SQL->executeTextStatement($delete_comp_entry);

       // end function
      }


      /**
      *  @public
      *
      *  Deletes a comment
      *
      *  @param Comment $comment The comment object
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 19.05.2007<br />
      *  Version 0.2, 26.03.2009 (Refactoring)<br />
      */
      public function deleteComment($comment){

         $SQL = &$this->__getConnection();

         $delete_comp_comment = 'DELETE FROM comp_entry_comment WHERE CommentID = \''.$comment->get('ID').'\';';
         $SQL->executeTextStatement($delete_comp_comment);

         $delete_comment = 'DELETE FROM comment WHERE CommentID = \''.$comment->get('ID').'\';';
         $SQL->executeTextStatement($delete_comment);

       // end function
      }


      /**
      *  @private
      *
      *  Maps an result-set into the domain object.
      *
      *  @param array $entryResultSet The database result set
      *  @return Entry $entry The entry object
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      *  Version 0.2, 26.03.2009 (Refactoring)<br />
      */
      private function __mapEntry2DomainObject($entryResultSet){

         $entry = new Entry();

         if(isset($entryResultSet['EntryID'])){
            $entry->set('ID',$entryResultSet['EntryID']);
          // end if
         }
         if(isset($entryResultSet['Name'])){
            $entry->set('Name',$entryResultSet['Name']);
          // end if
         }
         if(isset($entryResultSet['EMail'])){
            $entry->set('EMail',$entryResultSet['EMail']);
          // end if
         }
         if(isset($entryResultSet['City'])){
            $entry->set('City',$entryResultSet['City']);
          // end if
         }
         if(isset($entryResultSet['Website'])){
            $entry->set('Website',$entryResultSet['Website']);
          // end if
         }
         if(isset($entryResultSet['ICQ'])){
            $entry->set('ICQ',$entryResultSet['ICQ']);
          // end if
         }
         if(isset($entryResultSet['MSN'])){
            $entry->set('MSN',$entryResultSet['MSN']);
          // end if
         }
         if(isset($entryResultSet['Skype'])){
            $entry->set('Skype',$entryResultSet['Skype']);
          // end if
         }
         if(isset($entryResultSet['AIM'])){
            $entry->set('AIM',$entryResultSet['AIM']);
          // end if
         }
         if(isset($entryResultSet['Yahoo'])){
            $entry->set('Yahoo',$entryResultSet['Yahoo']);
          // end if
         }
         if(isset($entryResultSet['Text'])){
            $entry->set('Text',$entryResultSet['Text']);
          // end if
         }
         if(isset($entryResultSet['Date'])){
            $entry->set('Date',$entryResultSet['Date']);
          // end if
         }
         if(isset($entryResultSet['Time'])){
            $entry->set('Time',$entryResultSet['Time']);
          // end if
         }

         return $entry;

       // end function
      }


      /**
      *  @private
      *
      *  Maps an result-set into the domain object.
      *
      *  @param array $guestbookResultSet The database result set
      *  @return Guestbook $guestbook The guestbook object
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      *  Version 0.2, 26.03.2009 (Refactoring)<br />
      */
      private function __mapGuestbook2DomainObject($guestbookResultSet){

         $guestbook = new Guestbook();

         if(isset($guestbookResultSet['GuestbookID'])){
            $guestbook->set('ID',$guestbookResultSet['GuestbookID']);
          // end if
         }
         if(isset($guestbookResultSet['Name'])){
            $guestbook->set('Name',$guestbookResultSet['Name']);
          // end if
         }
         if(isset($guestbookResultSet['Description'])){
            $guestbook->set('Description',$guestbookResultSet['Description']);
          // end if
         }
         if(isset($guestbookResultSet['Admin_Username'])){
            $guestbook->set('Admin_Username',$guestbookResultSet['Admin_Username']);
          // end if
         }
         if(isset($guestbookResultSet['Admin_Password'])){
            $guestbook->set('Admin_Password',$guestbookResultSet['Admin_Password']);
          // end if
         }

         return $guestbook;

       // end function
      }


      /**
      *  @private
      *
      *  Maps an result-set into the domain object.
      *
      *  @param array $commentResultSet The database result set
      *  @return Comment $comment The database result set
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      *  Version 0.2, 26.03.2009 (Refactoring)<br />
      */
      private function __mapComment2DomainObject($commentResultSet){

         $comment = new Comment();

         if(isset($commentResultSet['CommentID'])){
            $comment->set('ID',$commentResultSet['CommentID']);
          // end if
         }
         if(isset($commentResultSet['Title'])){
            $comment->set('Title',$commentResultSet['Title']);
          // end if
         }
         if(isset($commentResultSet['Text'])){
            $comment->set('Text',$commentResultSet['Text']);
          // end if
         }
         if(isset($commentResultSet['Date'])){
            $comment->set('Date',$commentResultSet['Date']);
          // end if
         }
         if(isset($commentResultSet['Time'])){
            $comment->set('Time',$commentResultSet['Time']);
          // end if
         }

         return $comment;

       // end function
      }

    // end class
   }
?>
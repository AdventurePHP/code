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

   import('modules::guestbook::biz', 'Guestbook');
   import('modules::guestbook::biz', 'Entry');
   import('modules::guestbook::biz', 'Comment');

   /**
    * @package modules::guestbook::data
    * @class GuestbookMapper
    *
    * DataMapper of the guestbook.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.04.2007<br />
    * Version 0.2, 07.01.2008 (Values are now quoted during insert/update)<br />
    */
   class GuestbookMapper extends APFObject {

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
      private function &getConnection() {

         // get configuration
         $config = $this->getConfiguration('modules::guestbook', 'guestbook.ini');
         $connectionKey = $config->getSection('Default')->getValue('Database.ConnectionKey');

         // create database
         $cM = &$this->__getServiceObject('core::database', 'ConnectionManager');
         $this->__DatabaseConnection = &$cM->getConnection($connectionKey);
         return $this->__DatabaseConnection;

      }

      /**
       *  @public
       *
       *  Loads an entry by id.
       *
       *  @param string $entryId Id of the desired entry
       *  @return Entry The desired entry.
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 12.04.2007<br />
       *  Version 0.2, 26.03.2009 (Refactoring)<br />
       */
      public function loadEntryByID($entryId) {

         $SQL = &$this->getConnection();
         $select = 'SELECT * FROM entry WHERE EntryID = \'' . $entryId . '\';';
         $result = $SQL->executeTextStatement($select);
         return $this->mapEntry2DomainObject($SQL->fetchData($result));

      }

      /**
       *  @public
       *
       *  Loads a comment by id.
       *
       *  @param string $commentId Id of the desired comment
       *  @return Comment The comment object
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 12.04.2007<br />
       *  Version 0.2, 26.03.2009 (Refactoring)<br />
       */
      public function loadCommentById($commentId) {

         $SQL = &$this->getConnection();
         $select = 'SELECT * FROM comment WHERE CommentID = \'' . $commentId . '\';';
         $result = $SQL->executeTextStatement($select);
         return $this->mapComment2DomainObject($SQL->fetchData($result));

      }

      /**
       *  @public
       *
       *  Loads a guestbook by id.
       *
       *  @param string $guestbookId Id of the desired guestbook
       *  @return Guestbook The guestbook object.
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 12.04.2007<br />
       *  Version 0.2, 26.03.2009 (Refactoring)<br />
       */
      public function loadGuestbookByID($guestbookId) {

         $SQL = &$this->getConnection();
         $select = 'SELECT * FROM guestbook WHERE GuestbookID = \'' . $guestbookId . '\';';
         $result = $SQL->executeTextStatement($select);
         return $this->mapGuestbook2DomainObject($SQL->fetchData($result));

      }

      /**
       *  @public
       *
       *  Loads an entry with it's comments.
       *
       *  @param string $entryId Id of the desired entry
       *  @return Entry The entry id with it's comments.
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 12.04.2007<br />
       *  Version 0.2, 26.03.2009 (Refactoring)<br />
       */
      public function loadEntryWithComments($entryId) {

         $SQL = &$this->getConnection();
         $entry = $this->loadEntryByID($entryId);
         $select = 'SELECT comment.CommentID AS ID FROM comment
                       INNER JOIN comp_entry_comment ON comment.CommentID = comp_entry_comment.CommentID
                       INNER JOIN entry ON comp_entry_comment.EntryID = entry.EntryID
                       WHERE entry.EntryID = \'' . $entry->getId() . '\';';
         $result = $SQL->executeTextStatement($select);

         while ($data = $SQL->fetchData($result)) {
            $entry->addComment($this->loadCommentById($data['ID']));
         }

         return $entry;

      }

      /**
       *  @public
       *
       *  Loads a guestbook with it's entries.
       *
       *  @param string $guestbookId Id of the desired guestbook
       *  @return Guestbook The desired guestbook.
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 12.04.2007<br />
       *  Version 0.2, 26.03.2009 (Refactoring)<br />
       */
      public function loadGuestbookWithEntries($guestbookId) {

         $guestbook = $this->loadGuestbookByID($guestbookId);

         $SQL = &$this->getConnection();
         $select = 'SELECT entry.EntryID AS ID FROM entry
                       INNER JOIN comp_guestbook_entry ON entry.EntryID = comp_guestbook_entry.EntryID
                       INNER JOIN guestbook ON comp_guestbook_entry.GuestbookID = guestbook.GuestbookID
                       WHERE guestbook.GuestbookID = \'' . $guestbook->getId() . '\';';
         $result = $SQL->executeTextStatement($select);

         while ($data = $SQL->fetchData($result)) {
            $guestbook->addEntry($this->loadEntryWithComments($data['ID']));
         }

         return $guestbook;

      }

      /**
       *  @public
       *
       *  Saves a guestbook.
       *
       *  @param Guestbook $guestbook The guestbook object
       *  @return string $guestbookID Id of the guestbook
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 12.04.2007<br />
       *  Version 0.2, 14.04.2007 (Fixed bug during saving)<br />
       *  Version 0.3, 26.03.2009 (Refactoring)<br />
       */
      public function saveGuestbook(Guestbook $guestbook) {

         $SQL = &$this->getConnection();

         // save the guestbook itself
         $guestbookId = $guestbook->getId();

         if ($guestbookId != null) {

            $update = 'UPDATE guestbook SET
                          Name = \'' . $SQL->escapeValue($guestbook->getName()) . '\',
                          Description = \'' . $SQL->escapeValue($guestbook->getDescription()) . '\',
                          Admin_Username = \'' . $SQL->escapeValue($guestbook->getAdminUsername()) . '\',
                          Admin_Password = \'' . $SQL->escapeValue($guestbook->getAdminPassword()) . '\'
                          WHERE GuestbookID = \'' . $guestbookId . '\';';
            $SQL->executeTextStatement($update);

         } else {

            $insert = 'INSERT INTO guestbook

                          (
                             Name,
                             Description,
                             Admin_Username,
                             Admin_Password
                          )

                          VALUES
                          (
                             \'' . $SQL->escapeValue($guestbook->getName()) . '\',
                             \'' . $SQL->escapeValue($guestbook->getDescription()) . '\',
                             \'' . $SQL->escapeValue($guestbook->getAdminUsername()) . '\',
                             \'' . $SQL->escapeValue($guestbook->getAdminPassword()) . '\'
                          );';
            $SQL->executeTextStatement($insert);
            $guestbookId = $SQL->getLastID();

         }

         // check, if an entry has comments and save them, too
         $entries = $guestbook->getEntries();
         if (count($entries) > 0) {

            for ($i = 0; $i < count($entries); $i++) {

               $entryID = $this->saveEntry($entries[$i]);

               // save relation
               $select = 'SELECT * FROM comp_guestbook_entry WHERE GuestbookID  = \'' . $guestbookId . '\' AND EntryID = \'' . $entryID . '\';';
               $result = $SQL->executeTextStatement($select);

               // save relation
               if ($SQL->getNumRows($result) == 0) {
                  $insert = 'INSERT INTO comp_guestbook_entry (GuestbookID,EntryID) VALUES (\'' . $guestbookId . '\',\'' . $entryID . '\');';
                  $SQL->executeTextStatement($insert);
               }

            }

         }

         return $guestbookId;

      }

      /**
       *  @public
       *
       *  Save a entry object.
       *
       *  @param Entry $entry The entry object
       *  @return string Id of the entry.
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 12.04.2007<br />
       *  Version 0.2, 26.03.2009 (Refactoring)<br />
       */
      public function saveEntry(Entry $entry) {

         $SQL = &$this->getConnection();
         $entryId = $entry->getId();

         if ($entryId != null) {

            $update = 'UPDATE entry SET
                          Name = \'' . $SQL->escapeValue($entry->getName()) . '\',
                          EMail = \'' . $SQL->escapeValue($entry->getEmail()) . '\',
                          City = \'' . $SQL->escapeValue($entry->getCity()) . '\',
                          Website = \'' . $SQL->escapeValue($entry->getWebsite()) . '\',
                          ICQ = \'' . $SQL->escapeValue($entry->getIcq()) . '\',
                          MSN = \'' . $SQL->escapeValue($entry->getMsn()) . '\',
                          Skype = \'' . $SQL->escapeValue($entry->getSkype()) . '\',
                          AIM = \'' . $SQL->escapeValue($entry->getAim()) . '\',
                          Yahoo = \'' . $SQL->escapeValue($entry->getYahoo()) . '\',
                          Text = \'' . $SQL->escapeValue($entry->getText()) . '\'
                          WHERE EntryID = \'' . $entryId . '\';';
            $SQL->executeTextStatement($update);

         } else {

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
                             \'' . $SQL->escapeValue($entry->getName()) . '\',
                             \'' . $SQL->escapeValue($entry->getEmail()) . '\',
                             \'' . $SQL->escapeValue($entry->getCity()) . '\',
                             \'' . $SQL->escapeValue($entry->getWebsite()) . '\',
                             \'' . $SQL->escapeValue($entry->getIcq()) . '\',
                             \'' . $SQL->escapeValue($entry->getMsn()) . '\',
                             \'' . $SQL->escapeValue($entry->getSkype()) . '\',
                             \'' . $SQL->escapeValue($entry->getAim()) . '\',
                             \'' . $SQL->escapeValue($entry->getYahoo()) . '\',
                             \'' . $SQL->escapeValue($entry->getText()) . '\',
                             CURDATE(),
                             CURTIME()
                          );';
            $SQL->executeTextStatement($insert);
            $entryId = $SQL->getLastID();

         }

         // check, whether the entry has comments
         $comments = $entry->getComments();
         if (count($comments) > 0) {

            for ($i = 0; $i < count($comments); $i++) {

               // save comment itself
               $commentId = $this->saveComment($comments[$i]);

               // save relations
               $select = 'SELECT * FROM comp_entry_comment WHERE EntryID = \'' . $entryId . '\' AND CommentID = \'' . $commentId . '\';';
               $result = $SQL->executeTextStatement($select);

               if ($SQL->getNumRows($result) == 0) {
                  $insert = 'INSERT INTO comp_entry_comment (EntryID,CommentID) VALUES (\'' . $entryId . '\',\'' . $commentId . '\');';
                  $SQL->executeTextStatement($insert);
               }

            }

         }

         return $entryId;

      }

      /**
       *  @public
       *
       *  Saves a comment.
       *
       *  @param Comment $comment The comment object
       *  @return string Id of the comment.
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 12.04.2007<br />
       *  Version 0.2, 26.03.2009 (Refactoring)<br />
       */
      public function saveComment(Comment $comment) {

         $SQL = &$this->getConnection();
         $commentId = $comment->getId();

         if ($commentId != null) {

            $update = 'UPDATE comment SET
                          Title = \'' . $SQL->escapeValue($comment->getTitle()) . '\',
                          Text = \'' . $SQL->escapeValue($comment->getText()) . '\'
                          WHERE CommentID = \'' . $commentId . '\';';
            $SQL->executeTextStatement($update);

         } else {

            $insert = 'INSERT INTO comment
                          (
                             Title,
                             Text,
                             Date,
                             Time
                          )
                          VALUES
                          (
                             \'' . $SQL->escapeValue($comment->getTitle()) . '\',
                             \'' . $SQL->escapeValue($comment->getText()) . '\',
                             CURDATE(),
                             CURTIME()
                          );';
            $SQL->executeTextStatement($insert);
            $commentId = $SQL->getLastID();

         }

         return $commentId;

      }

      /**
       *  @public
       *
       *  Deletes a guestbook entry.
       *
       *  @param Entry $entry The entry object
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 05.05.2007<br />
       *  Version 0.2, 26.03.2009 (Refactoring)<br />
       */
      public function deleteEntry(Entry $entry) {

         $SQL = &$this->getConnection();
         $select_commment = 'SELECT comment.CommentID AS ID FROM comment
                                INNER JOIN comp_entry_comment ON comment.CommentID = comp_entry_comment.CommentID
                                INNER JOIN entry ON comp_entry_comment.EntryID = entry.EntryID
                                WHERE entry.EntryID = \'' . $entry->getId() . '\';';
         $result_comment = $SQL->executeTextStatement($select_commment);

         while ($data_comment = $SQL->fetchData($result_comment)) {

            // delete comment
            $delete_comment = 'DELETE FROM comment WHERE CommentID = \'' . $data_comment['ID'] . '\';';
            $SQL->executeTextStatement($delete_comment);

            // delete relation
            $delete_comp_comment = 'DELETE FROM comp_entry_comment WHERE CommentID = \'' . $data_comment['ID'] . '\';';
            $SQL->executeTextStatement($delete_comp_comment);

            // reset variables
            $delete_comment = null;
            $delete_comp_comment = null;

         }

         // delete entry itself
         $delete_entry = 'DELETE FROM entry WHERE EntryID = \'' . $entry->getId() . '\';';
         $SQL->executeTextStatement($delete_entry);

         // return composition
         $delete_comp_entry = 'DELETE FROM comp_guestbook_entry WHERE EntryID = \'' . $entry->getId() . '\';';
         $SQL->executeTextStatement($delete_comp_entry);

      }

      /**
       *  @public
       *
       *  Deletes a comment
       *
       *  @param Comment $comment The comment object
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 19.05.2007<br />
       *  Version 0.2, 26.03.2009 (Refactoring)<br />
       */
      public function deleteComment(Comment $comment) {

         $SQL = &$this->getConnection();

         $delete_comp_comment = 'DELETE FROM comp_entry_comment WHERE CommentID = \'' . $comment->getId() . '\';';
         $SQL->executeTextStatement($delete_comp_comment);

         $delete_comment = 'DELETE FROM comment WHERE CommentID = \'' . $comment->getId() . '\';';
         $SQL->executeTextStatement($delete_comment);

      }

      /**
       *  @private
       *
       *  Maps an result-set into the domain object.
       *
       *  @param array $entryResultSet The database result set
       *  @return Entry $entry The entry object
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 12.04.2007<br />
       *  Version 0.2, 26.03.2009 (Refactoring)<br />
       */
      private function mapEntry2DomainObject($entryResultSet) {
         $entry = new Entry();
         $entry->setId($entryResultSet['EntryID']);
         $entry->setName($entryResultSet['Name']);
         $entry->setEmail($entryResultSet['EMail']);
         $entry->setCity($entryResultSet['City']);
         $entry->setWebsite($entryResultSet['Website']);
         $entry->setIcq($entryResultSet['ICQ']);
         $entry->setMsn($entryResultSet['MSN']);
         $entry->setSkype($entryResultSet['Skype']);
         $entry->setAim($entryResultSet['AIM']);
         $entry->setYahoo($entryResultSet['Yahoo']);
         $entry->setText($entryResultSet['Text']);
         $entry->setDate($entryResultSet['Date']);
         $entry->setTime($entryResultSet['Time']);
         return $entry;
      }

      /**
       *  @private
       *
       *  Maps an result-set into the domain object.
       *
       *  @param array $guestbookResultSet The database result set
       *  @return Guestbook $guestbook The guestbook object
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 12.04.2007<br />
       *  Version 0.2, 26.03.2009 (Refactoring)<br />
       */
      private function mapGuestbook2DomainObject(array $guestbookResultSet) {
         $guestbook = new Guestbook();
         $guestbook->setId($guestbookResultSet['GuestbookID']);
         $guestbook->setName($guestbookResultSet['Name']);
         $guestbook->setDescription($guestbookResultSet['Description']);
         $guestbook->setAdminUsername($guestbookResultSet['Admin_Username']);
         $guestbook->setAdminPassword($guestbookResultSet['Admin_Password']);
         return $guestbook;
      }

      /**
       *  @private
       *
       *  Maps an result-set into the domain object.
       *
       *  @param array $commentResultSet The database result set
       *  @return Comment The database result set
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 12.04.2007<br />
       *  Version 0.2, 26.03.2009 (Refactoring)<br />
       */
      private function mapComment2DomainObject(array $commentResultSet) {
         $comment = new Comment();
         $comment->setId($commentResultSet['CommentID']);
         $comment->setTitle($commentResultSet['Title']);
         $comment->setText($commentResultSet['Text']);
         $comment->setDate($commentResultSet['Date']);
         $comment->setTime($commentResultSet['Time']);
         return $comment;
      }

   }
?>
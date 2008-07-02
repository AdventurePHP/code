<?php
   import('modules::comments::biz','ArticleComment');
   import('core::database','MySQLHandler');


   /**
   *  @package modules::comments::date
   *  @class commentMapper
   *
   *  Implementiert die Daten-Schicht des Comments-Moduls.<br />
   *
   *  @author Christian W. Schäfer
   *  @version
   *  Version 0.1, 22.08.2007
   */
   class commentMapper extends coreObject
   {

      function commentMapper(){
      }


      /**
      *  @public
      *
      *  Läd ein ArticleComment-Objekt an Hand einer ID.<br />
      *
      *  @param string $ArticleCommentID ID des Eintrags
      *  @return ArticleComment $ArticleComment ArticleComment-Objekt
      *
      *  @author Christian W.Schäfer
      *  @version
      *  Version 0.1, 22.08.2007<br />
      */
      function loadArticleCommentByID($ArticleCommentID){

         // SQL-Handler holen
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');

         // Eintrag selektieren
         $select = 'SELECT ArticleCommentID, Name, EMail, Comment, Date, Time FROM article_comments WHERE ArticleCommentID = \''.$ArticleCommentID.'\';';
         $result = $SQL->executeTextStatement($select);

         // Objekt zurückgeben
         return $this->__mapArticleComment2DomainObject($SQL->fetchData($result));

       // end function
      }


      /**
      *  @public
      *
      *  Speichert ein ArticleComment-Objekt. Update wurde noch nicht implementiert.<br />
      *
      *  @param ArticleComment $ArticleComment ArticleComment-Objekt
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 22.08.2007<br />
      */
      function saveArticleComment($ArticleComment){

         // SQL-Handler holen
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');

         // Prüfen, ob Artikel bereits existiert
         if($ArticleComment->get('ID') == null){
            $insert = 'INSERT INTO article_comments (Name, EMail, Comment, Date, Time, CategoryKey) VALUES (\''.$ArticleComment->get('Name').'\',\''.$ArticleComment->get('EMail').'\',\''.$ArticleComment->get('Comment').'\',CURDATE(),CURTIME(),\''.$ArticleComment->get('CategoryKey').'\');';
            $SQL->executeTextStatement($insert);
          // end if
         }

       // end function
      }


      /**
      *  @private
      *
      *  Mappt ein Result-Array in ein ArticleComment-Objekt.<br />
      *
      *  @param array $ResultSet Ergebnis-Array des SQL-Statements
      *  @return ArticleComment $ArticleComment Gefülltes ArticleComment-Objekt
      *
      *  @author Christian W.Schäfer
      *  @version
      *  Version 0.1, 22.08.2007<br />
      */
      function __mapArticleComment2DomainObject($ResultSet){

         // Neues Objekt erstellen
         $ArticleComment = new ArticleComment();

         // ArticleCommentID
         if(isset($ResultSet['ArticleCommentID'])){
            $ArticleComment->set('ID',$ResultSet['ArticleCommentID']);
          // end if
         }

         // Name
         if(isset($ResultSet['Name'])){
            $ArticleComment->set('Name',$ResultSet['Name']);
          // end if
         }

         // EMail
         if(isset($ResultSet['EMail'])){
            $ArticleComment->set('EMail',$ResultSet['EMail']);
          // end if
         }

         // Comment
         if(isset($ResultSet['Comment'])){
            $ArticleComment->set('Comment',$ResultSet['Comment']);
          // end if
         }

         // Date
         if(isset($ResultSet['Date'])){
            $ArticleComment->set('Date',$ResultSet['Date']);
          // end if
         }

         // Time
         if(isset($ResultSet['Time'])){
            $ArticleComment->set('Time',$ResultSet['Time']);
          // end if
         }

         // Gefülltes Objekt zurückgeben
         return $ArticleComment;

       // end function
      }

    // end class
   }
?>
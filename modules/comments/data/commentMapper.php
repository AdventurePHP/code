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

   import('modules::comments::biz','ArticleComment');
   import('core::database','MySQLHandler');


   /**
    * @namespace modules::comments::date
    * @class commentMapper
    *
    * Represents the data layer component of the comment function. Loads and saves entries.
    *
    * @author Christian W. Schäfer
    * @version
    * Version 0.1, 22.08.2007
    */
   class commentMapper extends coreObject
   {

      function commentMapper(){
      }


      /**
       * @public
       *
       * Loads an article comment object by id. Can be used by the pager.
       *
       * @param string $commentId ID des Eintrags
       * @return ArticleComment A comment object.
       *
       * @author Christian W.Schäfer
       * @version
       * Version 0.1, 22.08.2007<br />
       */
      public function loadArticleCommentByID($commentId){

         $SQL = &$this->__getConnection();
         $select = 'SELECT ArticleCommentID, Name, EMail, Comment, Date, Time 
                    FROM article_comments
                    WHERE ArticleCommentID = \''.$commentId.'\';';
         $result = $SQL->executeTextStatement($select);
         return $this->__mapArticleComment2DomainObject($SQL->fetchData($result));

       // end function
      }


      /**
       * @public
       *
       * Saves a new comment.
       *
       * @param ArticleComment $comment The domain object to save.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 22.08.2007<br />
       */
      public function saveArticleComment($comment){

         $SQL = &$this->__getConnection();
         if($comment->get('ID') == null){
            $insert = 'INSERT INTO article_comments (Name, EMail, Comment, Date, Time, CategoryKey) VALUES (\''.$comment->get('Name').'\',\''.$comment->get('EMail').'\',\''.$comment->get('Comment').'\',CURDATE(),CURTIME(),\''.$comment->get('CategoryKey').'\');';
            $SQL->executeTextStatement($insert);
          // end if
         }

       // end function
      }

      /**
       * @public
       *
       * Returns the initialized database connection (reference!) for the
       * current application instance.
       *
       * @return AbstractDatabaseHandler The database connection.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 09.06.2008<br />
       */
      private function &__getConnection(){

         $cM = &$this->__getServiceObject('core::database','connectionManager');
         $config = $this->__getConfiguration('modules::comments','comments');
         $connectionKey = $config->getValue('Default','Database.ConnectionKey');
         if($connectionKey == null){
            trigger_error('[commentMapper::__getConnection()] The module\'s configuration file '
               .'does not contain a valid database connection key. Please specify the database '
               .'configuration according to the example configuration files!',E_USER_ERROR);
            exit();
         }
         return $cM->getConnection($connectionKey);

       // end function
      }


      /**
       * @private
       *
       * Mapps a database result set into s domain object.
       *
       * @param string[] $resultSet MySQL (database) result array.
       * @return ArticleComment A initialized domain object.
       *
       * @author Christian W.Schäfer
       * @version
       * Version 0.1, 22.08.2007<br />
       */
      private function __mapArticleComment2DomainObject($resultSet){

         $comment = new ArticleComment();

         if(isset($resultSet['ArticleCommentID'])){
            $comment->set('ID',$resultSet['ArticleCommentID']);
          // end if
         }
         if(isset($resultSet['Name'])){
            $comment->set('Name',$resultSet['Name']);
          // end if
         }
         if(isset($resultSet['EMail'])){
            $comment->set('EMail',$resultSet['EMail']);
          // end if
         }
         if(isset($resultSet['Comment'])){
            $comment->set('Comment',$resultSet['Comment']);
          // end if
         }
         if(isset($resultSet['Date'])){
            $comment->set('Date',$resultSet['Date']);
          // end if
         }
         if(isset($resultSet['Time'])){
            $comment->set('Time',$resultSet['Time']);
          // end if
         }

         return $comment;

       // end function
      }

    // end class
   }
?>
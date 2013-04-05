<?php
namespace APF\modules\comments\data;

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
use APF\core\database\AbstractDatabaseHandler;
use APF\core\database\ConnectionManager;
use APF\core\pagecontroller\APFObject;
use APF\modules\comments\biz\ArticleComment;

/**
 * @package APF\modules\comments\date
 * @class ArticleCommentMapper
 *
 * Represents the data layer component of the comment function. Loads and saves entries.
 *
 * @author Christian W. Schäfer
 * @version
 * Version 0.1, 22.08.2007
 */
class ArticleCommentMapper extends APFObject {

   /**
    * @public
    *
    * Loads an article comment object by id. Can be used by the pager.
    *
    * @param string $commentId ID of the entry.
    * @return ArticleComment The comment object.
    *
    * @author Christian W. Schäfer
    * @version
    * Version 0.1, 22.08.2007<br />
    */
   public function loadArticleCommentByID($commentId) {

      $SQL = & $this->getConnection();
      $select = 'SELECT ArticleCommentID, Name, EMail, Comment, Date, Time
                    FROM article_comments
                    WHERE ArticleCommentID = \'' . $commentId . '\';';
      $result = $SQL->executeTextStatement($select);
      return $this->mapArticleComment2DomainObject($SQL->fetchData($result));
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
   public function saveArticleComment(ArticleComment $comment) {

      $conn = & $this->getConnection();
      if ($comment->getId() == null) {
         $insert = 'INSERT INTO article_comments
                       (Name, EMail, Comment, Date, Time, CategoryKey)
                       VALUES
                       (\'' . $conn->escapeValue($comment->getName()) . '\',\'' . $conn->escapeValue($comment->getEmail()) . '\',\'' . $conn->escapeValue($comment->getComment()) . '\',CURDATE(),CURTIME(),\'' . $comment->getCategoryKey() . '\');';
         $conn->executeTextStatement($insert);
      }
   }

   /**
    * @public
    *
    * Returns the initialized database connection (reference!) for the
    * current application instance.
    *
    * @return AbstractDatabaseHandler The database connection.
    * @throws \InvalidArgumentException In case the connection key does not refer to a valid connection definition.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.06.2008<br />
    */
   private function &getConnection() {

      /* @var $cM ConnectionManager */
      $cM = & $this->getServiceObject('APF\core\database\ConnectionManager');
      $config = $this->getConfiguration('APF\modules\comments', 'comments.ini');
      $connectionKey = $config->getSection('Default')->getValue('Database.ConnectionKey');
      if ($connectionKey == null) {
         throw new \InvalidArgumentException('[ArticleCommentMapper::getConnection()] The module\'s '
                  . 'configuration file does not contain a valid database connection key. Please '
                  . 'specify the database configuration according to the example configuration files!',
            E_USER_ERROR);
      }
      return $cM->getConnection($connectionKey);
   }

   /**
    * @private
    *
    * Maps a database result set into s domain object.
    *
    * @param string[] $resultSet MySQL (database) result array.
    * @return ArticleComment A initialized domain object.
    *
    * @author Christian W.Schäfer
    * @version
    * Version 0.1, 22.08.2007<br />
    */
   private function mapArticleComment2DomainObject($resultSet) {
      $comment = new ArticleComment();
      $comment->setId($resultSet['ArticleCommentID']);
      $comment->setName($resultSet['Name']);
      $comment->setEmail($resultSet['EMail']);
      $comment->setComment($resultSet['Comment']);
      $comment->setDate($resultSet['Date']);
      $comment->setTime($resultSet['Time']);
      return $comment;
   }

}

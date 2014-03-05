<?php
namespace APF\extensions\news\pres\documentcontroller\backend;

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
use APF\extensions\news\pres\documentcontroller\NewsBaseController;
use APF\tools\link\Url;
use APF\tools\request\RequestHandler;
use APF\tools\link\LinkGenerator;

/**
 * @package APF\extensions\news\pres\documentcontroller\backend
 * @class NewsDeleteController
 *
 * Document controller for deleting news.
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>
 * @version
 * Version 1.0,  18.06.2011<br />
 */
class NewsDeleteController extends NewsBaseController {

   public function transformContent() {

      $deleteId = RequestHandler::getValue('deletenewsid');
      $deleteYes = (bool)RequestHandler::getValue('deleteyes', false);

      $newsManager = $this->getNewsManager();

      $news = $newsManager->getNewsById((int)$deleteId);
      if ($news === null) {
         $this->getTemplate('error')->transformOnPlace();
         return;
      }

      if ($deleteYes === true) {
         $newsManager->deleteNews($news);
         $tpl = $this->getTemplate('success');
         $tpl->transformOnPlace();
         return;
      }

      $tpl = $this->getTemplate('delete');
      $tpl->setPlaceHolder(
         'LinkYes', LinkGenerator::generateUrl(
            Url::fromCurrent()->mergeQuery(
               array(
                  'backendview' => 'delete',
                  'deletenewsid' => (int)$deleteId,
                  'deleteyes' => 'true'
               )
            )
         )
      );
      $tpl->setPlaceHolder(
         'LinkNo', LinkGenerator::generateUrl(
            Url::fromCurrent()->mergeQuery(
               array(
                  'backendview' => 'list',
                  'deletenewsid' => null
               )
            )
         )
      );
      $tpl->transformOnPlace();
   }

}

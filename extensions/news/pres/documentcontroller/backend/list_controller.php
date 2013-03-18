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
use APF\tools\link\LinkGenerator;

/**
 * @package extensions::news::pres::documentcontroller::backend
 * @class list_controller
 *
 * Document controller for listing the news in the backend with links for editing
 * and deleting.
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>
 * @version
 * Version 1.0,  16.06.2011<br />
 */
class list_controller extends NewsBaseController {

   public function transformContent() {

      $appKey = $this->getAppKey();

      $newsManager = $this->getNewsManager();

      $Count = $newsManager->getNewsCount($appKey);
      $NewsList = $newsManager->getNews(0, $Count, 'DESC', $appKey);

      if (count($NewsList) === 0) {
         $this->getTemplate('noentry')->transformOnPlace();
         return;
      }

      $DataArray = array();

      // retrieve the charset from the registry to guarantee interoperability!
      $charset = Registry::retrieve('apf::core', 'Charset');

      foreach ($NewsList as &$News) {
         $DataArray[] = array(
            'Title' => htmlentities($News->getTitle(), ENT_QUOTES, $charset, false),
            'Date' => $News->getProperty('CreationTimestamp'),
            'LinkEdit' => LinkGenerator::generateUrl(URL::fromCurrent()->mergeQuery(
                  array(
                     'backendview' => 'edit',
                     'editnewsid' => (int)$News->getObjectId()
                  )
               )
            ),
            'LinkDelete' => LinkGenerator::generateUrl(URL::fromCurrent()->mergeQuery(
                  array(
                     'backendview' => 'delete',
                     'deletenewsid' => (int)$News->getObjectId()
                  )
               )
            )
         );
      }

      $I = $this->getIterator('newslist');
      $I->fillDataContainer($DataArray);
      $I->transformOnPlace();
   }

   /**
    * Overwriting parent's function
    *
    * @return string The application identifier (for login purposes).
    */
   protected function getAppKey() {
      return $this->getDocument()->getParentObject()->getAttribute('app-ident', $this->getContext());
   }
}

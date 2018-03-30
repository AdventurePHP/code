<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
namespace APF\modules\newspager\biz\actions;

use APF\core\frontcontroller\AbstractFrontControllerAction;
use APF\core\http\HeaderImpl;
use APF\modules\newspager\data\NewsPagerProvider;

/**
 * Front controller action implementation for AJAX style loading of a news page.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 02.20.2008<br />
 */
class NewsPagerAjaxBackendAction extends AbstractFrontControllerAction {

   /**
    * Implements the abstract run() method to deliver the news XML for the AJAX call.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 02.02.2007<br />
    * Version 0.2, 05.02.2008 (language is now directly taken from the AJAX request)<br />
    * Version 0.3, 18.09.2008 (Added dynamic data dir behaviour)<br />
    */
   public function run() {

      $input = $this->getParameters();

      $page = $input->getParameter('page');
      $dataDir = base64_decode($input->getParameter('datadir'));

      // inject the language here to ease service creation
      $this->setLanguage($input->getParameter('lang'));

      // load news object
      /* @var $provider NewsPagerProvider */
      $provider = $this->getServiceObject(NewsPagerProvider::class);
      $news = $provider->getNewsByPage($dataDir, $page);

      // send json
      $response = $this->getResponse();
      $response->setHeader(new HeaderImpl('Content-Type', 'application/json'));

      $response->setBody(json_encode([
            'headline'    => $news->getHeadline(),
            'subheadline' => $news->getSubHeadline(),
            'content'     => $news->getContent(),
            'newscount'   => $news->getNewsCount()
      ]));

      // close application, since we've done all the work here.
      $response->send();
   }

}

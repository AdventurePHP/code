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
namespace APF\modules\newspager\pres\controller;

use APF\core\pagecontroller\BaseDocumentController;
use APF\modules\newspager\data\NewsPagerProvider;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;
use InvalidArgumentException;

/**
 * Document controller for the news pager module.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 02.20.2008<br />
 */
class NewsPagerController extends BaseDocumentController {

   /**
    * Implements the abstract transformation function of the BaseDocumentController class.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 02.20.2008<br />
    * Version 0.2, 05.01.2008 (language is now published to the java script code)<br />
    * Version 0.3, 18.09.2008 (Introduced datadir attribute to be able to operate the module in more than one application)<br />
    */
   public function transformContent() {

      // get current data dir or trigger error
      $dataDir = $this->getDocument()->getAttribute('datadir');
      if ($dataDir === null) {
         throw new InvalidArgumentException('[NewsPagerController::transformContent()] Tag '
               . 'attribute "datadir" was not present in the &lt;core:importdesign /&gt; tag '
               . 'definition! Please specify a news content directory!');
      }

      // load default news page
      /* @var $provider NewsPagerProvider */
      $provider = & $this->getServiceObject('APF\modules\newspager\data\NewsPagerProvider');
      $newsItem = $provider->getNewsByPage($dataDir, 1);

      // fill place holders
      $this->setPlaceHolder('NewsLanguage', $this->getLanguage());
      $this->setPlaceHolder('NewsCount', $newsItem->getNewsCount());
      $this->setPlaceHolder('Headline', $newsItem->getHeadline());
      $this->setPlaceHolder('SubHeadline', $newsItem->getSubHeadline());
      $this->setPlaceHolder('Content', $newsItem->getContent());

      // set news service base url
      $url = LinkGenerator::generateActionUrl(
            Url::fromCurrent(),
            'APF\modules\newspager\biz',
            'Pager'
      );
      $this->setPlaceHolder('ActionUrl', $url);

      $this->setPlaceHolder('DataDir', base64_encode($dataDir));

      if ($this->getLanguage() == 'de') {
         $this->setPlaceHolder('ErrorMsg', 'Es ist ein Fehler beim Aufrufen der News aufgetreten! Bitte versuchen Sie es später wieder.');
      } else {
         $this->setPlaceHolder('ErrorMsg', 'Requesting the next news page failed! Please try again later.');
      }
   }

}

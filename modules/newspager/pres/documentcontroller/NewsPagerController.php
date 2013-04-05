<?php
namespace APF\modules\newspager\pres\documentcontroller;

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
use APF\core\pagecontroller\BaseDocumentController;
use APF\core\registry\Registry;
use APF\modules\newspager\biz\NewsPagerManager;

/**
 * @package modules::newspager::pres
 * @class NewsPagerController
 *
 * Document controller for the newspager module.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 02.20.2008<br />
 */
class NewsPagerController extends BaseDocumentController {

   /**
    * @public
    *
    *  Implements the abstract transformation function of the BaseDocumentController class.<br />
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
         throw new \InvalidArgumentException('[NewsPagerController::transformContent()] Tag '
               . 'attribute "datadir" was not present in the &lt;core:importdesign /&gt; tag '
               . 'definition! Please specify a news content directory!');
      }

      // get manager
      /* @var $manager NewsPagerManager */
      $manager = & $this->getAndInitServiceObject('APF\modules\newspager\biz\NewsPagerManager', $dataDir);

      // load default news page
      $newsItem = $manager->getNewsByPage();

      // fill place holders
      $this->setPlaceHolder('NewsLanguage', $this->getLanguage());
      $this->setPlaceHolder('NewsCount', $newsItem->getNewsCount());
      $this->setPlaceHolder('Headline', $newsItem->getHeadline());
      $this->setPlaceHolder('Subheadline', $newsItem->getSubHeadline());
      $this->setPlaceHolder('Content', $newsItem->getContent());

      // set news service base url
      if (Registry::retrieve('APF\core', 'URLRewriting') === true) {
         $this->setPlaceHolder('NewsServiceBaseURL', '/~/modules_newspager_biz-action/Pager/page/');
         $this->setPlaceHolder('NewsServiceLangParam', '/lang/');
         $this->setPlaceHolder('NewsServiceDataDir', '/datadir/' . base64_encode($dataDir));
      } else {
         $this->setPlaceHolder('NewsServiceBaseURL', './?modules_newspager_biz-action:Pager=page:');
         $this->setPlaceHolder('NewsServiceLangParam', '|lang:');
         $this->setPlaceHolder('NewsServiceDataDir', '|datadir:' . base64_encode($dataDir));
      }

      if ($this->getLanguage() == 'de') {
         $this->setPlaceHolder('ErrorMsg', 'Es ist ein Fehler beim Aufrufen der News aufgetreten! Bitte versuchen Sie es später wieder.');
      } else {
         $this->setPlaceHolder('ErrorMsg', 'Requesting the next news page failed. Please come back again later.');
      }
   }

}

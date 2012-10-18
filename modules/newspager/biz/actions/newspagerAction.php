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
import('modules::newspager::biz', 'newspagerManager');

/**
 * @package modules::newspager::biz
 * @class newspagerAction
 *
 * Front controller action implemenatation for AJAX style loading of a news page.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 02.20.2008<br />
 */
class newspagerAction extends AbstractFrontcontrollerAction {

   /**
    * @public
    *
    * Implements the abstract run() method to deliver the news XML for the AJAX call.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 02.02.2007<br />
    * Version 0.2, 05.02.2008 (language is now directly taken from the AJAX request)<br />
    * Version 0.3, 18.09.2008 (Added dynamic data dir behaviour)<br />
    */
   public function run() {

      $page = $this->getInput()->getAttribute('page');
      $dataDir = base64_decode($this->getInput()->getAttribute('datadir'));

      // inject the language here to ease service creation
      $this->setLanguage($this->getInput()->getAttribute('lang'));

      /* @var $nM newspagerManager */
      $nM = &$this->getAndInitServiceObject('modules::newspager::biz', 'newspagerManager', $dataDir);

      // load news object
      $news = $nM->getNewsByPage($page);
      /* @var $news newspagerContent */

      // create xml
      $xml = (string)'';
      $xml .= '<?xml version="1.0" encoding="utf-8" ?>';
      $xml .= '<news>';
      $xml .= '<headline>' . $news->getHeadline() . '</headline>';
      $xml .= '<subheadline>' . $news->getSubHeadline() . '</subheadline>';
      $xml .= '<content>' . $news->getContent() . '</content>';
      $xml .= '<newscount>' . $news->getNewsCount() . '</newscount>';
      $xml .= '</news>';

      // send xml
      header('Content-Type: text/xml');
      echo $xml;

      // close application, since we've done all the work here.
      exit(0);
   }

}

<?php
namespace APF\modules\comments\pres\documentcontroller;

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
use APF\modules\comments\biz\ArticleComment;
use APF\modules\comments\biz\ArticleCommentManager;
use APF\modules\comments\pres\documentcontroller\CommentBaseDocumentController;
use APF\tools\link\Url;
use APF\tools\string\AdvancedBBCodeParser;
use APF\tools\link\LinkGenerator;

/**
 * @package modules::comments::pres::documentcontroller
 * @class CommentListingController
 *
 * Implements the document controller for the 'listing.html' template.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 22.08.2007<br />
 * Version 0.2, 20.04.2008 (Added a display restriction)<br />
 * Version 0.3, 12.06.2008 (Removed the display restriction)<br />
 */
class CommentListingController extends CommentBaseDocumentController {

   /**
    * @public
    *
    *  Displays the paged comment list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 22.08.2007<br />
    * Version 0.2, 02.09.2007<br />
    * Version 0.3, 09.03.2008 (Changed deactivation due to indexation)<br />
    * Version 0.4, 12.06.2008 (Removed display limitation quick hack)<br />
    * Version 0.5, 30.01.2009 (Replaced the bbCodeParser with the AdvancedBBCodeParser)<br />
    */
   public function transformContent() {

      /* @var $m ArticleCommentManager */
      $m = &$this->getAndInitServiceObject('modules::comments::biz', 'ArticleCommentManager', $this->getCategoryKey());

      // load the entries using the business component
      $entries = $m->loadEntries();

      $buffer = (string)'';
      $template = &$this->getTemplate('ArticleComment');

      // init bb code parser (remove some provider, that we don't need configuration files)
      /* @var $bP AdvancedBBCodeParser */
      $bP = &$this->getServiceObject('tools::string', 'AdvancedBBCodeParser');
      $bP->removeProvider('standard.font.color');
      $bP->removeProvider('standard.font.size');

      $i = 1;
      foreach ($entries as $entry) {

         /* @var $entry ArticleComment */
         $template->setPlaceHolder('Number', $i++);
         $template->setPlaceHolder('Name', $entry->getName());
         $template->setPlaceHolder('Date', \DateTime::createFromFormat('Y-m-d', $entry->getDate())->format('d.m.Y'));
         $template->setPlaceHolder('Time', $entry->getTime());
         $template->setPlaceHolder('Comment', $bP->parseCode($entry->getComment()));

         $buffer .= $template->transformTemplate();
      }

      // display hint, if no entries are to display
      if (count($entries) < 1) {
         $Template__NoEntries = &$this->getTemplate('NoEntries');
         $buffer = $Template__NoEntries->transformTemplate();
      }

      // display the list
      $this->setPlaceHolder('Content', $buffer);

      // display the pager
      $this->setPlaceHolder('Pager', $m->getPager('comments'));

      // get the pager url params from the business component
      // to be able to delete them from the url.
      $urlParams = $m->getURLParameter();

      // generate the add comment link
      $this->setPlaceHolder('Link',
         LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(array(
                  $urlParams['PageName'] => '',
                  $urlParams['CountName'] => '',
                  'coview' => 'form'
               )
            )
         )
      );
   }

}

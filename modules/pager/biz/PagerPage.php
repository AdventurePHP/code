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

/**
 * @package modules::pager::biz
 * @class PagerPage
 *
 * Represents the business object of the pager module.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 06.08.2006<br />
 * Version 0.2, 22.09.2010<br />
 */
final class PagerPage {

   /**
    * @var int The page id.
    */
   private $page;

   /**
    * @var string Hyperlink to the current page.
    */
   private $link;

   /**
    * @var boolean Indicates if the current page is selected.
    */
   private $isSelected;

   /**
    * @var int Indicates the entries count on the current page.
    */
   private $entriesCount;

   /**
    * @var int Indicates the total amount of pages.
    */
   private $pageCount;

   public function __construct() {
      $this->page = (int)0;
      $this->link = (string)'';
      $this->isSelected = false;
      $this->entriesCount = (int)0;
      $this->pageCount = (int)0;
   }

   public function getPage() {
      return $this->page;
   }

   public function setPage($page) {
      $this->page = $page;
   }

   public function getLink() {
      return $this->link;
   }

   public function setLink($link) {
      $this->link = $link;
   }

   public function isSelected() {
      return $this->isSelected;
   }

   public function setSelected($isSelected) {
      $this->isSelected = $isSelected;
   }

   public function getEntriesCount() {
      return $this->entriesCount;
   }

   public function setEntriesCount($entriesCount) {
      $this->entriesCount = $entriesCount;
   }

   public function getPageCount() {
      return $this->pageCount;
   }

   public function setPageCount($pageCount) {
      $this->pageCount = $pageCount;
   }

}

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

   import('tools::link', 'FrontcontrollerLinkHandler');
   import('tools::request', 'RequestHandler');
   import('modules::pager::biz', 'PagerPage');
   import('modules::pager::data', 'PagerMapper');

   /**
    * @package modules::pager::biz
    * @class PagerManager
    *
    * Represents a concrete pager.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.08.2006<br />
    * Version 0.2, 14.08.2006 (Added new class variables)<br />
    * Version 0.3, 16.08.2006 (Added configuration for the count and entries statements)<br />
    * Version 0.4, 13.04.2007 (Added the possibility to add params from the application)<br />
    * Version 0.5, 25.01.2009 (Refactoring of the API, refactoring of the functionality)<br />
    */
   final class PagerManager extends APFObject {

      /**
       * @private
       * @since 0.5
       * Contains the current configuration.
       * @var string[]
       */
      private $section = null;
      
      /**
       * @private
       * Contains the desired anchor name.
       * @var string
       */
      private $anchorName = null;

      /**
       *  @public
       *
       *  Initializes the pager. Loads the desired config section.
       *
       *  @param string $initParam the name of the config section.
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 06.08.2006<br />
       *  Version 0.2, 16.08.2006 (Added more params to be applied to the count statement)<br />
       *  Version 0.3, 29.03.2007 (Renamed to "init()" to support the ServiceManager)<br />
       *  Version 0.4, 30.03.2007 (Removed the old iniHandler component)<br />
       *  Version 0.5, 13.04.2007 (Enhanced the method, that the pager can be created using the fabric)<br />
       *  Version 0.6, 26.04.2008 (The statement params are now casted to int by default)<br />
       *  Version 0.7, 25.01.2009 (Complete redesign / refactoring due to pager design changes. Now the pager can be used together with the GenericORMapper)<br />
       */
      public function init($initParam) {

         // initialize the config
         $config = $this->getConfiguration('modules::pager', 'pager');
         $this->section = $config->getSection($initParam);

         // translate the cache directive
         if ($this->section->getValue('Pager.CacheInSession') === null || $this->section->getValue('Pager.CacheInSession') === 'false') {
            $this->section->setValue('Pager.CacheInSession', false);
         } else {
            $this->section->setValue('Pager.CacheInSession', true);
         }

       // end function
      }

      /**
       *  @private
       *
       *  Returns the statement params needed by the pager's data layer.
       *
       *  @param string[] $addStmtParams Additional statement parameters.
       *  @return string[] A list of default statement params.
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 24.01.2009<br />
       */
      private function getStatementParams($addStmtParams = array()) {
         if ($this->isDynamicPageSizeActivated()) {
            $entriesCount = (int) RequestHandler::getValue($this->section->getValue('Pager.ParameterCountName'), $this->section->getValue('Pager.EntriesPerPage'));
         } else {
            $entriesCount = (int) $this->section->getValue('Pager.EntriesPerPage');
         }

         $defaultParams = array(
             'Start' => (int) RequestHandler::getValue($this->section->getValue('Pager.ParameterPageName'), 1) * $entriesCount - $entriesCount,
             'EntriesCount' => $entriesCount
         );
         return array_merge($defaultParams, $this->generateStatementParams($this->section->getValue('Pager.EntriesStatement.Params')), $addStmtParams);
       // end function
      }

      /**
       *  @public
       *
       *  Loads the ids of the entries of the current page.
       *
       *  @param string[] $addStmtParams additional statement parameters.
       *  @return int[] List of entry ids for the current page.
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 05.08.2006<br />
       *  Version 0.2, 06.08.2006<br />
       *  Version 0.3, 16.08.2006 (Added the enhanced param configuration opportunity)<br />
       *  Version 0.4, 24.01.2009 (Changed the API of the method. Moved the additional param handling to this method)<br />
       */
      public function loadEntries($addStmtParams = array()) {
         $m = &$this->__getAndInitServiceObject('modules::pager::data', 'PagerMapper', $this->section->getValue('Pager.DatabaseConnection'));
         return $m->loadEntries($this->section->getValue('Pager.StatementNamespace'), $this->section->getValue('Pager.EntriesStatement'), $this->getStatementParams($addStmtParams), $this->section->getValue('Pager.CacheInSession'));
      }

      /**
       * @public
       *
       * Loads a list of domain objects using a given data layer component.
       *
       * @param APFObject $dataComponent instance of a data component, that loads the domain objects directly
       * @param string $loadMethod name of the load method for the domain object
       * @param string[] $addStmtParams additional statement parameters
       * @return APFObject[] List of domain objects for the current page.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 01.09.2007<br />
       * Version 0.2, 18.09.2007 (Introduced PHP 4 compatibility)<br />
       * Version 0.3, 24.01.2009 (Added the $addStmtParams param to the API)<br />
       * Version 0.4, 25.01.2009 (Refactored the function. Now uses the $this->loadEntries() to load the ids)<br />
       * Version 0.5, 27.12.2010 (Bugfix: In case of empty results, no empty objects are returned any more.)<br />
       */
      public function loadEntriesByAppDataComponent(&$dataComponent, $loadMethod, $addStmtParams = array()) {

         // check, if the load method exists
         if (in_array($loadMethod, get_class_methods($dataComponent))) {

            // select the ids of the desired entries
            $entryIds = $this->loadEntries($addStmtParams);
            if ($entryIds === false) {
               return array();
            }

            // load the entries using the data layer component
            $entries = array();
            for ($i = 0; $i < count($entryIds); $i++) {
               $entries[] = $dataComponent->{$loadMethod}($entryIds[$i]);
            }

            return $entries;

         }

         throw new InvalidArgumentException('[PagerManager->loadEntriesByAppDataComponent()] '
                 . 'Given data component (' . get_class($dataComponent) . ') has no method "'
                 . $loadMethod . '"! Thus, no entries can be loaded!', E_USER_ERROR);
      }

      /**
       *  @public
       *
       *  Sets the anchor name.
       *
       *  @param string $anchorName The name of the desired anchor.
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 25.01.2009<br />
       */
      public function setAnchorName($anchorName = null) {
         $this->anchorName = $anchorName;
      }

      /**
       *  @public
       *
       *  Returns the anchor name.
       *
       *  @return string The name of the anchor
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 25.01.2009<br />
       */
      public function getAnchorName() {
         return $this->anchorName;
      }

      /**
       *  @public
       *
       *  Creates the graphical output of the pagerc concerning the configured presentation layer template.
       *
       *  @param array $addStmtParams list of additional statement params
       *  @return string The HTML representation of the pager
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 05.08.2006<br />
       *  Version 0.2, 11.03.2007 (Ported to new document controller, removed MessageQueue)<br />
       *  Version 0.3, 29.08.2007 (Anchor name is not set as the document's attribute)<br />
       *  Version 0.4, 02.03.2008 (The page is now applied the context and language)<br />
       */
      public function getPager($addStmtParams = array()) {

         $pager = new Page();

         // apply context and language (form configuration purposes!)
         $pager->setLanguage($this->__Language);
         $pager->setContext($this->__Context);

         // load the econfigured design
         $pager->loadDesign($this->section->getValue('Pager.DesignNamespace'), $this->section->getValue('Pager.DesignTemplate'));

         // add the necessary config params and pages
         $document = &$pager->getRootDocument();
         $document->setAttribute('Pages', $this->createPages4PagerDisplay($addStmtParams));
         $document->setAttribute('Config', array(
               'ParameterPageName' => $this->section->getValue('Pager.ParameterPageName'),
               'ParameterCountName' => $this->section->getValue('Pager.ParameterCountName'),
               'EntriesPerPage' => $this->section->getValue('Pager.EntriesPerPage'),
               'DynamicPageSizeActivated' => $this->isDynamicPageSizeActivated()
            )
         );

         // add the anchor if desired
         if ($this->anchorName !== null) {
            $document->setAttribute('AnchorName', $this->anchorName);
         }

         // transform pager GUI representation
         return $pager->transform();

       // end function
      }

      /**
       * @public
       *
       * Returns the name of the current URL params of the pager. The array featiures the following
       * offsets:
       * <ul>
       *   <li>PageName: the name of the page param</li>
       *   <li>CountName: the name of the count per page param</li>
       * <ul>
       *
       * @return string[] Url params of the pager.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 17.03.2007<br />
       */
      public function getPagerURLParameters() {
         return array(
             'PageName' => $this->section->getValue('Pager.ParameterPageName'),
             'CountName' => $this->section->getValue('Pager.ParameterCountName')
         );
      }

      /**
       *  @private
       *
       *  Creates a list of pager pages and returns it.
       *
       *  @param string[] $addStmtParams list of additional statement params
       *  @return PagerPage[] List of pages.
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 05.08.2006<br />
       *  Version 0.2, 06.08.2006<br />
       *  Version 0.3, 14.08.2006 (Added a global configuration for url rewriting)<br />
       *  Version 0.4, 16.11.2007 (Switched to the FrontcontrollerLinkHandler)<br />
       *  Version 0.5, 26.04.2008 (Avoid division by zero)<br />
       *  Version 0.6, 19.01.2009 (Changed the implementation due to refactoring)<br />
       */
      private function createPages4PagerDisplay($addStmtParams = array()) {

         $t = &Singleton::getInstance('BenchmarkTimer');
         $t->start('PagerManager::createPages4PagerDisplay()');

         // initialize start params
         $start = (int) 1;

         $countPerPage = $this->getCountPerPage();
         $currentStart = (int) RequestHandler::getValue($this->section->getValue('Pager.ParameterPageName'), 1) * $countPerPage;

         // initialize page delimiter params
         $M = &$this->__getAndInitServiceObject('modules::pager::data', 'PagerMapper', $this->section->getValue('Pager.DatabaseConnection'));
         $entriesCount = $M->getEntriesCount($this->section->getValue('Pager.StatementNamespace'), $this->section->getValue('Pager.CountStatement'), $this->getStatementParams($addStmtParams), $this->section->getValue('Pager.CacheInSession'));

         $pageCount = ceil($entriesCount / $countPerPage);

         // create the page representation objects
         $pages = array();
         for ($i = 0; $i < $pageCount; $i++) {

            // create a new pager page object
            $pages[$i] = new PagerPage();

            // generate the link
            $link = FrontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'], array($this->section->getValue('Pager.ParameterPageName') => $start));
            $pages[$i]->setLink($link);

            // set the number of the page
            $pages[$i]->setPage($i + 1);

            // mark as selected
            if ($start === $currentStart / $countPerPage) {
               $pages[$i]->setSelected(true);
            }

            // add the entries count
            $pages[$i]->setEntriesCount($entriesCount);

            // add the page count
            $pages[$i]->setPageCount($pageCount);

            // increment the start point
            $start++;

          // end for
         }

         $t->stop('PagerManager::createPages4PagerDisplay()');
         return $pages;

       // end function
      }

      /**
       *  @private
       *
       *  Returns a param array, that contains the initialized params from the page configuration
       *  file. The initialization is done by the url params. Default values are taken from the
       *  configuration offset *.Params. If no value is contained in the URL, the default ones are
       *  taken.
       *
       *  @param string $configString the param-value-string from the configuration (e.g.: param1:value1|param2:value2)
       *  @return string[] A list of statement parameters
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 16.08.2006<br />
       *  Version 0.2, 24.01.2009 (Refactoring due to configuration param changes)<br />
       */
      private function generateStatementParams($configString) {

         // initialize the return array
         $stmtParams = array();

         // create the params
         if (!empty($configString)) {

            $params = explode('|', $configString);

            for ($i = 0; $i < count($params); $i++) {

               // only accept params, that have a default value configured (to avoid errors!)
               if (substr_count($params[$i], ':') !== 0) {

                  // add the param with the default value of the url value
                  $temp = explode(':', $params[$i]);
                  $stmtParams = array_merge($stmtParams, RequestHandler::getValues(array(trim($temp[0]) => trim($temp[1]))));
                  unset($temp);

                // end if
               }

             // end for
            }

          // end if
         }

         return $stmtParams;

       // end function
      }

      /**
       * @public
       *
       * Returns the count of pages.
       *
       * @param string[] $addStmtParams list of additional statement params
       * @return int The count of pages.
       *
       * @author Daniel Seemaier
       * @version
       * Version 0.1, 22.08.2010
       */
      public function getPageCount($addStmtParams = array()) {
         $countPerPage = $this->getCountPerPage();

         $currentStart = (int) RequestHandler::getValue($this->section->getValue('Pager.ParameterPageName'), 1) * $countPerPage;

         // initialize page delimiter params
         $M = &$this->__getAndInitServiceObject('modules::pager::data', 'PagerMapper', $this->section->getValue('Pager.DatabaseConnection'));
         $entriesCount = $M->getEntriesCount($this->section->getValue('Pager.StatementNamespace'), $this->section->getValue('Pager.CountStatement'), $this->getStatementParams($addStmtParams), $this->section->getValue('Pager.CacheInSession'));

         return ceil($entriesCount / $countPerPage);
      }

      /**
       * @public
       *
       * Returns the count of entries per page.
       *
       * @return int The count of entries per page.
       *
       * @author Daniel Seemaier
       * @version
       * Version 0.1, 22.08.2010
       */
      public function getCountPerPage() {
         if ($this->isDynamicPageSizeActivated()) {
            $countPerPage = (int) RequestHandler::getValue($this->section->getValue('Pager.ParameterCountName'), 0);
         }

         if (!$this->isDynamicPageSizeActivated() || $countPerPage === 0) { // avoid devision by zero!
            $countPerPage = (int) $this->section->getValue('Pager.EntriesPerPage');
         }

         return $countPerPage;
      }

      /**
       * @public
       *
       * Generates the link to a given page.
       *
       * @param int $page The number of the page.
       * @param string $baseURI The base URI. Default = REQUEST_URI
       * @return string The link to the given page.
       *
       * @author Daniel Seemaier
       * @version
       * Version 0.1, 22.08.2010
       */
      public function getPageLink($page, $baseURI = null) {
         $linkParams = array($this->section->getValue('Pager.ParameterPageName') => $page);
         if ($this->isDynamicPageSizeActivated()) {
            $linkParams[$this->section->getValue('Pager.ParameterCountName')] = $this->getCountPerPage();
         }

         if ($baseURI == null) {
            $baseURI = $_SERVER['REQUEST_URI'];
         }

         return FrontcontrollerLinkHandler::generateLink($baseURI, $linkParams);
      }

      /**
       * @public
       *
       * Evaluates, whether the config attribute <em>Pager.AllowDynamicEntriesPerPage</em>
       * is present and set to <em>true</em> to activate the dynamic page size feature.
       *
       * @return boolean True in case, the config allows dynamic page size, false otherwise.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 22.09.2010<br />
       */
      public function isDynamicPageSizeActivated() {
         if ($this->section->getValue('Pager.AllowDynamicEntriesPerPage') !== null && $this->section->getValue('Pager.AllowDynamicEntriesPerPage') === 'true') {
            return true;
         } else {
            return false;
         }
      }

    // end class
   }
?>
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
namespace APF\modules\pager\biz;

use APF\core\benchmark\BenchmarkTimer;
use APF\core\http\mixins\GetRequestResponse;
use APF\core\pagecontroller\APFObject;
use APF\core\pagecontroller\Page;
use APF\core\singleton\Singleton;
use APF\modules\pager\data\PagerMapper;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;
use InvalidArgumentException;

/**
 * Represents a concrete pager.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 06.08.2006<br />
 * Version 0.2, 14.08.2006 (Added new class variables)<br />
 * Version 0.3, 16.08.2006 (Added configuration for the count and entries statements)<br />
 * Version 0.4, 13.04.2007 (Added the possibility to add params from the application)<br />
 * Version 0.5, 25.01.2009 (Refactoring of the API, refactoring of the functionality)<br />
 * Version 0.6, 16.11.2013 (Introduced option to create pager by DIServiceManager)<br />
 */
final class PagerManager extends APFObject {

   use GetRequestResponse;

   /**
    * Contains the desired anchor name.
    *
    * @var string $anchorName
    */
   private $anchorName = null;

   /**
    * The number of entries to display per page
    *
    * @var int $entriesPerPage
    */
   private $entriesPerPage = 10;

   /**
    * The name of the url parameter indicating which page to load.
    *
    * @var string $pageUrlParameterName
    */
   private $pageUrlParameterName = 'page';

   /**
    * The name of the url parameter indicating how much entries to load.
    *
    * @var string $countUrlParameterName
    */
   private $countUrlParameterName = 'count';

   /**
    * The namespace where the pager searches for count and entries selection statements to execute using a DatabaseConnection.
    *
    * @var string $statementNamespace
    */
   private $statementNamespace;

   /**
    * The name of the file containing the SQL statement to retrieve the total entries count.
    *
    * @var string $countStatementFile
    */
   private $countStatementFile;

   /**
    * The name of the file containing the SQL statement to retrieve the entries to display.
    *
    * @var string $entriesStatementFile
    */
   private $entriesStatementFile;

   /**
    * Static SQL statement parameters to be used to retrieve the total entries count.
    *
    * @var string $statementParameters
    */
   private $statementParameters;

   /**
    * Namespace of the pager UI component.
    *
    * @var string $pagerUiNamespace
    */
   private $pagerUiNamespace;

   /**
    * Template name of the pager UI component.
    *
    * @var string $pagerUiTemplate
    */
   private $pagerUiTemplate;

   /**
    * The name of the database connection to use.
    *
    * @var string $databaseConnectionName
    */
   private $databaseConnectionName;

   /**
    * Activates (true) or deactivates (false) dynamic amount of entries per page to be managed via URL (potentially less secure!).
    *
    * @var string $allowDynamicEntriesPerPage
    */
   private $allowDynamicEntriesPerPage = 'false';

   /**
    * Indicates whether pager statement results should be cached within session (true) or not (false).
    *
    * @var string $cacheInSession
    */
   private $cacheInSession = 'false';

   public function setAnchorName($anchorName) {
      $this->anchorName = $anchorName;
   }

   public function setAllowDynamicEntriesPerPage($allowDynamicEntriesPerPage) {
      $this->allowDynamicEntriesPerPage = $allowDynamicEntriesPerPage;
   }

   public function setCountStatementFile($countStatementFile) {
      $this->countStatementFile = $countStatementFile;
   }

   public function setCountUrlParameterName($countUrlParameterName) {
      $this->countUrlParameterName = $countUrlParameterName;
   }

   public function setDatabaseConnectionName($databaseConnectionName) {
      $this->databaseConnectionName = $databaseConnectionName;
   }

   public function setEntriesPerPage($entriesPerPage) {
      $this->entriesPerPage = $entriesPerPage;
   }

   public function setEntriesStatementFile($entriesStatementFile) {
      $this->entriesStatementFile = $entriesStatementFile;
   }

   public function setPageUrlParameterName($pageUrlParameterName) {
      $this->pageUrlParameterName = $pageUrlParameterName;
   }

   public function setPagerUiNamespace($pagerUiNamespace) {
      $this->pagerUiNamespace = $pagerUiNamespace;
   }

   public function setPagerUiTemplate($pagerUiTemplate) {
      $this->pagerUiTemplate = $pagerUiTemplate;
   }

   public function setStatementNamespace($statementNamespace) {
      $this->statementNamespace = $statementNamespace;
   }

   public function setStatementParameters($statementParameters) {
      $this->statementParameters = $statementParameters;
   }

   public function setCacheInSession($cacheInSession) {
      $this->cacheInSession = $cacheInSession;
   }

   /**
    * Initializes the pager. Loads the desired config section.
    *
    * @deprecated Use DI container initialization instead!
    *
    * @param string $initParam the name of the config section.
    *
    * @throws InvalidArgumentException In case the referred configuration section is missing.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.08.2006<br />
    * Version 0.2, 16.08.2006 (Added more params to be applied to the count statement)<br />
    * Version 0.3, 29.03.2007 (Renamed to "init()" to support the ServiceManager)<br />
    * Version 0.4, 30.03.2007 (Removed the old iniHandler component)<br />
    * Version 0.5, 13.04.2007 (Enhanced the method, that the pager can be created using the fabric)<br />
    * Version 0.6, 26.04.2008 (The statement params are now casted to int by default)<br />
    * Version 0.7, 25.01.2009 (Complete redesign / refactoring due to pager design changes. Now the pager can be used together with the GenericORMapper)<br />
    * Version 0.8, 16.11.2013 (Introduced mapping to internal fields to support both creation via factory as well as the DI container)<br />
    */
   public function init($initParam) {

      // initialize the config
      $config = $this->getConfiguration('APF\modules\pager', 'pager.ini');

      // empty sections are not allowed since they produced NPE's
      if (!$config->hasSection($initParam)) {
         throw new InvalidArgumentException('[PagerManager::init()] The given configuration section '
               . '"' . $initParam . '" cannot be found within the pager configuration file. Please '
               . 'review your setup!');
      }

      $section = $config->getSection($initParam);

      // translate entries per page
      $this->entriesPerPage = (int) $section->getValue('EntriesPerPage', '10');

      // translate url parameters
      $this->pageUrlParameterName = $section->getValue('ParameterPageName', 'page');
      $this->countUrlParameterName = $section->getValue('ParameterCountName', 'count');

      // translate statement related parameters
      $this->statementNamespace = $section->getValue('StatementNamespace');
      $this->countStatementFile = $section->getValue('CountStatement');
      $this->entriesStatementFile = $section->getValue('EntriesStatement');
      $this->statementParameters = $section->getValue('StatementParameters');

      // UI component configuration
      $this->pagerUiNamespace = $section->getValue('DesignNamespace');
      $this->pagerUiTemplate = $section->getValue('DesignTemplate');

      // translate database connection
      $this->databaseConnectionName = $section->getValue('DatabaseConnection');

      // translate dynamic page size
      $this->allowDynamicEntriesPerPage = $section->getValue('AllowDynamicEntriesPerPage', 'false');

      // translate caching settings
      $this->cacheInSession = $section->getValue('CacheInSession', 'false');
   }

   /**
    * Returns the statement params needed by the pager's data layer.
    *
    * @param string[] $addStmtParams Additional statement parameters.
    *
    * @return string[] A list of default statement params.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 24.01.2009<br />
    */
   private function getStatementParams(array $addStmtParams = array()) {
      if ($this->isDynamicPageSizeActivated()) {
         $entriesCount = (int) self::getRequest()->getParameter($this->countUrlParameterName, $this->entriesPerPage);
      } else {
         $entriesCount = $this->entriesPerPage;
      }

      // determine offset by page with respect to the first page being a special case in calculation
      $page = self::getRequest()->getParameter($this->pageUrlParameterName, 1);
      $start = 0;
      if ($page > 1) {
         $start = ($page * $entriesCount) - $entriesCount;
      }

      $defaultParams = array(
            'Start'        => $start,
            'EntriesCount' => $entriesCount
      );

      return array_merge($defaultParams, $this->generateStatementParams($this->statementParameters), $addStmtParams);
   }

   /**
    * Loads the ids of the entries of the current page.
    *
    * @param string[] $addStmtParams additional statement parameters.
    *
    * @return int[] List of entry ids for the current page.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.08.2006<br />
    * Version 0.2, 06.08.2006<br />
    * Version 0.3, 16.08.2006 (Added the enhanced param configuration opportunity)<br />
    * Version 0.4, 24.01.2009 (Changed the API of the method. Moved the additional param handling to this method)<br />
    */
   public function loadEntries($addStmtParams = array()) {
      $m = &$this->getMapper();

      return $m->loadEntries(
            $this->statementNamespace,
            $this->entriesStatementFile,
            $this->getStatementParams($addStmtParams),
            $this->cacheInSession === 'true'
      );
   }

   /**
    * @return PagerMapper
    */
   protected function &getMapper() {
      return $this->getAndInitServiceObject('APF\modules\pager\data\PagerMapper', $this->databaseConnectionName);
   }

   /**
    * Loads a list of domain objects using a given data layer component.
    *
    * @param APFObject $dataComponent instance of a data component, that loads the domain objects directly
    * @param string $loadMethod name of the load method for the domain object
    * @param string[] $addStmtParams additional statement parameters
    *
    * @return APFObject[] List of domain objects for the current page.
    * @throws InvalidArgumentException In case the data component does not have the desired get method.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 01.09.2007<br />
    * Version 0.2, 18.09.2007 (Introduced PHP 4 compatibility)<br />
    * Version 0.3, 24.01.2009 (Added the $addStmtParams param to the API)<br />
    * Version 0.4, 25.01.2009 (Refactored the function. Now uses the $this->loadEntries() to load the ids)<br />
    * Version 0.5, 27.12.2010 (Bug-fix: In case of empty results, no empty objects are returned any more.)<br />
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
    * Creates the graphical output of the pagerc concerning the configured presentation layer template.
    *
    * @param array $addStmtParams list of additional statement params
    *
    * @return string The HTML representation of the pager
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.08.2006<br />
    * Version 0.2, 11.03.2007 (Ported to new document controller, removed MessageQueue)<br />
    * Version 0.3, 29.08.2007 (Anchor name is not set as the document's attribute)<br />
    * Version 0.4, 02.03.2008 (The page is now applied the context and language)<br />
    */
   public function getPager($addStmtParams = array()) {

      $pager = new Page();

      // apply context and language (form configuration purposes!)
      $pager->setLanguage($this->language);
      $pager->setContext($this->context);

      // load the configured design
      $pager->loadDesign($this->pagerUiNamespace, $this->pagerUiTemplate);

      // add the necessary config params and pages
      $document = &$pager->getRootDocument();
      $document->setAttribute('Pages', $this->createPages4PagerDisplay($addStmtParams));
      $document->setAttribute('PageUrlParameterName', $this->pageUrlParameterName);
      $document->setAttribute('CountUrlParameterName', $this->countUrlParameterName);
      $document->setAttribute('EntriesPerPage', $this->entriesPerPage);
      $document->setAttribute('DynamicPageSizeActivated', $this->isDynamicPageSizeActivated());

      // add the anchor if desired
      if ($this->anchorName !== null) {
         $document->setAttribute('AnchorName', $this->anchorName);
      }

      return $pager->transform();
   }

   /**
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
            'PageName'  => $this->pageUrlParameterName,
            'CountName' => $this->countUrlParameterName
      );
   }

   /**
    * Creates a list of pager pages and returns it.
    *
    * @param string[] $addStmtParams list of additional statement params
    *
    * @return Page[] List of pages.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.08.2006<br />
    * Version 0.2, 06.08.2006<br />
    * Version 0.3, 14.08.2006 (Added a global configuration for url rewriting)<br />
    * Version 0.4, 16.11.2007 (Switched to the FrontcontrollerLinkHandler)<br />
    * Version 0.5, 26.04.2008 (Avoid division by zero)<br />
    * Version 0.6, 19.01.2009 (Changed the implementation due to refactoring)<br />
    * Version 0.7, 10.04.2011 (Switched to LinkGenerator due to new link generation concept in 1.14)<br />
    */
   private function createPages4PagerDisplay($addStmtParams = array()) {

      /* @var $t BenchmarkTimer */
      $t = &Singleton::getInstance('APF\core\benchmark\BenchmarkTimer');
      $t->start('PagerManager::createPages4PagerDisplay()');

      // initialize start params
      $start = (int) 1;

      $countPerPage = $this->getCountPerPage();
      $currentStart = (int) self::getRequest()->getParameter($this->pageUrlParameterName, 1) * $countPerPage;

      // initialize page delimiter params
      $m = &$this->getMapper();
      $entriesCount = $m->getEntriesCount(
            $this->statementNamespace,
            $this->countStatementFile,
            $this->getStatementParams($addStmtParams),
            $this->cacheInSession === 'true'
      );

      $pageCount = ceil($entriesCount / $countPerPage);

      // create the page representation objects
      /* @var $pages PageItem[] */
      $pages = array();
      for ($i = 0; $i < $pageCount; $i++) {

         // create a new pager page object
         $pages[$i] = new PageItem();

         // generate the link
         $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(array($this->pageUrlParameterName => $start)));
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

         $start++;

      }

      $t->stop('PagerManager::createPages4PagerDisplay()');

      return $pages;

   }

   /**
    * Returns a param array, that contains the initialized params from the page configuration
    * file. The initialization is done by the url params. Default values are taken from the
    * configuration offset *.Params. If no value is contained in the URL, the default ones are
    * taken.
    *
    * @param string $configString the param-value-string from the configuration (e.g.: param1:value1|param2:value2)
    *
    * @return string[] A list of statement parameters
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.08.2006<br />
    * Version 0.2, 24.01.2009 (Refactoring due to configuration param changes)<br />
    */
   private function generateStatementParams($configString) {

      $stmtParams = array();

      if (!empty($configString)) {

         $params = explode('|', $configString);

         for ($i = 0; $i < count($params); $i++) {

            // only accept params, that have a default value configured (to avoid errors!)
            if (substr_count($params[$i], ':') !== 0) {

               // add the param with the default value of the url value
               $temp = explode(':', $params[$i]);
               $stmtParams = array_merge($stmtParams, array(trim($temp[0]) => self::getRequest()->getParameter(trim($temp[1]))));
               unset($temp);

            }

         }

      }

      return $stmtParams;
   }

   /**
    * Returns the count of pages.
    *
    * @param string[] $addStmtParams list of additional statement params
    *
    * @return int The count of pages.
    *
    * @author Daniel Seemaier
    * @version
    * Version 0.1, 22.08.2010
    */
   public function getPageCount($addStmtParams = array()) {
      $countPerPage = $this->getCountPerPage();

      // initialize page delimiter params
      $m = &$this->getMapper();
      $entriesCount = $m->getEntriesCount(
            $this->statementNamespace,
            $this->countStatementFile,
            $this->getStatementParams($addStmtParams),
            $this->cacheInSession === 'true'
      );

      return ceil($entriesCount / $countPerPage);
   }

   /**
    * Returns the count of entries per page.
    *
    * @return int The count of entries per page.
    *
    * @author Daniel Seemaier
    * @version
    * Version 0.1, 22.08.2010
    */
   public function getCountPerPage() {

      $countPerPage = 0;

      if ($this->isDynamicPageSizeActivated()) {
         $countPerPage = (int) self::getRequest()->getParameter($this->countUrlParameterName, 0);
      }

      if (!$this->isDynamicPageSizeActivated() || $countPerPage === 0) { // avoid division by zero!
         $countPerPage = $this->entriesPerPage;
      }

      return $countPerPage;
   }

   /**
    * Generates the link to a given page.
    *
    * @param int $page The number of the page.
    * @param string $baseURI The base URI. Default = REQUEST_URI
    *
    * @return string The link to the given page.
    *
    * @author Daniel Seemaier
    * @version
    * Version 0.1, 22.08.2010
    */
   public function getPageLink($page, $baseURI = null) {
      $linkParams = array($this->pageUrlParameterName => $page);
      if ($this->isDynamicPageSizeActivated()) {
         $linkParams[$this->countUrlParameterName] = $this->getCountPerPage();
      }

      if ($baseURI === null) {
         $baseURI = self::getRequest()->getRequestUri();
      }

      return LinkGenerator::generateUrl(Url::fromString($baseURI)->mergeQuery($linkParams));
   }

   /**
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
      return $this->allowDynamicEntriesPerPage === 'true';
   }

}

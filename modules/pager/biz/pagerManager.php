<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('tools::link','frontcontrollerLinkHandler');
   import('tools::request','RequestHandler');
   import('modules::pager::biz','PagerPage');
   import('modules::pager::data','PagerMapper');


   /**
   *  @namespace modules::pager::biz
   *  @class PagerManagerFabric
   *
   *  Implements the factory of the pager manager. Application sample:
   *  <pre>$pMF = &$this->__getServiceObject('modules::pager::biz','PagerManagerFabric');
   *  $pM = &$pMF->getPagerManager('{ConfigSection}',{AdditionalParamArray});</pre>
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 13.04.2007<br />
   */
   class PagerManagerFabric extends coreObject
   {

      /**
      *  @private
      *  Cache list if concrete pager manager instances.
      */
      var $__Pager = array();


      function PagerManagerFabric(){
      }


      /**
      *  @public
      *
      *  Gibt eine Referenz auf einen pagerManager zur�ck.<br />
      *
      *  @param string $configString; Konfigurations-String
      *  @return pagerManager $pagerManager; Referenz auf den gew�nschten pagerManager
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 13.04.2007<br />
      *  Version 0.2, 24.01.2009 (Moved the additional params to the loadEntries() method. Refactored the method.)<br />
      */
      function &getPagerManager($configString){

         // create cache key
         $pagerHash = md5($configString);

         if(!isset($this->__Pager[$pagerHash])){
             $this->__Pager[$pagerHash] = &$this->__getAndInitServiceObject('modules::pager::biz','PagerManager',$configString,'NORMAL');
          // end if
         }

         // return desired pager reference
         return $this->__Pager[$pagerHash];

       // end function
      }

    // end class
   }


   /**
   *  @namespace modules::pager::biz
   *  @class PagerManager
   *
   *  Represents a concrete pager.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 06.08.2006<br />
   *  Version 0.2, 14.08.2006 (Added new class variables)<br />
   *  Version 0.3, 16.08.2006 (Added configuration for the count and entries statements)<br />
   *  Version 0.4, 13.04.2007 (Added the possibility to add params from the application)<br />
   *  Version 0.5, 25.01.2009 (Refactoring of the API, refactoring of the functionality)<br />
   */
   class PagerManager extends coreObject
   {

      /**
      *  @private
      *  @since 0.5
      *  Contains the current configuration.
      */
      var $__PagerConfig = null;


      /**
      *  @private
      *  Contains the desired anchor name.
      */
      var $__AnchorName = null;


      function PagerManager(){
      }


      /**
      *  @public
      *
      *  Initializes the pager. Loads the desired config section.
      *
      *  @param string $configSection the name of the config section+
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
      function init($configSection){

         // initialize the config
         $Config = &$this->__getConfiguration('modules::pager','pager');
         $this->__PagerConfig = $Config->getSection($configSection);

         // translate the cache directive
         if(!isset($this->__PagerConfig['Pager.CacheInSession']) || $this->__PagerConfig['Pager.CacheInSession'] === 'false'){
            $this->__PagerConfig['Pager.CacheInSession'] = false;
          // end if
         }
         else{
            $this->__PagerConfig['Pager.CacheInSession'] = true;
          // end else
         }

       // end function
      }


      /**
      *  @private
      *
      *  Returns the statement params needed by the pager's data layer.
      *
      *  @param array $addStmtParams additional statement parameters
      *  @return array $stmtParams a list of default statement params.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.01.2009<br />
      */
      function __getStatementParams($addStmtParams = array()){
         $defaultParams = array(
                                'Start' => (int)RequestHandler::getValue($this->__PagerConfig['Pager.ParameterStartName'],0),
                                'EntriesCount' => (int)RequestHandler::getValue($this->__PagerConfig['Pager.ParameterCountName'],$this->__PagerConfig['Pager.EntriesPerPage'])
                               );
         return array_merge($defaultParams,$this->__generateStatementParams($this->__PagerConfig['Pager.EntriesStatement.Params']),$addStmtParams);
       // end function
      }


      /**
      *  @public
      *
      *  Loads the ids of the entries of the current page.
      *
      *  @param array $addStmtParams additional statement parameters
      *  @return array $entryIDs list of entry ids for the current page
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 05.08.2006<br />
      *  Version 0.2, 06.08.2006<br />
      *  Version 0.3, 16.08.2006 (Added the enhanced param configuration opportunity)<br />
      *  Version 0.4, 24.01.2009 (Changed the API of the method. Moved the additional param handling to this method)<br />
      */
      function loadEntries($addStmtParams = array()){
         $M = &$this->__getAndInitServiceObject('modules::pager::data','PagerMapper',$this->__PagerConfig['Pager.DatabaseConnection']);
         return $M->loadEntries($this->__PagerConfig['Pager.StatementNamespace'],$this->__PagerConfig['Pager.EntriesStatement'],$this->__getStatementParams($addStmtParams),$this->__PagerConfig['Pager.CacheInSession']);
       // end function
      }


      /**
      *  @public
      *
      *  Loads a list of domain objects using a given data layer component.
      *
      *  @param object $dataComponent instance of a data component, that loads the domain objects directly
      *  @param string $loadMethod name of the load method for the domain object
      *  @param array $addStmtParams additional statement parameters
      *  @return array $entries list of domain objects for the current page
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 01.09.2007<br />
      *  Version 0.2, 18.09.2007 (Introduced PHP 4 compatibility)<br />
      *  Version 0.3, 24.01.2009 (Added the $addStmtParams param to the API)<br />
      *  Version 0.4, 25.01.2009 (Refactored the function. Now uses the $this->loadEntries() to load the ids)<br />
      */
      function loadEntriesByAppDataComponent(&$dataComponent,$loadMethod,$addStmtParams = array()){

         // check, if the load method exists
         if(in_array(strtolower($loadMethod),get_class_methods($dataComponent))){

            // select the ids of the desired entries
            $entryIDs = $this->loadEntries($addStmtParams);

            // load the entries using the data layer component
            $entries = array();
            for($i = 0; $i < count($entryIDs); $i++){
               $entries[] = $dataComponent->{$loadMethod}($entryIDs[$i]);
             // end for
            }

            return $entries;

          // end if
         }
         else{
            trigger_error('[PagerManager->loadEntriesByAppDataComponent()] Given data component ('.get_class($dataComponent).') has no method "'.$loadMethod.'"! No entries can be loaded!',E_USER_WARNING);
            return array();
          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Sets the anchor name.
      *
      *  @param string $anchorName the name of the desired anchor
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 25.01.2009<br />
      */
      function setAnchorName($anchorName = null){
         $this->__AnchorName = $anchorName;
       // end function
      }


      /**
      *  @public
      *
      *  Returns the anchor name.
      *
      *  @return string $anchorName the name of the anchor
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 25.01.2009<br />
      */
      function getAnchorName(){
         return $this->__AnchorName;
       // end function
      }


      /**
      *  @public
      *
      *  Creates the graphical output of the pagerc concerning the configured presentation layer template.
      *
      *  @param array $addStmtParams list of additional statement params
      *  @return string $pagerOutput the HTML representation of the pager
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 05.08.2006<br />
      *  Version 0.2, 11.03.2007 (Ported to new document controller, removed MessageQueue)<br />
      *  Version 0.3, 29.08.2007 (Anchor name is not set as the document's attribute)<br />
      *  Version 0.4, 02.03.2008 (The page is now applied the context and language)<br />
      */
      function getPager($addStmtParams = array()){

         // create pager page
         $pager = new Page();

         // apply context and language (form configuration purposes!)
         $pager->set('Language',$this->__Language);
         $pager->set('Context',$this->__Context);

         // load the econfigured design
         $pager->loadDesign($this->__PagerConfig['Pager.DesignNamespace'],$this->__PagerConfig['Pager.DesignTemplate']);

         // add the necessary config params and pages
         $document = &$pager->getByReference('Document');
         $document->setAttribute('Pages',$this->__createPages4PagerDisplay($addStmtParams));
         $document->setAttribute('Config',array('ParameterStartName' => $this->__PagerConfig['Pager.ParameterStartName'],
                                                'ParameterCountName' => $this->__PagerConfig['Pager.ParameterCountName'],
                                                'EntriesPerPage' => $this->__PagerConfig['Pager.EntriesPerPage']
                                               )
                                );

         // add the anchor if desired
         if($this->__AnchorName !== null){
            $document->setAttribute('AnchorName',$this->__AnchorName);
          // end if
         }

         // transform pager GUI representation
         return $pager->transform();

       // end function
      }


      /**
      *  @public
      *
      *  Returns the name of the current URL params of the pager. The array featiures the following
      *  offsets:
      *  <ul>
      *    <li>StartName: the name of the start param</li>
      *    <li>CountName: the name of the count per page param</li>
      *  <ul>
      *
      *  @return array $URLParams; Array der URL-Parameter des Pagers
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 17.03.2007<br />
      */
      function getPagerURLParameters(){
         return array('StartName' => $this->__PagerConfig['Pager.ParameterStartName'],'CountName' => $this->__PagerConfig['Pager.ParameterCountName']);
       // end function
      }


      /**
      *  @private
      *
      *  Creates a list of pager pages and returns it.
      *
      *  @param array $addStmtParams list of additional statement params
      *  @return array $pages list of pages
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 05.08.2006<br />
      *  Version 0.2, 06.08.2006<br />
      *  Version 0.3, 14.08.2006 (Added a global configuration for url rewriting)<br />
      *  Version 0.4, 16.11.2007 (Switched to the frontcontrollerLinkHandler)<br />
      *  Version 0.5, 26.04.2008 (Avoid division by zero)<br />
      *  Version 0.6, 19.01.2009 (Changed the implementation due to refactoring)<br />
      */
      function __createPages4PagerDisplay($addStmtParams = array()){

         // start benchmarker
         $t = &Singleton::getInstance('benchmarkTimer');
         $t->start('PagerManager::__createPages4PagerDisplay()');

         // initialize start params
         $start = (int)0;
         $currentStart = (int)RequestHandler::getValue($this->__PagerConfig['Pager.ParameterStartName'],0);

         // initialize page delimiter params
         $M = &$this->__getAndInitServiceObject('modules::pager::data','PagerMapper',$this->__PagerConfig['Pager.DatabaseConnection']);
         $entriesCount = $M->getEntriesCount($this->__PagerConfig['Pager.StatementNamespace'],$this->__PagerConfig['Pager.CountStatement'],$this->__getStatementParams($addStmtParams),$this->__PagerConfig['Pager.CacheInSession']);

         $countPerPage = (int)RequestHandler::getValue($this->__PagerConfig['Pager.ParameterCountName'],0);
         if($countPerPage === 0){ // avoid devision by zero!
            $countPerPage = (int)$this->__PagerConfig['Pager.EntriesPerPage'];
          // end if
         }
         $pageCount = ceil($entriesCount / $countPerPage);

         // create the page representation objects
         $pages = array();
         for($i = 0; $i < $pageCount; $i++){

            // create a new pager page object
            $pages[$i] = new PagerPage();

            // generate the link
            $link = frontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'],array($this->__PagerConfig['Pager.ParameterStartName'] => $start));
            $pages[$i]->set('Link',$link);

            // set the number of the page
            $pages[$i]->set('Page',$i + 1);

            // mark as selected
            if($start === $currentStart){
               $pages[$i]->set('isSelected',true);
             // end if
            }

            // add the entries count
            $pages[$i]->set('entriesCount',$entriesCount);

            // add the page count
            $pages[$i]->set('pageCount',$pageCount);

            // increment the start point
            $start = $start + $countPerPage;

          // end for
         }

         // stop benchmarker and return the list of pager pages
         $t->stop('PagerManager::__createPages4PagerDisplay()');
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
      *  @return array $stmtParams a list of statement parameters
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 16.08.2006<br />
      *  Version 0.2, 24.01.2009 (Refactoring due to configuration param changes)<br />
      */
      function __generateStatementParams($configString){

         // initialize the return array
         $stmtParams = array();

         // create the params
         if(!empty($configString)){

            $params = explode('|',$configString);

            for($i = 0; $i < count($params); $i++){

               // only accept params, that have a default value configured (to avoid errors!)
               if(substr_count($params[$i],':') !== 0){

                  // add the param with the default value of the url value
                  $temp = explode(':',$params[$i]);
                  $stmtParams = array_merge($stmtParams,RequestHandler::getValues(array(trim($temp[0]) => trim($temp[1]))));
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

    // end class
   }
?>
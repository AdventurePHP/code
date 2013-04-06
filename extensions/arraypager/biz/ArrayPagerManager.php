<?php
namespace APF\extensions\arraypager\biz;

use APF\core\configuration\ConfigurationException;
use APF\core\pagecontroller\APFObject;
use APF\core\pagecontroller\Page;
use APF\extensions\arraypager\data\ArrayPagerMapper;
use APF\tools\request\RequestHandler;

/**
 * @package APF\extensions\arraypager\biz
 * @class ArrayPagerManager
 *
 * Represents a concrete pager.
 *
 * @author Lutz Mahlstedt
 * @version
 * Version 0.1, 21.12.2009<br />
 */
final class ArrayPagerManager extends APFObject {

   private $pagerConfig = NULL;
   private $anchorName = NULL;

   /**
    * @public
    *
    * Initializes the pager. Loads the desired config section.
    *
    * @param string $initParam the name of the config section.
    * @throws ConfigurationException In case the configuration section cannot be loaded.
    *
    * @author Lutz Mahlstedt
    * @version
    * Version 0.1, 21.12.2009<br />
    */
   public function init($initParam) {

      // initialize the config
      $namespace = 'APF\extensions\arraypager';
      $configName = 'arraypager.ini';
      $config = $this->getConfiguration($namespace, $configName);

      // remap configuration
      $configParams = array();
      $section = $config->getSection($initParam);
      if ($section === null) {
         throw new ConfigurationException('[ArrayPagerManager::init()] Section with name "' . $initParam
               . '" cannot be found in configuration "' . $configName . '" unter namespace "'
               . $namespace . '" and context "' . $this->getContext() . '". '
               . 'Please double check you configuration!');
      } else {
         foreach ($section->getValueNames() as $name) {
            $configParams[$name] = $section->getValue($name);
         }
      }

      $arrayParameter = array(
         'Pager.ParameterPage' => 'page',
         'Pager.ParameterEntries' => 'entries',
         'Pager.Entries' => 10,
         'Pager.EntriesPossible' => '5|10|15'
      );

      $this->pagerConfig = array_merge($arrayParameter, $configParams);

      if (isset($this->pagerConfig['Pager.EntriesChangeable']) === TRUE
            && $this->pagerConfig['Pager.EntriesChangeable'] == 'true'
      ) {
         $this->pagerConfig['Pager.EntriesChangeable'] = TRUE;
      } else {
         $this->pagerConfig['Pager.EntriesChangeable'] = FALSE;
      }

      $this->pagerConfig['Pager.Entries'] = intval($this->pagerConfig['Pager.Entries']);
   }

   /**
    * @protected
    *
    * Returns the mapper.
    *
    * @return ArrayPagerMapper The pager mapper.
    *
    * @author Lutz Mahlstedt
    * @version
    * Version 0.1, 21.12.2009<br />
    */
   protected function &getDataMapper() {
      return $this->getServiceObject('APF\extensions\arraypager\data\ArrayPagerMapper');
   }

   /**
    * @public
    *
    * @param string $stringPager name of the pager
    * @param integer $integerPage optional parameter for current page
    * @param integer $integerEntries optional parameter for entries per page
    * @return mixed[] List of entries for the current page
    * @throws \Exception In case no pager configuration can be found.
    *
    * @author Lutz Mahlstedt
    * @version
    * Version 0.1, 21.12.2009<br />
    */
   public function loadEntries($stringPager, $integerPage = NULL, $integerEntries = NULL) {

      $mapper = $this->getDataMapper();

      $arrayData = $mapper->loadEntries($stringPager);

      if (is_array($arrayData) === TRUE) {
         if ($integerPage === NULL) {
            $integerPage = intval(RequestHandler::getValue($this->pagerConfig['Pager.ParameterPage'],
                  1
               )
            );

            if ($integerPage <= 0) {
               $integerPage = 1;
            }
         }

         if ($integerEntries === NULL) {
            if ($this->pagerConfig['Pager.EntriesChangeable'] === TRUE) {
               $integerEntries = intval(RequestHandler::getValue($this->pagerConfig['Pager.ParameterEntries'],
                     $this->pagerConfig['Pager.Entries']
                  )
               );
            } else {
               $integerEntries = 0;
            }

            if ($integerEntries <= 0) {
               $integerEntries = $this->pagerConfig['Pager.Entries'];
            }
         }

         return array_slice($arrayData,
            (($integerPage - 1) * $integerEntries),
            $integerEntries,
            TRUE
         );
      } else {
         throw new \Exception('[ArrayPagerManager->loadEntries()] There is no pager named "'
               . $stringPager . '" registered!', E_USER_WARNING);
      }
   }

   /**
    * @public
    *
    * @param string $stringPager name of the pager
    * @return string The HTML representation of the pager
    * @throws \Exception In case no pager configuration can be found.
    *
    * @author Lutz Mahlstedt
    * @version
    * Version 0.1, 21.12.2009<br />
    */
   public function getPager($stringPager) {
      $stringOutput = '';

      $objectArrayPagerMapper = $this->getDataMapper();

      $arrayData = $objectArrayPagerMapper->loadEntries($stringPager);

      if (is_array($arrayData) === TRUE) {
         if (is_array($arrayData) === TRUE
               AND count($arrayData) > 0
         ) {
            // create pager page
            $pager = new Page();

            // apply context and language (form configuration purposes!)
            $pager->setLanguage($this->getLanguage());
            $pager->setContext($this->getContext());

            // load the configured design
            $pager->loadDesign($this->pagerConfig['Pager.DesignNamespace'],
               $this->pagerConfig['Pager.DesignTemplate']
            );

            // add the necessary config params and pages
            $rootDoc = $pager->getRootDocument();

            $rootDoc->setAttribute('Config',
               array('ParameterPage' => $this->pagerConfig['Pager.ParameterPage'],
                  'ParameterEntries' => $this->pagerConfig['Pager.ParameterEntries'],
                  'Entries' => intval(RequestHandler::getValue($this->pagerConfig['Pager.ParameterEntries'],
                     $this->pagerConfig['Pager.Entries']
                  )),
                  'EntriesPossible' => $this->pagerConfig['Pager.EntriesPossible'],
                  'EntriesChangeable' => $this->pagerConfig['Pager.EntriesChangeable']
               )
            );

            $rootDoc->setAttribute('DataCount',
               count($arrayData)
            );

            // add the anchor if desired
            if ($this->anchorName !== NULL) {
               $rootDoc->setAttribute('AnchorName',
                  $this->anchorName
               );
            }

            $stringOutput = $pager->transform();
         }
      } else {
         throw new \Exception('[ArrayPagerManager->getPager()] There is no pager named "'
               . $stringPager . '" registered!', E_USER_WARNING);
      }

      return $stringOutput;
   }

   /**
    * @public
    *
    *  Sets the anchor name.
    *
    * @param string $stringAnchorName The name of the desired anchor.
    *
    * @author Lutz Mahlstedt
    * @version
    * Version 0.1, 21.12.2009<br />
    */
   public function setAnchorName($stringAnchorName = NULL) {
      $this->anchorName = $stringAnchorName;
   }

   /**
    * @public
    *
    *  Returns the anchor name.
    *
    * @return string The name of the anchor
    *
    * @author Lutz Mahlstedt
    * @version
    * Version 0.1, 21.12.2009<br />
    */
   public function getAnchorName() {
      return $this->anchorName;
   }

   /**
    * @public
    *
    * @param string $stringPager name of the pager
    * @param array $arrayData the data-list
    * @throws \Exception In case the pager cannot be registered.
    *
    * @author Lutz Mahlstedt
    * @version
    * Version 0.1, 21.12.2009<br />
    * Version 0.2, 23.12.2009 Check whether $arrayData is an array or not<br />
    */
   public function registerPager($stringPager, &$arrayData
   ) {
      if (is_array($arrayData) === TRUE) {
         $objectArrayPagerMapper = $this->getDataMapper();

         $objectArrayPagerMapper->registerEntries($stringPager,
            $arrayData
         );
      } else {
         throw new \Exception('[ArrayPagerManager->registerPager()] Can not register pager named "'
               . $stringPager . '" because the given data is not an array!', E_USER_WARNING);
      }
   }

   /**
    * @public
    *
    * @param string $stringPager name of the pager
    *
    * @author Lutz Mahlstedt
    * @version
    * Version 0.1, 21.12.2009<br />
    */
   public function unregisterPager($stringPager) {
      $objectArrayPagerMapper = $this->getDataMapper();

      $objectArrayPagerMapper->unregisterEntries($stringPager);
   }

   /**
    * @public
    *
    * Returns whether pager exists or not.
    *
    * @param string $stringPager name of the pager
    * @return boolean True if pager exists, otherwise false
    *
    * @author Lutz Mahlstedt
    * @version
    * Version 0.1, 21.12.2009<br />
    */
   public function checkPager($stringPager) {
      $objectArrayPagerMapper = $this->getDataMapper();
      $booleanReturn = $objectArrayPagerMapper->checkPager($stringPager);
      return $booleanReturn;
   }

   /**
    * @public
    *
    * Returns whether page is defined.
    *
    * @return boolean True if page is defined, otherwise false
    *
    * @author Lutz Mahlstedt
    * @version
    * Version 0.1, 21.12.2009<br />
    */
   public function checkPage() {
      $booleanReturn = FALSE;

      $mixedData = RequestHandler::getValue($this->pagerConfig['Pager.ParameterPage'],
         FALSE
      );

      if ($mixedData !== FALSE) {
         $booleanReturn = TRUE;
      }

      return $booleanReturn;
   }

}

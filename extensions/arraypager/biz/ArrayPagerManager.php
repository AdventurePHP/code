<?php
   import ('extensions::arraypager::data',
           'ArrayPagerMapper'
   );
   import ('tools::request',
           'RequestHandler'
   );

   /**
    * @namespace extensions::arraypager::biz
    * @class ArrayPagerManager
    *
    * Represents a concrete pager.
    *
    * @author Lutz Mahlstedt
    * @version
    * Version 0.1, 21.12.2009<br />
    */
   final class ArrayPagerManager extends APFObject {
      private $__PagerConfig = NULL;

      private $__AnchorName = NULL;

      public function ArrayPagerManager () {
      }

      /**
       * @public
       *
       * Initializes the pager. Loads the desired config section.
       *
       * @param string $initParam the name of the config section.
       *
       * @author Lutz Mahlstedt
       * @version
       * Version 0.1, 21.12.2009<br />
       */
      public function init ($stringParameter) {
         // initialize the config
         $objectConfiguration = $this->__getConfiguration ('extensions::arraypager',
                 'arraypager'
         );

         $arrayParameter = array ('Pager.ParameterPage'    => 'page',
                 'Pager.ParameterEntries' => 'entries',
                 'Pager.Entries'          => 10,
                 'Pager.EntriesPossible'  => '5|10|15'
         );

         $this->__PagerConfig = array_merge ($arrayParameter,
                 $objectConfiguration->getSection ($stringParameter)
         );

         unset ($objectConfiguration);

         if (isset ($this->__PagerConfig['Pager.EntriesChangeable']) === TRUE
                 AND $this->__PagerConfig['Pager.EntriesChangeable']         ==  'true'
         ) {
            $this->__PagerConfig['Pager.EntriesChangeable'] = TRUE;
         }
         else {
            $this->__PagerConfig['Pager.EntriesChangeable'] = FALSE;
         }

         $this->__PagerConfig['Pager.Entries'] = intval ($this->__PagerConfig['Pager.Entries']);
      }

      /**
       *  @protected
       *
       *  Returns the mapper.
       *
       *  @return boolean True if pager exists, otherwise false
       *
       *  @author Lutz Mahlstedt
       *  @version
       *  Version 0.1, 21.12.2009<br />
       */
      protected function &__getDataMapper () {
         return $this->__getServiceObject ('extensions::arraypager::data',
                 'ArrayPagerMapper'
         );
      }

      /**
       * @public
       *
       * @param string $stringPager name of the pager
       * @param integer $integerPage optional parameter for current page
       * @param integer $integerEntries optional parameter for entries per page
       * @return mixed[] List of entrys for the current page
       *
       * @author Lutz Mahlstedt
       * @version
       * Version 0.1, 21.12.2009<br />
       */
      public function loadEntries ($stringPager,
              $integerPage    = NULL,
              $integerEntries = NULL
      ) {
         $objectArrayPagerMapper = $this->__getDataMapper ();

         $arrayData = $objectArrayPagerMapper->loadEntries ($stringPager);

         if (is_array ($arrayData) === TRUE) {
            if ($integerPage === NULL) {
               $integerPage = intval (RequestHandler::getValue ($this->__PagerConfig['Pager.ParameterPage'],
                       1
                       )
               );

               if ($integerPage <= 0) {
                  $integerPage = 1;
               }
            }

            if ($integerEntries === NULL) {
               if ($this->__PagerConfig['Pager.EntriesChangeable'] === TRUE) {
                  $integerEntries = intval (RequestHandler::getValue ($this->__PagerConfig['Pager.ParameterEntries'],
                          $this->__PagerConfig['Pager.Entries']
                          )
                  );
               }
               else {
                  $integerEntries = 0;
               }

               if ($integerEntries <= 0) {
                  $integerEntries = $this->__PagerConfig['Pager.Entries'];
               }
            }

            return array_slice ($arrayData,
                    (($integerPage - 1) * $integerEntries),
                    $integerEntries,
                    TRUE
            );
         }
         else {
            trigger_error ('[ArrayPagerManager->loadEntries()] There is no pager named "'.$stringPager.'" registered!',
                    E_USER_WARNING
            );
         }
      }

      /**
       * @public
       *
       * @param string $stringPager name of the pager
       * @return string The HTML representation of the pager
       *
       * @author Lutz Mahlstedt
       * @version
       * Version 0.1, 21.12.2009<br />
       */
      public function getPager ($stringPager) {
         $stringOutput = '';

         $objectArrayPagerMapper = $this->__getDataMapper ();

         $arrayData = $objectArrayPagerMapper->loadEntries ($stringPager);

         if (is_array ($arrayData) === TRUE) {
            if (is_array ($arrayData) === TRUE
                    AND count ($arrayData)    >   0
            ) {
               // create pager page
               $objectPager = new Page();

               // apply context and language (form configuration purposes!)
               $objectPager->set ('Language',
                       $this->__Language
               );
               $objectPager->set ('Context',
                       $this->__Context
               );

               // load the configured design
               $objectPager->loadDesign ($this->__PagerConfig['Pager.DesignNamespace'],
                       $this->__PagerConfig['Pager.DesignTemplate']
               );

               // add the necessary config params and pages
               $objectDocument = $objectPager->getByReference ('Document');

               $objectDocument->setAttribute ('Config',
                       array ('ParameterPage'     => $this->__PagerConfig['Pager.ParameterPage'],
                       'ParameterEntries'  => $this->__PagerConfig['Pager.ParameterEntries'],
                       'Entries'           => intval (RequestHandler::getValue ($this->__PagerConfig['Pager.ParameterEntries'],
                       $this->__PagerConfig['Pager.Entries']
                       )),
                       'EntriesPossible'   => $this->__PagerConfig['Pager.EntriesPossible'],
                       'EntriesChangeable' => $this->__PagerConfig['Pager.EntriesChangeable']
                       )
               );

               $objectDocument->setAttribute ('DataCount',
                       count ($arrayData)
               );

               // add the anchor if desired
               if ($this->__AnchorName !== NULL) {
                  $objectDocument->setAttribute ('AnchorName',
                          $this->__AnchorName
                  );
               }

               $stringOutput = $objectPager->transform ();
            }
         }
         else {
            trigger_error ('[ArrayPagerManager->getPager()] There is no pager named "'.$stringPager.'" registered!',
                    E_USER_WARNING
            );
         }

         return $stringOutput;
      }

      /**
       *  @public
       *
       *  Sets the anchor name.
       *
       *  @param string $stringAnchorName The name of the desired anchor.
       *
       *  @author Lutz Mahlstedt
       *  @version
       *  Version 0.1, 21.12.2009<br />
       */
      public function setAnchorName ($stringAnchorName = NULL) {
         $this->__AnchorName = $stringAnchorName;
      }

      /**
       *  @public
       *
       *  Returns the anchor name.
       *
       *  @return string The name of the anchor
       *
       *  @author Lutz Mahlstedt
       *  @version
       *  Version 0.1, 21.12.2009<br />
       */
      public function getAnchorName () {
         return $this->__AnchorName;
      }

      /**
       * @public
       *
       * @param string $stringPager name of the pager
       * @param array $arrayData the data-list
       * @return string[] Url params of the pager.
       *
       * @author Lutz Mahlstedt
       * @version
       * Version 0.1, 21.12.2009<br />
   * Version 0.2, 23.12.2009 Check whether $arrayData is an array or not<br />
       */
      public function registerPager ($stringPager,
              &$arrayData
      ) {
         if (is_array ($arrayData) === TRUE) {
            $objectArrayPagerMapper = $this->__getDataMapper ();

            $objectArrayPagerMapper->registerEntries ($stringPager,
                    $arrayData
            );
         }
         else {
            trigger_error ('[ArrayPagerManager->registerPager()] Can not register pager named "'.$stringPager.'" because the given data is not an array!',
                    E_USER_WARNING
            );
         }
      }

      /**
       * @public
       *
       * @param string $stringPager name of the pager
       * @param array $arrayData the data-list
       * @return string[] Url params of the pager.
       *
       * @author Lutz Mahlstedt
       * @version
       * Version 0.1, 21.12.2009<br />
       */
      public function unregisterPager ($stringPager) {
         $objectArrayPagerMapper = $this->__getDataMapper ();

         $objectArrayPagerMapper->unregisterEntries ($stringPager);
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
      public function checkPager ($stringPager) {
         $booleanReturn = FALSE;

         $objectArrayPagerMapper = $this->__getDataMapper ();

         $booleanReturn = $objectArrayPagerMapper->checkPager ($stringPager);

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
      public function checkPage () {
         $booleanReturn = FALSE;

         $mixedData = RequestHandler::getValue ($this->__PagerConfig['Pager.ParameterPage'],
                 FALSE
         );

         if ($mixedData !== FALSE) {
            $booleanReturn = TRUE;
         }

         return $booleanReturn;
      }
   }
?>
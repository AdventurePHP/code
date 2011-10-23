<?php
   import ('core::session',
           'SessionManager'
   );

   /**
    *  @namespace extensions::arraypager::data
    *  @class ArrayPagerMapper
    *
    *  Represents the data layer of the array-pager.
    *
    *  @author Lutz Mahlstedt
    *  @version
    *  Version 0.1, 21.12.2009<br />
    */
   final class ArrayPagerMapper extends APFObject {
      public function ArrayPagerMapper () {
      }

      /**
       *  @protected
       *
       *  Returns the session key.
       *
       *  @param string $stringPager name of the pager
       *  @return string $stringSessionKey the desired session key
       *
       *  @author Lutz Mahlstedt
       *  @version
       *  Version 0.1, 21.12.2009<br />
       */
      protected function getSessionKey ($stringPager) {
         return 'ArrayPagerMapper_'.md5 ($stringPager);
      }

      /**
       *  @public
       *
       *  Returns the number of entries of the desired object.
       *
       *  @param string $stringPager name of the pager
       *  @return integer $integerEntriesCount the number of entries
       *
       *  @author Lutz Mahlstedt
       *  @version
       *  Version 0.1, 21.12.2009<br />
       */
      public function getEntriesCount ($stringPager) {
         $integerEntriesCount = count ($this->loadEntries ($stringPager));

         return $integerEntriesCount;
      }

      /**
       *  @public
       *
       *  Returns a list of the objects, that should be loaded for the current page.
       *
       *  @param string $stringPager name of the pager
       *  @return array $arrayEntries a list of entries
       *
       *  @author Lutz Mahlstedt
       *  @version
       *  Version 0.1, 21.12.2009<br />
       */
      public function loadEntries ($stringPager) {
         $objectSession = new SessionManager ('extensions::arraypager::biz');
         $stringSessionKey = $this->getSessionKey ($stringPager);
         $arrayEntries = $objectSession->loadSessionData ($stringSessionKey);

         return $arrayEntries;
      }

      /**
       *  @public
       *
       *  @param string $stringPager name of the pager
       *  @return array $arrayEntries a list of entries
       *
       *  @author Lutz Mahlstedt
       *  @version
       *  Version 0.1, 21.12.2009<br />
       */
      public function registerEntries ($stringPager,
              $arrayData
      ) {
         $objectSession = new SessionManager ('extensions::arraypager::biz');
         $stringSessionKey = $this->getSessionKey ($stringPager);
         $objectSession->saveSessionData ($stringSessionKey,
                 $arrayData
         );
      }

      /**
       *  @public
       *
       *  @param string $stringPager name of the pager
       *
       *  @author Lutz Mahlstedt
       *  @version
       *  Version 0.1, 21.12.2009<br />
       */
      public function unregisterEntries ($stringPager) {
         $objectSession = new SessionManager ('extensions::arraypager::biz');
         $stringSessionKey = $this->getSessionKey ($stringPager);
         $objectSession->deleteSessionData ($stringSessionKey);
      }

      /**
       *  @public
       *
       *  @param string $stringPager name of the pager
       *  @return boolean $mixedData whether pager exists or not
       *
       *  @author Lutz Mahlstedt
       *  @version
       *  Version 0.1, 21.12.2009<br />
       */
      public function checkPager ($stringPager) {
         $objectSession = new SessionManager ('extensions::arraypager::biz');
         $stringSessionKey = $this->getSessionKey ($stringPager);
         $mixedData = $objectSession->loadSessionData ($stringSessionKey);

         $booleanExists = FALSE;

         if ($mixedData !== NULL) {
            $booleanExists = TRUE;
         }

         return $booleanExists;
      }
   }
?>
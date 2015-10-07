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
namespace APF\modules\guestbook2009\biz;

use APF\core\benchmark\BenchmarkTimer;
use APF\core\http\mixins\GetRequestResponse;
use APF\core\pagecontroller\APFObject;
use APF\core\singleton\Singleton;
use APF\modules\guestbook2009\data\GuestbookMapper;
use APF\modules\pager\biz\PagerManager;
use APF\modules\pager\biz\PagerManagerFabric;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;

/**
 * Implements the central business component of the guestbook. Must be initialized with
 * the DIServiceManager to get the necessary information injected (pager config).
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 21.05.2009<br />
 */
final class GuestbookService extends APFObject {

   use GetRequestResponse;

   /**
    * Stores the pager instance for further usage.
    *
    * @var PagerManager $pager
    */
   private $pager = null;

   /**
    * Defines the name of the pager config section.
    *
    * @var string $pagerConfigSection
    */
   private $pagerConfigSection;

   /**
    * Implements an initializer method for the DIServiceManager to inject the
    * name of the pager's config section.
    *
    * @param string $pagerConfigSection The name of the config section.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.06.2009<br />
    */
   public function setPagerConfigSection($pagerConfigSection) {
      $this->pagerConfigSection = $pagerConfigSection;
   }

   /**
    * Loads a paged entry list.
    *
    * @return Entry[] The paged entry list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.05.2009<br />
    */
   public function loadPagedEntryList() {

      /* @var $t BenchmarkTimer */
      $t = &Singleton::getInstance('APF\core\benchmark\BenchmarkTimer');
      $t->start('loadPagedEntryList');

      $pager = &$this->getPager();

      /* @var $model GuestbookModel */
      $model = &$this->getServiceObject('APF\modules\guestbook2009\biz\GuestbookModel');
      $entryIds = $pager->loadEntries(array('GuestbookID' => $model->getGuestbookId()));

      $entries = array();
      $mapper = &$this->getMapper();
      foreach ($entryIds as $entryId) {
         $entries[] = $mapper->loadEntry($entryId);
      }

      $t->stop('loadPagedEntryList');

      return $entries;
   }

   /**
    * Returns the string representation of the current pager status.
    *
    * @return string The pager output.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.06.2009<br />
    */
   public function getPagerOutput() {
      /* @var $model GuestbookModel */
      $model = &$this->getServiceObject('APF\modules\guestbook2009\biz\GuestbookModel');
      $pager = &$this->getPager();

      return $pager->getPager(array('GuestbookID' => $model->getGuestbookId()));
   }

   /**
    * Returns the pager's url params.
    *
    * @return string[] The pager's url params.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.06.2009<br />
    */
   public function getPagerURLParams() {
      $pager = &$this->getPager();

      return $pager->getPagerURLParameters();
   }

   /**
    * Returns the pager instance for the current guestbook instance.
    * Internally abstracts the access of the pager instance to enable
    * lazy initialization.
    *
    * @return PagerManager The pager instance.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.06.2009<br />
    */
   private function &getPager() {

      if ($this->pager === null) {
         /* @var $pMF PagerManagerFabric */
         $pMF = &$this->getServiceObject('APF\modules\pager\biz\PagerManagerFabric');
         $this->pager = &$pMF->getPagerManager($this->pagerConfigSection);
      }

      return $this->pager;
   }

   /**
    * Loads a complete entry list for selection (backend!).
    *
    * @return Entry[] The entry list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.05.2009<br />
    */
   public function loadEntryListForSelection() {
      $mapper = &$this->getMapper();

      return $mapper->loadEntryListForSelection();
   }

   /**
    * Loads a dedicated entry.
    *
    * @param int $id The id of the desired entry.
    *
    * @return Entry An entry.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.05.2009<br />
    */
   public function loadEntry($id) {
      $mapper = &$this->getMapper();

      return $mapper->loadEntry($id);
   }

   /**
    * Loads the guestbook.
    *
    * @return Guestbook The current guestbook domain object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.05.2009<br />
    */
   public function loadGuestbook() {
      $mapper = &$this->getMapper();

      return $mapper->loadGuestbook();
   }

   /**
    * Deletes the given entry.
    *
    * @param Entry $entry The entry domain object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.05.2009<br />
    */
   public function deleteEntry(Entry $entry) {

      if ($entry !== null) {
         $mapper = &$this->getMapper();
         $mapper->deleteEntry($entry);
      }

      // display the admin start page
      $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(array(
            'gbview'    => 'admin',
            'adminview' => null
      )));
      $this->getResponse()->forward($link);
   }

   /**
    * Logs the user out and displays the list view.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.05.2009<br />
    */
   public function logout() {

      // logout by cleaning the session
      /* @var $model GuestbookModel */
      $model = &$this->getServiceObject('APF\modules\guestbook2009\biz\GuestbookModel');
      $session = $this->getSession($model->getGuestbookId());
      $session->delete('LoggedIn');

      // display the list view
      $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(array(
            'gbview'    => 'list',
            'adminview' => null
      )));
      $this->getResponse()->forward($link);
   }

   protected function getSession($guestBookId) {
      return $this->getRequest()->getSession(__NAMESPACE__ . '\\' . $guestBookId);
   }

   /**
    * Checks, whether the current a user is logged in and the admin backend
    * may be displayed. If no, the user is redirected to the list view.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.05.2009<br />
    */
   public function checkAccessAllowed() {

      /* @var $model GuestbookModel */
      $model = &$this->getServiceObject('APF\modules\guestbook2009\biz\GuestbookModel');
      $session = $this->getSession($model->getGuestbookId());
      $loggedId = $session->load('LoggedIn');

      // redirect to admin page
      if ($loggedId !== 'true') {
         $startLink = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(array(
               'gbview'    => 'list',
               'adminview' => null
         )));
         $this->getResponse()->forward($startLink);
      }
   }

   /**
    * Implements the login helper method called by the document controller. Returns false, in
    * case of login errors or logs the user in and redirects to the admin page.
    *
    * @param User $user The user object containing the username and password typed by the user.
    *
    * @return boolean False in case, the credential check failed, true otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.05.2009<br />
    */
   public function validateCredentials(User $user) {

      $mapper = &$this->getMapper();
      if ($mapper->validateCredentials($user)) {

         // log user in
         /* @var $model GuestbookModel */
         $model = &$this->getServiceObject('APF\modules\guestbook2009\biz\GuestbookModel');
         $session = $this->getSession($model->getGuestbookId());
         $session->save('LoggedIn', 'true');

         // redirect to admin page
         $adminLink = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(array(
               'gbview'    => 'admin',
               'adminview' => null
         )));
         $this->getResponse()->forward($adminLink);
      }

      return false;
   }

   /**
    * Saves the entry and forwards to the list view.
    *
    * @param Entry $entry The guestbook entry to save.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.05.2009<br />
    */
   public function saveEntry(Entry $entry) {

      $mapper = &$this->getMapper();
      $mapper->saveEntry($entry);

      // Forward to the desired view to prevent F5-bugs.
      $entryId = $entry->getId();
      if (!empty($entryId)) {
         $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(array(
               'gbview' => 'admin'
         )));
      } else {
         $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(array(
               'gbview' => 'list'
         )));
      }
      $this->getResponse()->forward($link);
   }

   /**
    * Returns the configured instance of the guestbook's data component.
    *
    * @return GuestbookMapper The mapper instance.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.05.2009<br />
    */
   private function &getMapper() {
      return $this->getDIServiceObject('APF\modules\guestbook2009\data', 'GuestbookMapper');
   }

}

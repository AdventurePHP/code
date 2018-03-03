<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
use APF\core\service\APFDIService;
use APF\core\service\APFService;
use APF\core\singleton\Singleton;
use APF\modules\guestbook2009\data\GuestbookMapper;
use APF\modules\pager\biz\PagerManager;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;
use APF\tools\link\UrlFormatException;
use Exception;

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
    * @throws Exception
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.05.2009<br />
    */
   public function loadPagedEntryList() {

      /* @var $t BenchmarkTimer */
      $t = Singleton::getInstance(BenchmarkTimer::class);
      $t->start('loadPagedEntryList');

      $pager = $this->getPager();

      $entryIds = $pager->loadEntries(['GuestbookID' => $this->getModel()->getGuestbookId()]);

      $entries = [];
      $mapper = $this->getMapper();
      foreach ($entryIds as $entryId) {
         $entries[] = $mapper->loadEntry($entryId);
      }

      $t->stop('loadPagedEntryList');

      return $entries;
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
         $this->pager = $this->getServiceObject(PagerManager::class, [], APFService::SERVICE_TYPE_NORMAL);
         $this->pager->init($this->pagerConfigSection);
      }

      return $this->pager;
   }

   /**
    * @return APFService|GuestbookModel
    */
   protected function &getModel() {
      return $this->getServiceObject(GuestbookModel::class);
   }

   /**
    * Returns the configured instance of the guestbook's data component.
    *
    * @return APFDIService|GuestbookMapper
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.05.2009<br />
    */
   private function &getMapper() {
      return $this->getDIServiceObject('APF\modules\guestbook2009\data', 'GuestbookMapper');
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
      return $this->getPager()->getPager(['GuestbookID' => $this->getModel()->getGuestbookId()]);
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
      return $this->getPager()->getPagerURLParameters();
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
      return $this->getMapper()->loadEntryListForSelection();
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
      return $this->getMapper()->loadEntry($id);
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
      return $this->getMapper()->loadGuestbook();
   }

   /**
    * Deletes the given entry.
    *
    * @param Entry $entry The entry domain object.
    *
    * @throws UrlFormatException
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.05.2009<br />
    */
   public function deleteEntry(Entry $entry) {

      if ($entry !== null) {
         $this->getMapper()->deleteEntry($entry);
      }

      // display the admin start page
      $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery([
            'gbview' => 'admin',
            'adminview' => null
      ]));
      $this->getResponse()->forward($link);
   }

   /**
    * Logs the user out and displays the list view.
    *
    * @throws UrlFormatException
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.05.2009<br />
    */
   public function logout() {

      // logout by cleaning the session
      $session = $this->getSession($this->getModel()->getGuestbookId());
      $session->delete('LoggedIn');

      // display the list view
      $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery([
            'gbview' => 'list',
            'adminview' => null
      ]));
      $this->getResponse()->forward($link);
   }

   protected function getSession($guestBookId) {
      return $this->getRequest()->getSession(__NAMESPACE__ . '\\' . $guestBookId);
   }

   /**
    * Checks, whether the current a user is logged in and the admin backend
    * may be displayed. If no, the user is redirected to the list view.
    *
    * @throws UrlFormatException
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.05.2009<br />
    */
   public function checkAccessAllowed() {

      $session = $this->getSession($this->getModel()->getGuestbookId());
      $loggedId = $session->load('LoggedIn');

      // redirect to admin page
      if ($loggedId !== 'true') {
         $startLink = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery([
               'gbview' => 'list',
               'adminview' => null
         ]));
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
    * @throws UrlFormatException
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.05.2009<br />
    */
   public function validateCredentials(User $user) {

      if ($this->getMapper()->validateCredentials($user)) {

         // log user in
         $session = $this->getSession($this->getModel()->getGuestbookId());
         $session->save('LoggedIn', 'true');

         // redirect to admin page
         $adminLink = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery([
               'gbview' => 'admin',
               'adminview' => null
         ]));
         $this->getResponse()->forward($adminLink);
      }

      return false;
   }

   /**
    * Saves the entry and forwards to the list view.
    *
    * @param Entry $entry The guestbook entry to save.
    *
    * @throws UrlFormatException
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.05.2009<br />
    */
   public function saveEntry(Entry $entry) {

      $this->getMapper()->saveEntry($entry);

      // Forward to the desired view to prevent F5-bugs.
      $entryId = $entry->getId();
      if (!empty($entryId)) {
         $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery([
               'gbview' => 'admin'
         ]));
      } else {
         $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery([
               'gbview' => 'list'
         ]));
      }
      $this->getResponse()->forward($link);
   }

}

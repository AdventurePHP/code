<?php

import('tools::request', 'RequestHandler');

class UmgtLogoutAction extends AbstractFrontcontrollerAction {

   public function run() {
      $logout = $this->getInput()->getAttribute('logout', 'false');
      if ($logout === 'true') {
         $sessionStore = &$this->getServiceObject('modules::usermanagement::biz', 'UmgtUserSessionStore', APFObject::SERVICE_TYPE_SESSIONSINGLETON);
         /* @var $sessionStore UmgtUserSessionStore */
         $sessionStore->logout();
      }
   }

}

?>
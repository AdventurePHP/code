<?php

import('modules::usermanagement::pres::documentcontroller','umgtbaseController');

class role_controller extends umgtbaseController {

 function transformContent() {
  $this->sph('manage_roles', $this->__generateLink(array('mainview' => 'role', 'roleview' => '')));
  $this->sph('role_add', $this->__generateLink(array('mainview' => 'role', 'roleview' => 'add')));
 }
}
?>
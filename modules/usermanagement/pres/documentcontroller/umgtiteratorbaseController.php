<?php

import('tools::link', 'frontcontrollerLinkHandler');
import('tools::html::taglib::documentcontroller', 'iteratorBaseController');

class umgtiteratorbaseController extends iteratorbaseController {

 function genLink($myLink, $Link = null) {
  if ($Link === null) $Link = $_SERVER['REQUEST_URI'];
  return frontcontrollerLinkHandler::generateLink($Link, $myLink);
 }
 
 // Avoids ErrorMessages if PlaceHolder does not exist in template
 function sph($PlaceHolder, $value) {
  if ($this->__placeHolderExists($PlaceHolder))
    $this->setPlaceHolder($PlaceHolder, $value);
 }
}

?>
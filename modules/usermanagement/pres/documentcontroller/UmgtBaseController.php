<?php
namespace APF\modules\usermanagement\pres\documentcontroller;

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
use APF\core\pagecontroller\BaseDocumentController;
use APF\core\pagecontroller\TemplateTag;
use APF\modules\usermanagement\biz\UmgtManager;

use APF\modules\usermanagement\pres\taglib\UmgtMediaInclusionTag;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;

/**
 * @package modules::usermanagement::pres::documentcontroller
 * @class UmgtBaseController
 *
 * Implements a base controller for the concrete document controllers of
 * the usermanagement module. Includes helper functions.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 26.12.2008<br />
 */
abstract class UmgtBaseController extends BaseDocumentController {

   /**
    * @protected
    *
    * Returns a link including the desired params and some standard parts.
    *
    * @param string[] $linkParams the desired link params.
    * @param string $baseURL the desired base url.
    * @return string The generated link.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.12.2008<br />
    */
   protected function generateLink($linkParams, $baseURL = null) {
      if ($baseURL === null) {
         $baseURL = $_SERVER['REQUEST_URI'];
      }
      return LinkGenerator::generateUrl(Url::fromString($baseURL)->mergeQuery($linkParams));
   }

   /**
    * @protected
    *
    * Initializes the umgt manager for usage within the presentation layer.
    *
    * @return UmgtManager The manager of the usermanagement module.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.04.2010<br />
    */
   protected function &getManager() {
      return $this->getDIServiceObject('APF\modules\usermanagement\biz', 'UmgtManager');
   }

   /**
    * @protected
    *
    * Returns the media inclusion tag contained in the given template.
    *
    * @param TemplateTag $template The template to search the media file tag in.
    * @return UmgtMediaInclusionTag The media file inclusion tag.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.06.2010<br />
    */
   protected function &getIcon(TemplateTag $template) {
      $children = & $template->getChildren();
      foreach ($children as $objectId => $DUMMY) {
         if ($children[$objectId] instanceof UmgtMediaInclusionTag) {
            return $children[$objectId];
         }
      }
      return null;
   }

}

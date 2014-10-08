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
namespace APF\extensions\news\pres\documentcontroller\backend;

use APF\core\pagecontroller\BaseDocumentController;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;

/**
 * Documentcontroller for news menu.
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>
 * @version
 * Version 1.0,  18.06.2011<br />
 */
class AdminNavigationController extends BaseDocumentController {

   public function transformContent() {
      $generalExclusion = array('deleteyes' => null, 'deletenewsid' => null, 'editnewsid' => null);

      $this->setPlaceHolder(
            'list', LinkGenerator::generateUrl(
                  Url::fromCurrent()
                        ->mergeQuery($generalExclusion)
                        ->mergeQuery(
                              array(
                                    'backendview' => 'list'
                              )
                        )
            )
      );
      $this->setPlaceHolder(
            'new', LinkGenerator::generateUrl(
                  Url::fromCurrent()
                        ->mergeQuery($generalExclusion)
                        ->mergeQuery(
                              array(
                                    'backendview' => 'edit'
                              )
                        )
            )
      );
   }

}

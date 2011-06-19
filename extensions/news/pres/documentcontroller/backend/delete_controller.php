<?php
/**
 *  <!--
 *  This file is part of the adventure php framework (APF) published under
 *  http://adventure-php-framework.org.
 *
 *  The APF is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The APF is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
 *  -->
 */

import('tools::request', 'RequestHandler');
import('tools::link', 'LinkGenerator');

/**
 * Documentcontroller for deleting news.
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>
 * @version 
 * Version 1.0,  18.06.2011<br />
 */
class delete_controller extends base_controller {

    public function transformContent() {
        $DeleteId = RequestHandler::getValue('deletenewsid');
        $DeleteYes = (bool) RequestHandler::getValue('deleteyes', false);

        $NewsManager = $this->getDIServiceObject('extensions::news', 'NewsManager');

        $News = $NewsManager->getNewsById((int) $DeleteId);
        if ($News === null) {
            $this->getTemplate('error')->transformOnPlace();
            return;
        }

        if ($DeleteYes === true) {
            $NewsManager->deleteNews($News);
            $Tpl = $this->getTemplate('success');
            $Tpl->transformOnPlace();
            return;
        }

        $Tpl = $this->getTemplate('delete');
        $Tpl->setPlaceHolder(
                'LinkYes', LinkGenerator::generateUrl(
                        URL::fromCurrent()->mergeQuery(
                                array(
                                    'backendview' => 'delete',
                                    'deletenewsid' => (int) $DeleteId,
                                    'deleteyes' => 'true'
                                )
                        )
                )
        );
        $Tpl->setPlaceHolder(
                'LinkNo', LinkGenerator::generateUrl(
                        URL::fromCurrent()->mergeQuery(
                                array(
                                    'backendview' => 'list',
                                    'deletenewsid' => null
                                )
                        )
                )
        );
        $Tpl->transformOnPlace();
    }

}

?>
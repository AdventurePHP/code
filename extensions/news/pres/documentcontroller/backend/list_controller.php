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

import('tools::html::taglib::documentcontroller', 'iteratorBaseController');
import('tools::link', 'LinkGenerator');

/**
 * Documentcontroller for listing the news in the backend with links for editing
 * and deleting.
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>
 * @version 
 * Version 1.0,  16.06.2011<br />
 */
class list_controller extends iteratorBaseController {

    public function transformContent() {
        $AppKey = $this->getAppKey();
        /* @var $NewsManager NewsManager */
        $NewsManager = $this->getDIServiceObject('extensions::news', 'NewsManager');
        $Count = $NewsManager->getNewsCount($AppKey);
        $NewsList = $NewsManager->getNews(0, $Count, 'DESC', $AppKey);

        if(count($NewsList) === 0){
            $this->getTemplate('noentry')->transformOnPlace();
            return;
        }
        
        $DataArray = array();
        foreach ($NewsList as &$News) {
            $DataArray[] = array(
                'Title' => htmlentities($News->getTitle(), ENT_QUOTES, 'UTF-8', false),
                'Date' => $News->getProperty('CreationTimestamp'),
                'LinkEdit' => LinkGenerator::generateUrl(URL::fromCurrent()->mergeQuery(
                                array(
                                    'backendview' => 'edit',
                                    'editnewsid' => (int) $News->getObjectId()
                                )
                        )
                ),
                'LinkDelete' => LinkGenerator::generateUrl(URL::fromCurrent()->mergeQuery(
                                array(
                                    'backendview' => 'delete',
                                    'deletenewsid' => (int) $News->getObjectId()
                                )
                        )
                )
            );
        }

        $I = $this->getIterator('newslist');
        $I->fillDataContainer($DataArray);
        $I->transformOnPlace();
    }
    
    protected function getAppKey(){
        return $this->__Document->getParentObject()->getAttribute('app-ident', $this->getContext());
    }

}
?>
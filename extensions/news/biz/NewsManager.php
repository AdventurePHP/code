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

import('extensions::news::biz', 'News');
import('tools::request', 'RequestHandler');

/**
 *  @package extensions::news::biz
 *  @class NewsManager
 *
 *  Manages the news system.
 *
 *  @author Ralf Schubert <ralf.schubert@the-screeze.de>
 *  @version
 *  Version 1.0, 16.06.2011<br />
 */
class NewsManager extends APFObject {
    /*
     * @var GenericORRelationMapper
     */
    protected $ORM = null;

    /**
     * Set's the data component.
     * 
     * @param GenericORRelationMapper $ORM
     * @return Postbox Return's itself.
     */
    public function setORM(GenericORRelationMapper &$ORM) {
        $this->ORM = $ORM;
        return $this;
    }
    
    /**
     * Get's the data component.
     * 
     * @return GenericORRelationMapper 
     */
    public function getORM(){
        return $this->ORM;
    }
    
    /**
     * Saves the given News.
     * 
     * @param News $News 
     */
    public function saveNews(News $News) {
        if($News->getAppKey() === ''){
            $News->setAppKey($this->getContext());
        }
        $this->ORM->saveObject($News);
    }
    
    /**
     * Loads a list of News.
     * 
     * @param int $Start The number of the first element which should be loaded
     * @param int $Count The number of how many news chould be loaded
     * @param string $Order The Order of how the news should be sorted by creation ('ASC' Or 'DESC')
     * @param string $AppKey Optional. Default: Current context. The application identifier, which is used to differentiate different news instances.
     * 
     * @return News[] A List of news.
     */
    public function getNews($Start = 0, $Count = 10, $Order = 'DESC', $AppKey = null) {
        if($AppKey === null){
            $AppKey = $this->getContext();
        }
        
        $crit = new GenericCriterionObject();
        $crit
            ->addCountIndicator($Start, $Count)
            ->addOrderIndicator('CreationTimestamp', $Order)
            ->addPropertyIndicator('AppKey', $AppKey);
        
        return $this->ORM->loadObjectListByCriterion('News', $crit);
    }
    
    /**
     * Returns the news with the given Id.
     * 
     * @param int $Id
     * @return News Returns null if it wasn't found. 
     */
    public function getNewsById($Id){
        return $this->ORM->loadObjectById('News', (int) $Id);
    }
    
    /**
     * Counts the News which exist for the given application key.
     * 
     * @param string $AppKey Optional. Default: Current context. The application identifier, which is used to differentiate different news instances.
     * @return int The number of existing news. 
     */
    public function getNewsCount($AppKey = null){
        if($AppKey === null){
            $AppKey = $this->getContext();
        }
        
        $Crit = new GenericCriterionObject();
        $Crit->addPropertyIndicator('AppKey', $AppKey);
        return $this->ORM->loadObjectCount('News', $Crit);
    }
    
    /**
     * Loads a list of news, supporting pagination.
     * 
     * @param int $Page Optional. If given, the news of the given page will be loaded, otherwise new page number will be loaded vom url parameter as defined.
     * @param string $Order The Order of how the news should be sorted by creation ('ASC' Or 'DESC')
     * @param string $AppKey Optional. Default: Current context. The application identifier, which is used to differentiate different news instances.
     * 
     * @return News[]
     */
    public function getNewsByPage($Page = null, $Order = 'DESC', $AppKey = null){
        $Page = $this->getPageNumber($AppKey, $Page);
        
        $Cfg = $this->getConfiguration('extensions::news', 'news');
        $Paging = $Cfg->getSection('Paging');
        $EntriesPerPage = (int)$Paging->getValue('EntriesPerPage');
        
        $Start = ($Page - 1) * $EntriesPerPage;
        
        return $this->getNews($Start, $EntriesPerPage, $Order, $AppKey);
    }
    
    /**
     * Deletes the given News.
     * 
     * @param News $News 
     */
    public function deleteNews(News $News){
        $this->ORM->deleteObject($News);
    }
    
    /**
     * Counts the number of newspages.
     * 
     * @param string $AppKey Optional. Default: Current context. The application identifier, which is used to differentiate different news instances.
     * @return int 
     */
    public function getPageCount($AppKey = null){
        if($AppKey === null){
            $AppKey = $this->getContext();
        }
        
        $Cfg = $this->getConfiguration('extensions::news', 'news');
        $EntriesPerPage = (int) $Cfg->getSection('Paging')->getValue('EntriesPerPage');
        $NewsCount = $this->getNewsCount($AppKey);
        
        return ceil($NewsCount / $EntriesPerPage);
    }
    
    /**
     * Returns the validated number of the current page.
     * 
     * @param string $AppKey Optional. Default: Current context. The application identifier, which is used to differentiate different news instances.
     * @param int $Page Optional. If set, the given page number will be validated.
     * 
     * @return int The current page number, which is within the possible range.
     */
    public function getPageNumber($AppKey = null, $Page = null){
        if($Page === null){
            $Cfg = $this->getConfiguration('extensions::news', 'news');
            $PageParameter = $Cfg->getSection('Paging')->getValue('PageParameter');
        
            $Page = RequestHandler::getValue($PageParameter, 1);
        }
        
        $Page = (int) $Page;
        $PageCount = $this->getPageCount($AppKey);
        
        if($PageCount < $Page){
            $Page = $PageCount;
        }
        
        if($Page < 1){
            $Page = 1;
        }
        
        return $Page;
    }
    
}

?>
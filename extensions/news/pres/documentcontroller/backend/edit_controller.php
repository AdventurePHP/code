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

/**
 * Documentcontroller for editing and creating news.
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>
 * @version 
 * Version 0.1,  17.06.2011<br />
 */
class edit_controller extends base_controller {
    
    public function transformContent() {
        $AppKey = $this->__Document->getParentObject()->getAttribute('app-ident', $this->getContext());
        $Form = $this->getForm('edit');
        $Cfg = $this->getConfiguration('extensions::news', 'labels.ini');
        $Lang = $Cfg->getSection($this->getLanguage());
        /* @var $NewsManager NewsManager */
        $NewsManager = $this->getDIServiceObject('extensions::news', 'NewsManager');
        
        // If an id is given, an existing news should be updated, 
        // so we check here if it really exists.
        $EditId = RequestHandler::getValue('editnewsid');
        if($EditId !== null && $EditId !== ''){
            $News = $NewsManager->getNewsById((int)$EditId);
            if($News === null){
                $this->getTemplate('notfound')->transformOnPlace();
                return;
            }
        }
        
        // Get the form elements we need later
        $FormTitle = $Form->getFormElementByID('news-edit-title');
        $FormText = $Form->getFormElementByID('news-edit-text');
        $FormUser = $Form->getFormElementByID('news-edit-user');
        $Button = $Form->getFormElementByName('send');
        
        // If input is valid, save the news.
        if($Form->isSent() && $Form->isValid()){
            if(!isset($News)){
                $News = new News();
                $News->setAppKey($AppKey);
            }
            
            $News->setTitle($FormTitle->getAttribute('value'));
            $News->setAuthor($FormUser->getAttribute('value'));
            $News->setText($FormText->getContent());
            
            $NewsManager->saveNews($News);
        }
        
        // Pre-fill form elements if an existing news should be updated
        // and take care of the right text of the button.
        if($EditId !== null && $EditId !== ''){
            $Button_value = $Lang->getValue('Form.Button.Edit');
            
            $FormTitle->setAttribute('value', htmlspecialchars($News->getTitle(), ENT_QUOTES, 'UTF-8', false));
            $FormUser->setAttribute('value', htmlspecialchars($News->getAuthor(), ENT_QUOTES, 'UTF-8', false));
            $FormText->setContent(htmlspecialchars($News->getText(), ENT_QUOTES, 'UTF-8', false));
        }
        else {
            $Button_value = $Lang->getValue('Form.Button.New');
            
            // Clear form inputs
            if($Form->isSent() && $Form->isValid()){
                $FormText->setContent('');
                $FormTitle->setAttribute('value', '');
                $FormUser->setAttribute('value', '');
            }
        }
        $Button->setAttribute('value', $Button_value);
        
        $Form->transformOnPlace();
    }

}

?>
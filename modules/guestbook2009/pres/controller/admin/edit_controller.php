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

   import('modules::guestbook2009::pres::controller::admin','backend_base_controller');
   import('tools::request','RequestHandler');

   /**
    * Implements the document controller to handle the edit flow.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 18.05.2009<br />
    */
   class edit_controller extends backend_base_controller {
       
       public function transformContent(){

          $entryId = RequestHandler::getValue('entryid');
          if($entryId === null){
            $this->__displayEntrySelection('edit');
          }
          else{

             // prefill edit form by directly accessing the APF form objects
             $gS = &$this->__getDIServiceObject('modules::guestbook2009::biz','GuestbookService');
             $form = &$this->__getForm('edit_entry');
             if($form->get('isSent') === false){

                $entry = $gS->loadEntry($entryId);
                $editor = $entry->getEditor();

                $name = &$form->getFormElementByName('name');
                $name->setAttribute('value',$editor->getName());

                $email = &$form->getFormElementByName('email');
                $email->setAttribute('value',$editor->getEmail());

                $website = &$form->getFormElementByName('website');
                $website->setAttribute('value',$editor->getWebsite());

                $title = &$form->getFormElementByName('title');
                $title->setAttribute('value',$entry->getTitle());

                $text = &$form->getFormElementByName('text');
                $text->set('Content',$entry->getText());
                
                $hiddenEntryId = &$form->getFormElementByName('entryid');
                $hiddenEntryId->setAttribute('value',$entry->getId());

                $hiddenEditorId = &$form->getFormElementByName('editorid');
                $hiddenEditorId->setAttribute('value',$editor->getId());
                
              // end if
             }
             else{

                // 2. save entry
                if($form->get('isValid') === true){

                   $entry = new Entry();
                   $editor = new User();

                   $name = &$form->getFormElementByName('name');
                   $editor->setName($name->getAttribute('value'));

                   $email = &$form->getFormElementByName('email');
                   $editor->setEmail($email->getAttribute('value'));

                   $website = &$form->getFormElementByName('website');
                   $editor->setWebsite($website->getAttribute('value'));

                   $title = &$form->getFormElementByName('title');
                   $entry->setTitle($title->getAttribute('value'));

                   $text = &$form->getFormElementByName('text');
                   $entry->setText($text->get('Content'));
                   
                   $hiddenEntryId = &$form->getFormElementByName('entryid');
                   $entry->setId($hiddenEntryId->getAttribute('value'));

                   $hiddenEditorId = &$form->getFormElementByName('editorid');
                   $editor->setId($hiddenEditorId->getAttribute('value'));

                   $entry->setEditor($editor);

                   // Problem: entry does not save the email and redirects to incorrect
                   // view in admin edit case!
                   $gS->saveEntry($entry);
                   
                 // end if
                }

              // end else
             }

             $form->transformOnPlace();

           // end else
          }

        // end function
       }
   
    // end class
   }
?>

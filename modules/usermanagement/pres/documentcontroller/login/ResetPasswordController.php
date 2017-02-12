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
namespace APF\modules\usermanagement\pres\documentcontroller\login;

use APF\core\configuration\ConfigurationException;
use APF\core\pagecontroller\BaseDocumentController;
use APF\modules\usermanagement\biz\UmgtManager;
use APF\tools\mail\MessageBuilder;
use APF\tools\mail\Recipient;
use DateInterval;
use DateTime;

/**
 * Manages the reset password form and changes the password.
 *
 * @author dave
 * @version
 * Version 0.1, 29.10.2015
 * Version 0.2, 23.02.2016 (Added the possibility to use a forgotpw configuration file)
 */
class ResetPasswordController extends BaseDocumentController {

   public function transformContent() {

      /* @var $umgt UmgtManager */
      $umgt = $this->getDIServiceObject('APF\modules\usermanagement\biz', 'UmgtManager');

      // get hash from request
      $hash = $this->getRequest()->getParameter('h');
      $user = $umgt->loadUserByForgotPasswordHash($hash);

      if ($user === null) {
         // system error meassage: no user with this hash found!?
         $this->getTemplate('system-error')->transformOnPlace();
      } else {

         // compare the timestamps - check if hash is valid
         $userTimestamp = new DateTime($user->getProperty('ModificationTimestamp'));
         $now = new DateTime('now');
         // try to get configuration file for hash-lifetime setting
         try {
            $forgetpwconfig = $this->getConfiguration('APF\modules\usermanagement\pres', 'forgotpw.ini');
            $lifetime = $forgetpwconfig->getSection('Default')->getValue('hash.lifetime') . ' seconds';
         } catch (ConfigurationException $e) {
            $lifetime = '86400 seconds';        // default lifetime if no configuration file was found - 24 hours DateInterval format!
         }

         $timestamp = $now->sub(DateInterval::createFromDateString($lifetime));

         if ($timestamp > $userTimestamp) {
            // system error message
            $this->getTemplate('system-error')->transformOnPlace();
         } else {
            $form = $this->getForm('resetpw');

            // handle form
            if ($form->isSent() && $form->isValid()) {

               $user->setForgotPasswordHash('');   // empty the field
               $user->setPassword($form->getFormElementByName('password')->getValue());   // set new password

               $umgt->saveUser($user);

               // send email to inform the user about the successful password reset
               $config = $this->getConfiguration('APF\tools\mail', 'mailsender.ini');

               $sectionName = 'UmgtForgotPassword';
               if (!$config->hasSection($sectionName)) {
                  throw new ConfigurationException('Section "' . $sectionName . '" is not defined within mailsender.ini. Please refer
                     the manual for more details.');
               }

               $labelConfig = $this->getConfiguration('APF\modules\usermanagement\pres', 'labels.ini');
               $subject = $labelConfig->getSection($this->getLanguage())->getValue('resetpw.mail.subject');
               $content = $labelConfig->getSection($this->getLanguage())->getValue('resetpw.mail.content');

               // replace the placeholders in message
               $content = str_replace('{username}', $user->getUsername(), $content);

               /* @var $builder MessageBuilder */
               $builder = $this->getServiceObject(MessageBuilder::class);
               $message = $builder->createMessage($sectionName, $subject, $content);

               $message->addRecipient(new Recipient($user->getUsername(), $user->getEMail()));

               $message->send();

               // success message
               $form->setPlaceHolder('resetpw-error', $this->getTemplate('resetpw-success')->transformTemplate());
            } elseif ($form->isSent() && !$form->isValid()) {
               // error message
               $form->setPlaceHolder('resetpw-error', $this->getTemplate('resetpw-error')->transformTemplate());
            } else {
               // nothing to do here
            }

            $form->transformOnPlace();
         }

      }

   }
}
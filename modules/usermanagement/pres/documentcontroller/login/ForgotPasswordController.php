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
use APF\tools\form\validator\AbstractFormValidator;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;
use APF\tools\mail\MessageBuilder;
use APF\tools\mail\MailAddress;
use Exception;

/**
 * Manages the forgot password form and reset the password.
 *
 * @author dave
 * @version
 * Version 0.1, 28.10.2015
 * Version 0.2, 23.02.2016 (Added the possibility to use a forgotpw configuration file)
 */
class ForgotPasswordController extends BaseDocumentController {

   public function transformContent() {

      $form = $this->getForm('forgotpw');

      // handle form
      if ($form->isSent() && $form->isValid()) {

         /* @var $umgt UmgtManager */
         $umgt = $this->getDIServiceObject('APF\modules\usermanagement\biz', 'UmgtManager');

         try {
            $user = $umgt->loadUserByEMail($form->getFormElementByName('email')->getValue());

            if ($user === null) {
               $form->getFormElementByName('email')->markAsInvalid();
               $form->getFormElementByName('email')->appendCssClass(AbstractFormValidator::$DEFAULT_MARKER_CLASS);

               // error message
               $form->setPlaceHolder('forgotpw-error', $this->getTemplate('forgotpw-error')->transformTemplate());
            } else {
               $passwordHash = md5(rand(100000, 999999));
               $user->setForgotPasswordHash($passwordHash);

               $umgt->saveUser($user);

               // send mail via Message
               // Configuration Section "UmgtForgotPassword" needed!
               $config = $this->getConfiguration('APF\tools\mail', 'mailsender.ini');

               $sectionName = 'UmgtForgotPassword';
               if (!$config->hasSection($sectionName)) {
                  throw new ConfigurationException('Section "' . $sectionName . '" is not defined within mailsender.ini. Please refer
                     the manual for more details.');
               }

               $labelConfig = $this->getConfiguration('APF\modules\usermanagement\pres', 'labels.ini');
               $subject = $labelConfig->getSection($this->getLanguage())->getValue('forgotpw.mail.subject');
               $content = $labelConfig->getSection($this->getLanguage())->getValue('forgotpw.mail.content');

               // replace placeholders in text with username and link
               $content = str_replace('{username}', $user->getUsername(), $content);
               // try to get configuration file for hash-lifetime setting
               try {
                  $forgetpwconfig = $this->getConfiguration('APF\modules\usermanagement\pres', 'forgotpw.ini');
                  $lifetime = $forgetpwconfig->getSection('Default')->getValue('hash.lifetime');
               } catch (ConfigurationException $e) {
                  $lifetime = '86400'; // default lifetime 24 hours
               }
               $content = str_replace('{lifetime}', $lifetime / 60 / 60, $content);
               $link = LinkGenerator::generateUrl(Url::fromCurrent(true)
                     ->setQueryParameter('user', 'reset_pw')
                     ->setQueryParameter('h', $passwordHash));
               $content = str_replace('{link}', $link, $content);

               /* @var $builder MessageBuilder */
               $builder = $this->getServiceObject(MessageBuilder::class);
               $message = $builder->createMessage($sectionName, $subject, $content);

               $message->addRecipient(new MailAddress($user->getUsername(), $user->getEMail()));

               $message->send();

               // display success message
               $form->setPlaceHolder('forgotpw-error', $this->getTemplate('forgotpw-success')->transformTemplate());
            }
         } catch (Exception $e) {
            // system error message
            $this->getTemplate('system-error')->transformOnPlace();
         }
      } elseif ($form->isSent() && !$form->isValid()) {
         // error message
         $form->setPlaceHolder('forgotpw-error', $this->getTemplate('forgotpw-error')->transformTemplate());
      }

      // transform form
      $form->transformOnPlace();
   }
}

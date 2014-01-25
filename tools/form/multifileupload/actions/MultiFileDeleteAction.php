<?php
namespace APF\tools\form\multifileupload\actions;

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
use APF\core\frontcontroller\AbstractFrontcontrollerAction;
use APF\tools\form\multifileupload\biz\MultiFileUploadManager;
use APF\tools\http\HeaderManager;
use APF\tools\form\FormException;

/**
 * @package APF\tools\form\multifileupload\actions
 * @class MultiFileDeleteAction
 *
 * This action deletes the given file physically and from session.
 *
 * @author Werner Liemberger <wpublicmail@gmail.com>
 * @version 1.0, 14.3.2011<br>
 * @version 1.1, 11.07.2012 (Change Exception to FormException)<br>
 * @version 1.2, 25.01.2014 (Security-patch: fixed path traversal vulnerability)<br>
 */
class MultiFileDeleteAction extends AbstractFrontcontrollerAction {

   public function run() {
      // modify header to avoid caching of this request
      HeaderManager::send('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
      HeaderManager::send('Last-Modified: ' . gmdate('D, d M Y H:i:s') . 'GMT');
      HeaderManager::send('Cache-Control: no-cache, must-revalidate');
      HeaderManager::send('Pragma: no-cache');

      try {
         $fieldName = $this->getInput()->getAttribute('name');
         $formName = $this->getInput()->getAttribute('formname');
         $uploadName = $this->getSanitizedUploadName();

         /* @var $manager MultiFileUploadManager */
         $manager = & $this->getAndInitServiceObject(
            'APF\tools\form\multifileupload\biz\MultiFileUploadManager',
            array('formname' => $formName, 'name' => $fieldName)
         );
         if ($uploadName !== null) {
            $manager->deleteFile($uploadName);
         } else {
            throw new FormException('[' . get_class($this) . '::run()] No filename was transmitted to the server!', E_USER_ERROR);
         }
         exit(0);
      } catch (FormException $e) {
         throw $e;
      }
   }
   
   /**
    * @private
    *
    * Cleans up the upload name
    *
    * @return string The upload name of the resource to delete.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 25.01.2014<br />
    */
   private function getSanitizedUploadName() {
      return preg_replace('/[^A-Za-z0-9\-_]/', '', $this->getInput()->getAttribute('uploadname'));
   }

}

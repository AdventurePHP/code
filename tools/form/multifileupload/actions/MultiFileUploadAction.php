<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
namespace APF\tools\form\multifileupload\actions;

use APF\core\frontcontroller\AbstractFrontControllerAction;
use APF\core\http\HeaderImpl;
use APF\tools\form\FormException;
use APF\tools\form\multifileupload\biz\MultiFileUploadManager;

/**
 * This action tales the files uploaded via form and saves them.
 *
 * @author Werner Liemberger <wpublicmail@gmail.com>
 * @version 1.0, 14.3.2011<br>
 * @version 1.1, 11.07.2012 (Change Exception to FormException)<br>
 */
class MultiFileUploadAction extends AbstractFrontControllerAction {

   public function run() {
      // modify header to avoid caching of this request
      $response = $this->getResponse();
      $response->setHeader(new HeaderImpl('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT'));
      $response->setHeader(new HeaderImpl('Last-Modified', gmdate('D, d M Y H:i:s') . 'GMT'));
      $response->setHeader(new HeaderImpl('Cache-Control', 'no-cache, must-revalidate'));
      $response->setHeader(new HeaderImpl('Pragma', 'no-cache'));

      try {
         $fieldName = $this->getParameters()->getParameter('name');
         $formName = $this->getParameters()->getParameter('formname');

         /* @var $manager MultiFileUploadManager */
         $manager = $this->getServiceObject(
               MultiFileUploadManager::class,
               ['formname' => $formName, 'name' => $fieldName]
         );
         if (!empty($_FILES[$fieldName])) {
            $fileInfo = $manager->addFile($_FILES[$fieldName]);
            echo json_encode($fileInfo);
         } else {
            throw new FormException('[' . get_class($this) . '::run()] No file was transmitted to the server!', E_USER_ERROR);
         }
         $response->send();
      } catch (FormException $e) {
         throw $e;
      }
   }

}

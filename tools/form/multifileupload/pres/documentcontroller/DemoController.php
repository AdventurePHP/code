<?php
namespace APF\tools\form\multifileupload\pres\documentcontroller;

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
use APF\core\pagecontroller\BaseDocumentController;
use APF\tools\form\multifileupload\pres\taglib\MultiFileUploadTag;

/**
 * @package APF\tools\form\multifileupload\pres\documentcontroller
 * @class DemoController
 *
 * Demo document controller that shows how to implement multi file upload.
 *
 * @author Werner Liemberger <wpublicmail@gmail.com>
 * @version
 * Version 1.0, 14.3.2011<br />
 */
class DemoController extends BaseDocumentController {

   public function transformContent() {

      $form = & $this->getForm('file_upload');

      /* @var $uploadTest MultiFileUploadTag */
      $uploadTest = $form->getFormElementByName('testfield');

      if ($form->isSent()) {
         $files = $uploadTest->getFiles();

         // please note the array with all files uploaded within the above form
         print_r($files);
      } else {
         $form->transformOnPlace();
      }
   }

}

<?php
namespace APF\modules\usermanagement\pres\taglib;
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
use APF\core\loader\RootClassLoader;
use APF\core\pagecontroller\Document;

/**
 * @package APF\modules\usermanagement\pres\taglib
 * @class UmgtIncludeCssTag
 *
 * Includes the css required to display the user management backend.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 30.08.2011<br />
 */
class UmgtIncludeCssTag extends Document {

   public function transform() {
      $rootPath = RootClassLoader::getLoaderByVendor('APF')->getRootPath();
      return '<style type="text/css">'
            . PHP_EOL
            . file_get_contents($rootPath . '/modules/usermanagement/pres/css/umgt.css')
            . PHP_EOL
            . '</style>';
   }

}

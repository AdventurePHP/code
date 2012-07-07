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
import('extensions::htmlheader::biz', 'JsPackageNode');
import('extensions::htmlheader::biz', 'CssPackageNode');

/**
 * @package extensions::htmlheader::pres::taglib
 * @class htmlheader_taglib_addpackage
 *
 * Enables you to add a package of combined CSS or JS files.
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>
 * @version 0.1, 20.03.2010<br />
 */
class htmlheader_taglib_addpackage extends Document {

   public function transform() {
      $header = $this->getServiceObject('extensions::htmlheader::biz', 'HtmlHeaderManager');

      $url = $this->getAttribute('url');
      $name = $this->getAttribute('name');
      $type = $this->getAttribute('type');
      $rewriting = $this->getAttribute('rewriting') === 'true' ? true : false;

      if ($type == 'js') {
         $node = new JsPackageNode($url, $name, $rewriting);

         if (strtolower($this->getAttribute('appendtobody')) === 'true') {
            $node->setAppendToBody(true);
         }

      } else {
         $node = new CssPackageNode($url, $name, $rewriting);
      }

      $node->setPriority($this->getAttribute('priority'));
      $header->addNode($node);

      return '';
   }

}

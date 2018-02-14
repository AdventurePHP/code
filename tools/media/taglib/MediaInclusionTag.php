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
namespace APF\tools\media\taglib;

use APF\core\pagecontroller\Document;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;

/**
 * Implements the base class for the <*:mediastream /> tag implementations. Generates a
 * generic front controller image source out of a namespace and file name.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.10.2008<br />
 */
class MediaInclusionTag extends Document {

   public function onParseTime() {

      $filename = $this->getRequiredAttribute('filename');

      // split filename into extension and body, since they are transferred in separate parts
      $dot = strrpos($filename, '.');
      $this->setAttribute('extension', substr($filename, $dot + 1));
      $this->setAttribute('filebody', substr($filename, 0, $dot));
   }

   /**
    * Generates the tag's output.
    *
    * @return string The desired media url
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.10.2008<br />
    * Version 0.2, 01.11.2008<br />
    * Version 0.3, 05.11.2008 (Changed action base url generation)<br />
    * Version 0.4, 07.11.2008 (Refactored the url generation due to some addressing bugs)<br />
    * Version 0.5, 20.06.2010 (Adapted parameter order to support old rewrite rules that do file extension matching for routing exceptions)<br />
    * Version 0.6, 09.04.2011 (Refactored to use release 1.14's new link generation concept)<br />
    */
   public function transform() {
      // generate action url using the APF's new link generation mechanism since 1.14
      return LinkGenerator::generateActionUrl(Url::fromCurrent(), 'APF\tools\media', 'streamMedia', [
            'namespace' => str_replace('\\', '_', $this->getRequiredAttribute('namespace')),
            'extension' => $this->getAttribute('extension'),
            'filebody'  => $this->getAttribute('filebody')
      ]);
   }

}

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
namespace APF\tools\html\taglib;

use APF\core\pagecontroller\ImportTemplateTag;
use APF\core\pagecontroller\IncludeException;
use APF\tools\http\HeaderManager;

/**
 * This class mainly implements the functionality of the core::importdesign tag. It generates a sub node
 * from the template specified by the tag's attributes within the current APF DOM tree. Each
 * importdesign tag can compose further tags. Additionally you can catch inclusion errors e.g.
 * from missing templates, and include a fallbacktemplate instead
 *
 * @author jw-lighting <jw-lighting@ewetel.net>
 * @version
 * Version 0.1, 28.08.2010<br />
 */
class FallbackImportTemplateTag extends ImportTemplateTag {

   /**
    * Implements the onParseTime() method from the Document class. Includes the desired template
    * as a new DOM node into the current APF DOM tree.
    *
    * @author jw-lighting, Christian Achatz
    * @version
    * Version 0.1, 16.07.2010, Christian Achatz (See more here: http://forum.adventure-php-framework.org/viewtopic.php?f=3&t=359#p3075)<br />
    * Version 0.2, 28.08.2010, jw-lighting (Added functionality and improved the code of v0.1 to this class)<br />
    * Version 0.3, 02.09.2010, jw-lighting (Added short-scripting support with the fallback attribute)<br />
    * Version 0.4, 03.09.2010, jw-lighting (Removed Bug: empty() can not use function return values, used temporary variables instead) <br />
    *                               (Removed Bug: The class HeaderManager is designed for static use) <br />
    *                               (Improved functionality: Added parameter 'errormsg' for failed fallback-templates include) <br />
    */
   public function onParseTime() {

      try {
         parent::onParseTime();
      } catch (IncludeException $ie) {

         $fallback = $this->getAttribute('fallback');
         if (!empty($fallback)) {
            $this->setAttribute('template', $this->getAttribute('fallback'));
         } else {
            $fallbackNamespace = $this->getAttribute('fallbackNamespace');
            if (!empty($fallbackNamespace)) {
               $this->setAttribute('namespace', $fallbackNamespace);
            }

            $this->setAttribute('template', $this->getAttribute('fallbackTemplate'));
         }

         $fallbackIncparam = $this->getAttribute('fallbackIncparam');
         if (!empty($fallbackIncparam)) {
            $this->setAttribute('incparam', $this->getAttribute('fallbackIncparam'));
         }

         // it can be necessary to send an 404-HTTP Error, check it here
         $send404Header = $this->getAttribute('send404Header');
         if ($send404Header != null && $send404Header != 'false') {
            HeaderManager::send('X-APF-Error: Template not found', true, 404);
         }

         // try to load the fallback template now
         try {
            parent::onParseTime();
         } catch (IncludeException $ie) {

            // if the template is not necessary needed, give the last chance to ignore the failure here
            $fallbackFailureIgnore = $this->getAttribute('fallbackFailureIgnore');
            if ($fallbackFailureIgnore != null && in_array($fallbackFailureIgnore, array('true', 'yes'))) {
               $this->content = $this->getAttribute('errormsg');
            }

            throw $ie;
         }

      }
   }

}
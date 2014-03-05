<?php
namespace APF\tools\link\taglib;

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
use APF\core\pagecontroller\Document;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;

/**
 * @package APF\tools\link\taglib
 * @class LinkGenerationTag
 *
 * This taglib generates a url with the given parameters taking care of the
 * current LinkScheme.
 *
 * @author Ralf Schubert <a href="http://develovision.de">Develovision Webentwicklung</a>
 * @version
 * Version 0.1, 28.07.2011<br />
 * Version 0.2, 22.11.2012 Werner Liemberger: removed a:getstring and ignored href bug<br />
 * Version 0.3, 15.05.2013 (Added use APF\tools\link\Url [Tobias Lückel|Megger])<br />
 */
class LinkGenerationTag extends Document {

   public function transform() {
      $parameters = $this->getAttributes();

      if (isset($parameters['href'])) {
         $url = Url::fromString($parameters['href']);
         unset($parameters['href']);
      } else {
         $url = Url::fromCurrent(true);
      }

      $queryOption = 'set';
      if (isset($parameters['queryoption'])) {
         $queryOption = $parameters['queryoption'];
         unset($parameters['queryoption']);
      }

      if ($queryOption === 'merge') {
         $url->mergeQuery($parameters);
      } else {
         $url->setQuery($parameters);
      }

      return LinkGenerator::generateUrl($url);
   }

}
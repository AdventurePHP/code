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
namespace APF\tools\link\taglib;

use APF\core\pagecontroller\Document;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;

/**
 * This taglib generates a url with the given parameters taking care of the
 * current LinkScheme.
 *
 * @author Ralf Schubert <a href="http://develovision.de">Develovision Webentwicklung</a>
 * @version
 * Version 0.1, 28.07.2011<br />
 * Version 0.2, 22.11.2012 Werner Liemberger: removed a:getstring and ignored href bug<br />
 * Version 0.3, 15.05.2013 (Added use APF\tools\link\Url [Tobias Lückel|Megger])<br />
 * Version 0.4, 30.06.2014 (Optimized code)<br />
 */
class LinkGenerationTag extends Document {

   const QUERY_OPTION_ATTRIBUTE_NAME = 'queryoption';
   const HREF_ATTRIBUTE_NAME = 'href';

   const QUERY_OPTION_OVERWRITE = 'set';
   const QUERY_OPTION_MERGE = 'merge';

   public function transform() {

      $href = $this->getAttribute(self::HREF_ATTRIBUTE_NAME);
      if ($href === null) {
         $url = Url::fromCurrent(true);
      } else {
         $url = Url::fromString($href);
         $this->deleteAttribute(self::HREF_ATTRIBUTE_NAME);
      }

      $queryOption = $this->getAttribute(self::QUERY_OPTION_ATTRIBUTE_NAME, self::QUERY_OPTION_OVERWRITE);
      $this->deleteAttribute(self::QUERY_OPTION_ATTRIBUTE_NAME);

      $parameters = $this->getUrlParameters();
      if ($queryOption === self::QUERY_OPTION_MERGE) {
         $url->mergeQuery($parameters);
      } else {
         $url->setQuery($parameters);
      }

      return LinkGenerator::generateUrl($url);
   }

   /**
    * Returns the parameters to include during URL generation. For this tag, it's all tag parameters.
    *
    * @return string[] Associative list of url parameters.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.05.2014<br />
    */
   protected function getUrlParameters() {
      return $this->getAttributes();
   }

}

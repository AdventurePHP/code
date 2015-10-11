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
namespace APF\modules\socialbookmark\pres\taglib;

use APF\core\pagecontroller\Document;
use APF\modules\socialbookmark\biz\SocialBookmarkBarManager;

/**
 * Implements a taglib to display social bookmark images. To configure this tag the
 * following attributes are available:
 * <ul>
 * <li>width: the width of the bookmark icon</li>
 * <li>height: the height of the bookmark icon</li>
 * <li>title: the title of the page to bookmark (optional)</li>
 * <li>url: the url of the page to bookmark (optional)</li>
 * </ul>
 *
 * @example
 * In order to use the tag, the bookmark manager must be configured. Then you can display
 * bookmark providers as follows:
 * <code>
 * &lt;social:bookmark width="16" height="16"/&gt;
 * </code>
 *
 * @author Christian W. Schäfer
 * @version
 * Version 0.1, 08.09.2007<br />
 */
class SocialBookmarkBarTag extends Document {

   public function __construct() {
      $this->setAttribute('width', '20');
      $this->setAttribute('height', '20');
      $this->setAttribute('title', null);
      $this->setAttribute('url', null);
   }

   public function transform() {

      $bm = & $this->getServiceObject('APF\modules\socialbookmark\biz\SocialBookmarkBarManager');
      /* @var $bm SocialBookmarkBarManager */

      $bm->setImageWidth($this->getAttribute('width'));
      $bm->setImageHeight($this->getAttribute('height'));
      $bm->setUrl($this->getAttribute('url'));
      $bm->setTitle($this->getAttribute('title'));

      return $bm->getBookmarkCode();
   }

}

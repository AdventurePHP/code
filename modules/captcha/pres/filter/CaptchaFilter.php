<?php
namespace APF\modules\captcha\pres\filter;

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
use APF\tools\form\filter\AbstractFormFilter;

/**
 * @package APF\modules\captcha\pres\filter
 * @class CaptchaFilter
 *
 * Implements a filter for the captcha field.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 30.08.2009<br />
 */
class CaptchaFilter extends AbstractFormFilter {

   /**
    * @public
    *
    * Filters the content of the captcha field through an aggressive
    * filter to ensure security.
    *
    * @param string $input The content to filter.
    * @return string Filtered content.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.08.2009<br />
    */
   public function filter($input) {
      return substr(preg_replace($this->getFilterExpression('/[^A-Za-z0-9]/'), '', $input), 0, 5);
   }

}

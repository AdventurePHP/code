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
namespace APF\core\filter;

use APF\core\registry\Registry;

/**
 * Filters the request to avoid XSS vulnerability. Implementation based on the original
 * wiki entry https://adventure-php-framework.org/wiki/XSS_Schutz_via_InputFilter.
 * <p/>
 * ATTENTION:
 * Please ensure that your application is not compromised from a functional
 * point of view as the inout filter maybe removes structures and/or formats
 * intentionally chosen!
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 28.01.2015 (ID#239: added input filter to the APF release.)<br />
 */
class XssProtectionInputFilter implements ChainedContentFilter {

   protected $charset;

   public function __construct() {
      $this->charset = Registry::retrieve('APF\core', 'Charset');
   }

   public function filter(FilterChain &$chain, $input = null) {
      $_POST = $this->sanitize($_POST);
      $_GET = $this->sanitize($_GET);
      $_REQUEST = $this->sanitize($_REQUEST);

      return $chain->filter($input);
   }

   protected function sanitize(array $data) {
      foreach ($data as $key => $value) {
         $data[$key] = is_array($value) ? $this->sanitize($value) : $this->cleanValue($value);
      }

      return $data;
   }

   protected function cleanValue($input) {
      return strip_tags(html_entity_decode($input, ENT_QUOTES, $this->charset));
   }

}

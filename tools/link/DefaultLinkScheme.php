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
namespace APF\tools\link;

/**
 * This link scheme implementation is intended to handle &quot;normal&quot; urls
 * that have a simple path followed by various request parameters.
 * <p/>
 * This link scheme is aware of front controller actions to be included into the
 * url by action configuration.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 04.04.2011<br />
 */
class DefaultLinkScheme extends BasicLinkScheme implements LinkScheme {

   public function formatLink(Url $url) {
      // remove existing action instructions since they should only appear with keepInUrl=true
      return $this->formatLinkInternal($this->removeActionInstructions($url));
   }

   /**
    * @param Url $url The URL instance to create a formatted link from.
    *
    * @return string The formatted URL.
    */
   protected function formatLinkInternal(Url $url) {

      $resultUrl = $this->getFormattedBaseUrl($url);

      $path = $url->getPath();
      if (!empty($path)) {
         $resultUrl .= $path;
      }

      // get URL mappings and try to resolve mapped actions
      $mappings = $this->getActionUrMappingTokens();

      $query = $url->getQuery();
      $queryString = '';

      foreach ($query as $name => $value) {
         if ($this->isValueEmpty($value)) {
            // include actions that may have empty values
            if (strpos($name, '-action') !== false || in_array($name, $mappings)) {
               if (!empty($queryString)) {
                  $queryString .= '&';
               }
               $queryString .= $name;
            }
         } else {
            if (!empty($queryString)) {
               $queryString .= '&';
            }
            $queryString .= $name . '=' . $value;
         }
      }

      if (!empty($queryString)) {
         $resultUrl .= '?' . $queryString;
      }

      // apply query to detect duplicate action definitions
      $actions = $this->getActionsUrlRepresentation($query, false);
      if (!empty($actions)) {
         $resultUrl .= strpos($resultUrl, '?') === false ? '?' : '&';
         $resultUrl .= $actions;
      }

      $resultUrl = $this->appendAnchor($resultUrl, $url);

      // encode ampersands if desired
      if ($this->getEncodeAmpersands()) {
         return str_replace('&', '&amp;', $resultUrl);
      }

      return $this->sanitizeUrl($resultUrl);
   }

   public function formatActionLink(Url $url, $namespace, $name, array $params = array()) {
      $url = $this->removeActionInstructions($url);

      return $this->formatLinkInternal(
            $url->setQueryParameter(
                  $this->formatActionIdentifier($namespace, $name, false),
                  $this->formatActionParameters($params, false)
            ));
   }

}

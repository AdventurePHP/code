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
 * This link scheme implementation is intended to handle rewritten urls
 * that have a generic path structure encoding params and their values.
 * <p/>
 * Ths link scheme is aware of front controller actions to be included into the
 * url by action configuration.
 * <p/>
 * Please note, that this link scheme ignores the ampersand encoding option,
 * since it makes no sense here.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 04.04.2011<br />
 */
class RewriteLinkScheme extends BasicLinkScheme implements LinkScheme {

   public function formatLink(Url $url) {

      // save and reset query to save variable order
      $query = $url->getQuery();
      $url->setQuery(array());

      // extract path to separate params and remove actions
      $parts = explode(self::REWRITE_PARAM_TO_ACTION_DELIMITER, $url->getPath());

      foreach ($parts as $part) {

         // only extract "normal" parameters, avoid actions
         if (strpos($part, '-action') === false) {

            $paths = explode('/', strip_tags($part));
            array_shift($paths);

            // create key => value pairs from the current request
            $x = 0;
            while ($x <= (count($paths) - 1)) {

               if (isset($paths[$x + 1])) {
                  $url->setQueryParameter($paths[$x], $paths[$x + 1]);
               }

               // increment by 2, because the next offset is the key!
               $x = $x + 2;
            }
         }
      }

      // reset the path to not have duplicate path due to generic param generation
      $url->setPath(null);

      // merge query now to overwrite values already contained in the url
      $url->mergeQuery($query);

      $resultUrl = $this->getFormattedBaseUrl($url);

      $query = $url->getQuery();
      if (count($query) > 0) {

         // get URL mappings and try to resolve mapped actions
         $mappings = $this->getActionUrMappingTokens();

         foreach ($query as $name => $value) {
            // allow empty params that are action definitions to not
            // exclude actions with no params!
            if (!$this->isValueEmpty($value)
                  || ($this->isValueEmpty($value) && strpos($name, '-action') !== false)
                  || ($this->isValueEmpty($value) && in_array($name, $mappings) !== false)
            ) {
               if (strpos($name, '-action') === false && in_array($name, $mappings) === false) {
                  $resultUrl .= '/' . $name . '/' . $value;
               } else {
                  // action blocks must be separated with group indicator
                  // to be able to parse the parameters
                  $resultUrl .= self::REWRITE_PARAM_TO_ACTION_DELIMITER . $name;

                  // check whether value is empty and append action only
                  if (!$this->isValueEmpty($value)) {
                     $resultUrl .= '/' . $value;
                  }
               }
            }
         }
      }

      // apply query to detect duplicate action definitions
      $actions = $this->getActionsUrlRepresentation($query, true);
      if (!empty($actions)) {
         $resultUrl .= self::REWRITE_PARAM_TO_ACTION_DELIMITER . $actions;
      }

      return $this->sanitizeUrl($this->appendAnchor($resultUrl, $url));
   }

   public function formatActionLink(Url $url, $namespace, $name, array $params = array()) {
      return $this->formatLink(
            $url->setQueryParameter(
                  $this->formatActionIdentifier($namespace, $name, true),
                  $this->formatActionParameters($params, true)
            ));
   }

}

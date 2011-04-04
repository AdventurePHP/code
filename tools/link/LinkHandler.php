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

/**
 * @package tools::link
 * @class LinkHandler
 * @static
 *
 * Presents a method to generate and validate urls.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 04.05.2005<br />
 * Version 0.2, 11.05.2005<br />
 * Version 0.3, 27.06.2005<br />
 * Version 0.4, 25.04.2006<br />
 * Version 0.5, 27.03.2007 (Replaced deprecated code)<br />
 * Version 0.6, 21.06.2008 (Introduced Registry)<br />
 */
class LinkHandler {

   private function LinkHandler() {
   }

   /**
    * @public
    * @static
    *
    * Implements a link creation tool for page controller based application. Generates links
    * given a base url and a set of manipulation params. Allows you to add, change and delete
    * params within the base url.
    * <p/>
    * This components lets you easily generate links, that respect all included params without
    * having to add then presenting the $_SERVER['REQUEST_URI'] as the first param.
    * <p/>
    * In case you apply the base url
    * <pre>http://myhost.de/index.php?Seite=123&Button=Send&Benutzer=456&Passwort=789</pre>
    * to the component, along with the manipulation params
    * <pre>array('Seite' => 'neueSeite','Button' => '')</pre>
    * the resulting url will be
    * <pre>http://myhost.de/index.php?Seite=neueSeite&Benutzer=456&Passwort=789</pre>
    *
    * @param string $url The base url to generate a link with.
    * @param string[] $parameter The params to manipulate the base link.
    * @param boolean $urlRewriting Indicates, whether the link should be generated in url
    *                              rewrite style (true) or not (false).
    * @param boolean $encodeAmpersands True in case the ampersands should be encoded to
    *                                  <em>&amp;</em>, false if not.
    * @return string The desired url.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 25.04.2006<br />
    * Version 0.2, 01.05.2006 (Removed bug, that values with length of one were filtered out)<br />
    * Version 0.3, 06.05.2006 (Removed bug, that misisng query generated an error)<br />
    * Version 0.4, 29.07.2006 (Refactoring to be able to create rewrite links)<br />
    * Version 0.5, 14.08.2006 (The third param is now initialized using the APPS__URL_REWRITING constant)<br />
    * Version 0.6, 24.02.2007 (Renamed to generateLink())<br />
    * Version 0.7, 27.05.2007 (Bugfix: non existent url paths are considered empty)<br />
    * Version 0.8, 02.06.2007 (Ampersands are now converted in case url rewriting is not active)<br />
    * Version 0.9, 16.06.2007 (Ampersands are now decoded at the beginning to avoid parse errors)<br />
    * Version 1.0, 26.08.2007 (URL is now checked to be a string. URL params do no like multi dimensional arrays!)<br />
    * Version 1.1, 21.06.2008 (Introduced the Registry to retrieve the URLRewriting information)<br />
    * Version 1.2, 19.03.2011 (Introduced the $encodeAmpersands param to not encode ampersands for e.g. ajax request urls)<br />
    */
   public static function generateLink($url, array $parameter, $urlRewriting = null, $encodeAmpersands = true) {

      // check, if given url is a string. if not print warning and convert to string
      // if we do not convert to string parse_url() will fail!
      if (!is_string($url)) {
         $paramStringParts = array();
         foreach ($parameter as $paramKey => $paramValue) {
            $paramStringParts[] = $paramKey . '=' . $paramValue;
         }
         trigger_error('[LinkHandler::generateLink()] Given url (' . $url . ') is not a string! '
                 . 'Given parameters are [' . implode(',', $paramStringParts) . ']', E_USER_WARNING);
         $url = strval($url);
      }

      // decode ampersands
      $url = str_replace('&amp;', '&', $url);

      $parsedURL = parse_url($url);

      // resolve missing query string
      if (!isset($parsedURL['query'])) {
         $parsedURL['query'] = (string) '';
      }

      // resolve missing path
      if (!isset($parsedURL['path'])) {
         $parsedURL['path'] = (string) '';
      }

      // set URLRewrite
      if ($urlRewriting === null) {
         $urlRewriting = Registry::retrieve('apf::core', 'URLRewriting');
      }

      if ($urlRewriting == true) {

         // extract request to array
         $requestArray = explode('/', strip_tags($parsedURL['path']));
         array_shift($requestArray);

         $splitURL = array();
         $x = 0;

         // create key => value pairs from the current request
         while ($x <= (count($requestArray) - 1)) {

            if (isset($requestArray[$x + 1])) {
               $splitURL[$requestArray[$x]] = $requestArray[$x + 1];
            }

            // increment by 2, because the next offset is the key!
            $x = $x + 2;

         }

         $splitParameters = $splitURL;

      } else {
         $splitURL = explode('&', $parsedURL['query']);

         $splitParameters = array();

         for ($i = 0; $i < count($splitURL); $i++) {

            // do only add parameters with length greater 3
            if (strlen($splitURL[$i]) > 3) {
               $equalSign = strpos($splitURL[$i], '=');
               $splitParameters[substr($splitURL[$i], 0, $equalSign)] = substr($splitURL[$i], $equalSign + 1, strlen($splitURL[$i]));
            }

         }

      }

      // create the final param set (this allows deletions with offsets that are empty or null!)
      $splitParameters = array_merge($splitParameters, $parameter);

      $query = (string) '';

      foreach ($splitParameters as $key => $value) {

         // only allow keys with more than 1 character and a minimum length of 0.
         // this enables the developer to delete params by applying an empty string
         // or null as the param's value. in case the value is an array, deny it!
         if (!is_array($value)) {

            if (strlen($key) > 1 && strlen($value) > 0) {

               // add '?' as first delimiter
               if (strlen($query) == 0) {
                  $query .= '?';
               } else {
                  $query .= '&';
               }

               $query .= trim($key) . '=' . trim($value);

            }

         }

      }

      $newURL = (string) '';

      // in case schema and host is given add it!
      if (isset($parsedURL['scheme']) && isset($parsedURL['host'])) {
         $newURL .= $parsedURL['scheme'] . '://' . $parsedURL['host'];
      }

      // if only the host is present, apply it either
      if (!isset($parsedURL['scheme']) && isset($parsedURL['host'])) {
         $newURL .= '/' . $parsedURL['host'];
      }

      // in case a none-standard port is given, apply it!
      if (!empty($parsedURL['port']) && $parsedURL['port'] != '80' && $parsedURL['port'] != '443') {
         $newURL .= ':' . $parsedURL['port'];
      }

      // assemble final url
      if ($urlRewriting == true) {
         $finishedURL = $newURL . '/' . $query;
      } else {
         $finishedURL = $newURL . $parsedURL['path'] . $query;
      }

      // rewrite url
      if ($urlRewriting == true) {

         $replace = array('./?' => '/',
             '/?' => '/',
             '=' => '/',
             '&' => '/'
         );
         $finishedURL = strtr($finishedURL, $replace);

      } else {
         // re-encode ampersands if desired
         if ($encodeAmpersands) {
            $finishedURL = str_replace('&', '&amp;', $finishedURL);
         }
      }

      return $finishedURL;

   }

}
?>
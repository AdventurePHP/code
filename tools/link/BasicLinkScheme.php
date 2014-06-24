<?php
namespace APF\tools\link;

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
use APF\core\frontcontroller\AbstractFrontcontrollerAction;
use APF\core\frontcontroller\ActionUrlMapping;
use APF\core\frontcontroller\Frontcontroller;
use APF\core\singleton\Singleton;

/**
 * @package APF\tools\link
 * @class BasicLinkScheme
 *
 * Implements basic functionality used by different link scheme implementations.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 07.04.2011<br />
 */
abstract class BasicLinkScheme {

   const DEFAULT_PARAM_TO_ACTION_DELIMITER = '&';
   const REWRITE_PARAM_TO_ACTION_DELIMITER = '/~/';

   /**
    * @var boolean Indicates whether to encode ampersands or not.
    */
   private $encodeAmpersands;

   public function __construct($encodeAmpersands = true) {
      $this->setEncodeAmpersands($encodeAmpersands);
   }

   public function setEncodeAmpersands($encodeAmpersands) {
      $this->encodeAmpersands = $encodeAmpersands;
   }

   public function getEncodeAmpersands() {
      return $this->encodeAmpersands;
   }

   /**
    * @protected
    *
    * Creates a base url string including scheme, host and port in case
    * it differs from the default ports.
    *
    * @param Url $url The current url representation.
    *
    * @return string The formatted base url.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.04.2011<br />
    */
   protected function getFormattedBaseUrl(Url $url) {

      $baseUrl = '';

      $scheme = $url->getScheme();
      if (!empty($scheme)) {
         $baseUrl .= $scheme . '://';
      }

      $host = $url->getHost();
      if (!empty($host)) {
         $baseUrl .= $host;
      }

      $port = $url->getPort();
      if (!empty($port) && $port !== Url::DEFAULT_HTTP_PORT && $port !== Url::DEFAULT_HTTPS_PORT) {
         $baseUrl .= ':' . $port;
      }

      return $baseUrl;
   }

   /**
    * @protected
    *
    * Retrieves the current action stack from the front controller.
    *
    * @return AbstractFrontcontrollerAction[] The list of registered actions.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2011<br />
    */
   protected function &getFrontcontrollerActions() {
      return $this->getFrontController()->getActions();
   }

   /**
    * @return Frontcontroller
    */
   protected function &getFrontController() {
      return Singleton::getInstance('APF\core\frontcontroller\Frontcontroller');
   }

   /**
    * @protected
    *
    * Creates a url sub-string that contains all action's encoded information that
    * have the <em>keepInUrl</em> flag set to true.
    * <p/>
    * In case an action has the <em>keepInUrl</em> flag defined with <em>true</em> and
    * you decide to manually add the same action again using <em>LinkScheme::formatActionUrl()</em>
    * the action definition added manually overrides the automatically generated action
    * instruction.
    * <p/>
    * This means: in case an action with namespace <em>APF\tools\media</em> and name
    * <em>streamMedia</em> is on the front controller action stack including parameters
    * <em>foo</em> (=1) and <em>bar</em> (=2) and is added manually by
    * <code>
    * $url = Url::fromCurrent();
    * $link = LinkGenerator::generateActionUrl($url, 'APF\tools\media', 'streamMedia', array('baz' => 1));
    * </code>
    * the resulting url will be <em>...?APF_tools_media-action:streamMedia=baz:1</em> instead of
    * <em>...?APF_tools_media-action:streamMedia=foo:1|bar:2</em>.
    *
    * @param array $query The current list of parameters.
    * @param boolean $urlRewriting True in case url rewriting is activated, false otherwise.
    *
    * @return string All actions' url representations.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.04.2011<br />
    * Version 0.2, 20.08.2013 (Added check for duplicate action generation caused by manual generateActionUrl() calls)<br />
    */
   protected function getActionsUrlRepresentation(array $query, $urlRewriting) {

      // retrieve actions from internal method (to enable testing)
      $actions = & $this->getFrontcontrollerActions();

      $actionUrlRepresentation = array();
      foreach ($actions as $action) {
         /* @var $action AbstractFrontcontrollerAction */
         if ($action->getKeepInUrl() === true) {
            // Only add actions in case they are not yet added within LinkGenerator::generateActionUrl().
            // This is done to avoid duplicate action definition within the generated url. Please note,
            // that this means "last definition wins" - manual definition overrides automatic generation.
            // We can use this mechanism/logic here, because actions are always appended to the regular
            // query. Hence, the given query will contain all manual action definitions before automatic
            // appending takes place.
            $key = $this->formatActionIdentifier($action->getActionNamespace(), $action->getActionName(), $urlRewriting);
            if (!isset($query[$key])) {
               $actionUrlRepresentation[] = $this->getActionUrlRepresentation($action, $urlRewriting);
            }
         }
      }

      if (count($actionUrlRepresentation) > 0) {
         $delimiter = $urlRewriting === true ? self::REWRITE_PARAM_TO_ACTION_DELIMITER
               : self::DEFAULT_PARAM_TO_ACTION_DELIMITER;

         return implode($delimiter, $actionUrlRepresentation);
      }

      return '';
   }

   /**
    * @protected
    *
    * Creates a url sub-string that contains one action's encoded information.
    *
    * @param AbstractFrontcontrollerAction $action The front controller action.
    * @param boolean $urlRewriting True in case url rewriting is activated, false otherwise.
    *
    * @return string The action's url representations.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.04.2011<br />
    */
   protected function getActionUrlRepresentation(AbstractFrontcontrollerAction $action, $urlRewriting) {
      $value = $this->formatActionParameters($action->getInput()->getParameters(), $urlRewriting);
      $key = $this->formatActionIdentifier($action->getActionNamespace(), $action->getActionName(), $urlRewriting);

      // avoid "=" sign with empty value list
      $actionParamsDelimiter = $urlRewriting === true ? '/' : '=';

      return empty($value) ? $key : $key . $actionParamsDelimiter . $value;
   }

   /**
    * @protected
    *
    * Returns an url identifier that addresses the action described by the applied parameters.
    *
    * @param string $namespace The namespace of the action.
    * @param string $name The name of the action.
    * @param boolean $urlRewriting True for activated url rewriting, false instead.
    *
    * @return string The formatted action identifier.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.04.2011<br />
    */
   protected function formatActionIdentifier($namespace, $name, $urlRewriting) {
      // ID#63: get action URL mapping from front controller and format if appropriate
      $mapping = $this->getActionUrlMapping($namespace, $name);
      if ($mapping !== null) {
         return $mapping->getUrlToken();
      }

      return str_replace('\\', '_', $namespace) . '-action' . ($urlRewriting === true ? '/' : ':') . $name;
   }

   /**
    * @param string $namespace The namespace of the action.
    * @param string $name The name of the action.
    *
    * @return ActionUrlMapping|null The desired action URL mapping or <em>null</em>.
    */
   protected function getActionUrlMapping($namespace, $name) {
      return $this->getFrontController()->getActionUrlMapping($namespace, $name);
   }

   /**
    * @return string[] The list of action URL mapping tokens.
    */
   protected function getActionUrMappingTokens() {
      return $this->getFrontController()->getActionUrlMappingTokens();
   }

   /**
    * @protected
    *
    * Returns all input parameters as a url formatted string.
    *
    * @param array $params The action parameters.
    * @param boolean $urlRewriting True for activated url rewriting, false instead.
    *
    * @return string The url formatted attributes string.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.02.2007<br />
    * Version 0.2, 08.11.2007 (Corrected error with empty input object)<br />
    * Version 0.3, 21.06.2008 (Removed APPS__URL_REWRITING and introduced the Registry instead)<br />
    * Version 0.4, 25.03.2011 (Renamed to <em>getParameterURLRepresentation()</em> due to page controller refactoring)<br />
    * Version 0.5, 08.04.2011 (Moved from FrontcontrollerInput to the link scheme implementation for consistency)<br />
    */
   protected function formatActionParameters(array $params, $urlRewriting) {

      $groupDelimiter = $urlRewriting === true ? '/' : '|';
      $keyValueDelimiter = $urlRewriting === true ? '/' : ':';

      // fill consolidated attributes array
      $groups = array();

      if (count($params) > 0) {
         foreach ($params as $key => $value) {
            $groups[] = $key . $keyValueDelimiter . $value;
         }
      }

      return implode($groupDelimiter, $groups);
   }

   /**
    * @protected
    *
    * Safely tests whether the applied value is considered empty or not.
    *
    * @param string $value The value to check.
    *
    * @return bool True in case the value is empty, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.10.2012 (Bug-fix: special empty check now respects "0" values)<br />
    */
   protected function isValueEmpty($value) {
      return empty($value) && strval($value) !== '0';
   }

   /**
    * @protected
    *
    * Appends an anchor if present within the url representation applied to a LinkScheme
    * implementation.
    *
    * @param string $urlString The url constructed by the given url representation.
    * @param Url $url The current url representation potentially including the anchor.
    *
    * @return string The final url.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.11.2013<br />
    */
   protected function appendAnchor($urlString, Url $url) {
      // append anchor only if desired
      $anchor = $url->getAnchor();
      if (!empty($anchor)) {
         $urlString .= '#' . $anchor;
      }

      return $urlString;
   }

   /**
    * @protected
    *
    * Removes action instructions from the query of a Url instance. Only works for
    * normal URLs.
    *
    * @param Url $url The url to clean up.
    *
    * @return Url The url instance without action instructions-.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 14.03.2014<br />
    */
   protected function removeActionInstructions(Url $url) {

      $mappings = $this->getActionUrMappingTokens();

      $query = $url->getQuery();

      // remove actions from query (either explicitly expressed by the action keyword or by url token)
      foreach ($query as $name => $value) {
         if (strpos($name, '-action') !== false || in_array($name, $mappings)) {
            unset($query[$name]);
         }
      }

      return $url->setQuery($query);
   }

}

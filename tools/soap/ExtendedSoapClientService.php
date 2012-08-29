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
import('tools::soap', 'WsdlObjectMapping');

/**
 * @package tools::soap
 * @class ExtendedSoapClientService
 *
 * Implements a wrapper class for PHP's SoapClient service to have a more convenient service and to
 * be able to create it using the APF's DI service manager implementation.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 26.01.2012<br />
 */
class ExtendedSoapClientService extends APFObject {

   /**
    * @var string[] The options of the SoapClient instance.
    */
   private $options = array();

   /**
    * @var string The url of the WSDL.
    */
   private $wsdlUrl;

   /**
    * @var SoapClient The instance of the PHP soap client.
    */
   private $client = null;

   /**
    * @public
    *
    * Creates the instance of the APF SoapClient wrapper.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 24.01.2012<br />
    */
   public function __construct() {

      // set default values and merge array
      $this->options = array(

         /**
          * The soap_version option specifies whether to use SOAP 1.1 (default), or SOAP 1.2 client.
          * Default to be able to evaluate headers
          */
         'soap_version' => SOAP_1_2,

         /**
          * Use UTF-8 encoding to be compatible with most of the services (e.g. JAVA services).
          */
         'encoding' => 'UTF-8',

         /**
          * Setting the boolean trace option enables use of the methods SoapClient->__getLastRequest,
          * SoapClient->__getLastRequestHeaders, SoapClient->__getLastResponse and SoapClient->__getLastResponseHeaders.
          */
         'trace' => TRUE,

         /**
          * The exceptions option is a boolean value defining whether soap errors throw exceptions of type SoapFault.
          */
         'exceptions' => TRUE
      );
   }

   /**
    * @protected
    *
    * Factory method for the PHP SoapClient.
    *
    * @return SoapClient The instance of the soap client.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.01.2012<br />
    */
   protected function getClient() {
      if ($this->client === null) {
         $this->client = new SoapClient($this->wsdlUrl, $this->options);
      }
      return $this->client;
   }

   /**
    * @public
    *
    * This method provides a method call to a SOAP method. In order to execute the call you have
    * to provide the name of the method (action) and an associative array including the input
    * parameters. The parameters are directly mapped to the SOAP request structured defined within
    * the WSDL. Thus, the names of the parameters MUST match the data types (normally defined within
    * the XSD attached/linked to the WSDL.
    * <p/>
    * This method supports the object mapping feature of PHP's SOAP client implementation. This means
    * that you are able to retrieve POPOs (a.k.a. plain old PHP objects) that represent the returned
    * XML structure. Please note that the object mapping feature (see <em>classmap</em> option at the
    * PHP.net manual) requires the mapping being defined correctly. This means, that the name of the
    * type MUST be equal to the complex or simple type defined within the XSD. The name of the POPO
    * can be defined freely. The ExtendedSoapClientService introduces automatic setup of the mapping
    * using the <em>WsdlObjectMapping</em> class. This feature can be used with the DI container as
    * well.
    * <p/>
    * Due to PHP bug https://bugs.php.net/bug.php?id=50997 the implementation relies on PHP's magic
    * __call() method. This seams to be a workaround for us since providing a dedicated method such
    * as executeSoapRequest() results in the described error.
    *
    * @example
    * <code>
    * class LoginResponse {
    *
    *    private $token;
    *
    *    public function setToken($token) {
    *       $this->token = $token;
    *    }
    *
    *    public function getToken() {
    *       return $this->token;
    *    }
    *
    * }
    *
    * $client = new ExtendedSoapClientService();
    * $client->setWsdlUrl('https://example.com/services/v1?wsdl');
    * $client->setLocation('https://example.com/services/v1');
    *
    * $client->registerWsdlObjectMapping(new WsdlObjectMapping(
    *    'login-response',
    *    'sample::namespace',
    *    'LoginResponse'
    * ));
    *
    * $params = array(
    *    'alias' => 'my user name',
    *    'secret' => 'my secret'
    * );
    *
    * $response = $client->Authenticate($params);
    *
    * echo $response->getToken();
    * </code>
    *
    * @param string $action The name of the SOAP method.
    * @param array $arguments The SPA method's input parameters (usually an associative array).
    * @return mixed The response of the call (string or a response object).
    * @throws SoapFault In case of any SOAP call error.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.01.2012<br />
    */
   public function __call($action, $arguments) {
      $client = $this->getClient();

      // we are taking the first argument only since it contains the parameters to pass to the
      // SOAP request (specialty of PHP's magic __call() method that passes the list of
      // arguments as list)
      $params = isset($arguments[0]) ? array($arguments[0]) : array();
      $response = $client->__soapCall($action, $params);

      // check for hidden soap faults
      if (isset($client->__soap_fault) && $client->__soap_fault != null) {
         throw new SoapFault('SOAP-ERROR', 'SOAP call "' . $action . '"" failed with request "' . $arguments . '" with message "'
               . $client->__soap_fault . '"! ' . 'Cause: ' . $response);
      }

      return $response;
   }

   /**
    * @public
    *
    * This method provides a low level method to call a SOAP method. In order to execute the call
    * you have to provide the name of the method (action) and the SOAP request (input) as a string.
    * <p/>
    * Please note that the object mapping feature of PHP's SOAP client implementation is
    * <strong>NOT</strong> working with this method. If you want to use it, please use the
    * <em>executeRequest()</em> function.
    *
    * @example
    * <code>
    * $request = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
    *                <soapenv:Header/>
    *                <soapenv:Body>
    *                   ...
    *                </soapenv:Body>
    *             </soapenv:Envelope>';';
    * $client = new ExtendedSoapClientService();
    * $client->setWsdlUrl('https://example.com/services/v1?wsdl');
    * $client->setLocation('https://example.com/services/v1');
    * $responseXml = $client->executeTextRequest('GetNews', $request);
    * </code>
    *
    * @param string $action The WSDL method's name.
    * @param string $request The SOAP request string.
    * @param boolean $oneWay True in case no answer is expected.
    * @return SimpleXMLElement|null The answer of the request or null in case $oneWay is set to TRUE.
    * @throws SoapFault In case of any SOAP call error.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.01.2012<br />
    */
   public function executeRequest($action, $request, $oneWay = null) {

      // lazily construct the client to be able to configure it by nice setter methods.
      $client = $this->getClient();

      if ($oneWay === true) {
         $client->__doRequest($request, $this->options['location'], $action, $this->getSoapVersion());
         return null;
      } else {
         $response = $client->__doRequest($request, $this->options['location'], $action, $this->getSoapVersion(), $oneWay);

         // check for hidden soap faults
         if (isset($client->__soap_fault) && $client->__soap_fault != null) {
            throw new SoapFault('SOAP-ERROR', 'SOAP call "' . $action . '"" failed with request "' . $request
                  . '" with message "' . $client->__soap_fault . '"! ' . 'Cause: ' . $response);
         }

         // create XML structure to simply take-n-go the result of the call
         $xml = simplexml_load_string($response);

         // automatically register included SOAP namespaces
         foreach ($xml->getNamespaces(true) as $prefix => $namespace) {
            $xml->registerXPathNamespace($prefix, $namespace);
         }

         return $xml;
      }
   }

   /**
    * @param string $location The location of the SOAP service.
    * @return ExtendedSoapClientService This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.01.2012<br />
    */
   public function setLocation($location) {
      $this->options['location'] = $location;

      // reconfiguration requires to create a new instance.
      $this->client = null;

      return $this;
   }

   /**
    * @return string The location of the SOAP service.
    */
   public function getLocation() {
      return $this->options['location'];
   }

   /**
    * @param string $wsdlUrl The location of the WSDL of the web service to consume.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.01.2012<br />
    */
   public function setWsdlUrl($wsdlUrl) {
      $this->wsdlUrl = $wsdlUrl;
   }

   /**
    * @return string The location of the WSDL service that is currently configured.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.01.2012<br />
    */
   public function getWsdlUrl() {
      return $this->wsdlUrl;
   }

   /**
    * Configures the The HTTP BASE AUTH user name for protected SOAP resources.
    *
    * @param string $username The HTTP BASE AUTH user name.
    * @return ExtendedSoapClientService This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.05.2012<br />
    */
   public function setHttpAuthUsername($username) {
      $this->options['login'] = $username;

      // reconfiguration requires to create a new instance.
      $this->client = null;

      return $this;
   }

   /**
    * Configures the The HTTP BASE AUTH password for protected SOAP resources.
    *
    * @param string $password The HTTP BASE AUTH password.
    * @return ExtendedSoapClientService This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.05.2012<br />
    */
   public function setHttpAuthPassword($password) {
      $this->options['password'] = $password;

      // reconfiguration requires to create a new instance.
      $this->client = null;

      return $this;
   }

   /**
    * Let's you inject the HTTP BASE AUTH credentials via dependency injection to ease configuration
    * (service injection).
    * <p/>
    * Besides, the credentials may be injected using the setHttpAuthUsername() and setHttpAuthPassword()
    * methods as well (configuration injection).
    *
    * @param SoapHttpBaseAuthCredentials $credentials The HTTP BASE AUTH credentials to apply to the connection.
    * @return ExtendedSoapClientService This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.05.2012<br />
    */
   public function setHttpAuthCredentials(SoapHttpBaseAuthCredentials $credentials) {
      $this->setHttpAuthUsername($credentials->getUsername());
      $this->setHttpAuthPassword($credentials->getPassword());

      return $this;
   }

   /**
    * @param int $compression The compression to use/accept.
    * @return ExtendedSoapClientService This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.01.2012<br />
    */
   public function setCompressionLevel($compression) {
      $this->options['compression'] = $compression;

      // reconfiguration requires to create a new instance.
      $this->client = null;

      return $this;
   }

   /**
    * @var int The connection_timeout option defines a timeout in seconds for the connection to the SOAP service. This option does not define a timeout for services with slow responses. To limit the time to wait for calls to finish the default_socket_timeout setting is available.
    * @return ExtendedSoapClientService This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.01.2012<br />
    */
   public function setConnectionTimeout($timeout) {
      $this->options['connection_timeout'] = $timeout;

      // reconfiguration requires to create a new instance.
      $this->client = null;

      return $this;
   }

   /**
    * @var int The cache_wsdl option is one of WSDL_CACHE_NONE, WSDL_CACHE_DISK, WSDL_CACHE_MEMORY or WSDL_CACHE_BOTH.
    * @return ExtendedSoapClientService This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.01.2012<br />
    */
   public function setCacheWsdl($cacheWsdl) {
      $this->options['cache_wsdl'] = $cacheWsdl;

      // reconfiguration requires to create a new instance.
      $this->client = null;

      return $this;
   }

   /**
    * @param string $encoding The encoding to use.
    * @return ExtendedSoapClientService This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.01.2012<br />
    */
   public function setEncoding($encoding) {
      $this->options['encoding'] = $encoding;

      // reconfiguration requires to create a new instance.
      $this->client = null;

      return $this;
   }

   /**
    * @param int $version The soap version.
    * @return ExtendedSoapClientService This instance for further usage.
    */
   public function setSoapVersion($version) {
      $this->options['soap_version'] = $version;

      // reconfiguration requires to create a new instance.
      $this->client = null;

      return $this;
   }

   /**
    * @return int The soap version.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.01.2012<br />
    */
   public function getSoapVersion() {
      return $this->options['soap_version'];
   }

   /**
    * Let's you enable special features of the PHP SOAP implementation. These are:
    * <ul>
    * <li>SOAP_SINGLE_ELEMENT_ARRAYS (map single element responses to array for elements of type sequence/minoccurs > 0;
    * see http://de.php.net/manual/en/soapclient.soapclient.php#73082)</li>
    * <li>SOAP_USE_XSI_ARRAY_TYPE (see http://de.php.net/manual/en/soapclient.soapclient.php#86908)</li>
    * <li>SOAP_WAIT_ONE_WAY_CALLS (see http://de.php.net/manual/en/soapclient.soapclient.php#98613)</li>
    * </ul>
    * For further details, please see http://de.php.net/manual/en/soapclient.soapclient.php.
    *
    * @param int $mask The bit mask of the feature to enable.
    * @return ExtendedSoapClientService This instance for further usage.
    */
   public function enableFeature($mask) {

      if (!isset($this->options['features'])) {
         $this->options['features'] = $mask;
      } else {
         $this->options['features'] = $this->options['features'] | $mask;
      }

      // reconfiguration requires to create a new instance.
      $this->client = null;

      return $this;
   }

   /**
    * @param WsdlObjectMapping $mapping The object mapping of WSDL types to PHP objects.
    * @return ExtendedSoapClientService This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.01.2012<br />
    */
   public function registerWsdlObjectMapping(WsdlObjectMapping $mapping) {
      $this->options['classmap'][$mapping->getWsdlType()] = $mapping->getPhpClassName();

      if (!class_exists($mapping->getPhpClassName())) {
         import($mapping->getPhpClassNamespace(), $mapping->getPhpClassName());
      }

      // reconfiguration requires to create a new instance.
      $this->client = null;

      return $this;
   }

   /**
    * @return string The headers of the last request.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.02.2012<br />
    */
   public function getLastRequestHeaders() {
      return $this->getClient()->__getLastRequestHeaders();
   }

   /**
    * @return string The content of the last request.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.02.2012<br />
    */
   public function getLastRequest() {
      return $this->getClient()->__getLastRequest();
   }

   /**
    * @return string The headers of the last response.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.02.2012<br />
    */
   public function getLastResponseHeaders() {
      return $this->getClient()->__getLastResponseHeaders();
   }

   /**
    * @return string The content of the last response.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.02.2012<br />
    */
   public function getLastResponse() {
      return $this->getClient()->__getLastResponse();
   }

   /**
    * @return array The list of methods that are defined within the WSDL applied to setWsdlUrl().
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.02.2012<br />
    */
   public function getFunctions() {
      return $this->getClient()->__getFunctions();
   }

   /**
    * @return array The list of types that are defined within WSDL applied to setWsdlUrl().
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.02.2012<br />
    */
   public function getTypes() {
      return $this->getClient()->__getTypes();
   }

   /**
    * @param string $name The name of the cookie.
    * @param string $value The value of the cookie.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.02.2012<br />
    */
   public function setCookie($name, $value) {
      $this->getClient()->__setCookie($name, $value);
   }

   /**
    * @param mixed $headers The soap headers to set for the subsequent calls.
    * @return boolean True in case of success, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.02.2012<br />
    */
   public function setSoapHeaders($headers) {
      return $this->getClient()->__setSoapHeaders($headers);
   }

   /**
    * @param string $host The proxy host.
    * @return ExtendedSoapClientService This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2012<br />
    */
   public function setProxyHost($host) {
      $this->options['proxy_host'] = $host;

      // reconfiguration requires to create a new instance.
      $this->client = null;

      return $this;
   }

   /**
    * @param string $port The proxy port.
    * @return ExtendedSoapClientService This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2012<br />
    */
   public function setProxyPort($port) {
      $this->options['proxy_port'] = $port;

      // reconfiguration requires to create a new instance.
      $this->client = null;

      return $this;
   }

   /**
    * @param string $username The proxy user name.
    * @return ExtendedSoapClientService This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2012<br />
    */
   public function setProxyUsername($username) {
      $this->options['proxy_login'] = $username;

      // reconfiguration requires to create a new instance.
      $this->client = null;

      return $this;
   }

   /**
    * @param string $password The proxy password.
    * @return ExtendedSoapClientService This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2012<br />
    */
   public function setProxyPassword($password) {
      $this->options['proxy_password'] = $password;

      // reconfiguration requires to create a new instance.
      $this->client = null;

      return $this;
   }

   /**
    * @param string $userAgent The user agent to send along with the SOAP request.
    * @return ExtendedSoapClientService This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2012<br />
    */
   public function setUserAgent($userAgent) {
      $this->options['user_agent'] = $userAgent;

      // reconfiguration requires to create a new instance.
      $this->client = null;

      return $this;
   }

}
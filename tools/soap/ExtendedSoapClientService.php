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
import('tools::soap', 'XPathNamespace');

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
     * @var XPathNamespace[] Registered namespaces for the default response parsing.
     */
    private $namespaces = array();

    /**
     * @var string The xpath expressions to extract a fault from the payload. Correlates with the registered namespaces!
     */
    private $faultXpathExpression;

    /**
     * @var string The url of the WSDL.
     */
    private $wsdlUrl;

    /**
     * @var string The SOAP service location.
     */
    private $location;

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
     * @param string $request
     * @param string $action
     * @param boolean $oneWay True in case no answer is expected.
     * @return SimpleXMLElement|null The answer of the request or null in case $oneWay is set to TRUE.
     * @throws SoapFault In case of any SOAP call error.
     */
    public function executeRequest($request, $action, $oneWay = null) {

        // lazily construct the client to be able to configure it by nice setter methods.
        $client = $this->getClient();

        if ($oneWay === null) {
            $client->__doRequest($request, $this->location, $action, $this->getSoapVersion());
            return null;
        } else {
            $response = $client->__doRequest($request, $this->location, $action, $this->getSoapVersion(), $oneWay);

            // check for hidden soap faults
            if ((isset($client->__soap_fault)) && ($client->__soap_fault != null)) {
                throw new SoapFault('SOAP call "' . $action . '"" failed with request "' . $request . '" with message "'
                    . $client->__soap_fault . '"! ' . 'Cause: ' . $response);
            }

            // return XML structure with correct namespaces already
            $xml = simplexml_load_string($response);
            foreach ($this->namespaces as $namespace) {
                $xml->registerXPathNamespace($namespace->getPrefix(), $namespace->getNamespace());
            }

            // check for soap faults within the response body
            $fault = $xml->xpath($this->faultXpathExpression);
            if (count($fault) > 0) {
                throw new SoapFault('SOAP call "' . $action . '"" failed with request "' . $request . '"! '
                    . 'Cause: ' . $response);
            }

            return $response;
        }
    }

    /**
     * @param string $location The location of the SOAP service.
     * @return ExtendedSoapClientService This instance for further usage.
     */
    public function setLocation($location) {
        $this->location = $location;
        return $this;
    }

    /**
     * @return string The location of the SOAP service.
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * @param string $wsdlUrl
     */
    public function setWsdlUrl($wsdlUrl) {
        $this->wsdlUrl = $wsdlUrl;
    }

    /**
     * @return string
     */
    public function getWsdlUrl() {
        return $this->wsdlUrl;
    }

    /**
     * @param string $login
     * @param string $password
     * @return ExtendedSoapClientService This instance for further usage.
     */
    public function setHttpAuthCredentials($login, $password) {
        $this->options['login'] = $login;
        $this->options['password'] = $password;

        // reconfiguration requires to create a new instance.
        $this->client = null;

        return $this;
    }

    /**
     * @param int $compression The compression to use/accept.
     * @return ExtendedSoapClientService This instance for further usage.
     */
    public function setCompressionLevel($compression) {
        $this->options['compression'] = $compression;

        // reconfiguration requires to create a new instance.
        $this->client = null;

        return $this;
    }

    /**
     * @var The connection_timeout option defines a timeout in seconds for the connection to the SOAP service. This option does not define a timeout for services with slow responses. To limit the time to wait for calls to finish the default_socket_timeout setting is available.
     * @return ExtendedSoapClientService This instance for further usage.
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
     */
    public function getSoapVersion() {
        return $this->options['soap_version'];
    }

    /**
     * @param XPathNamespace $namespace The desired namespace to register.
     */
    public function registerXPathNamespace(XPathNamespace $namespace) {
        $this->namespaces[] = $namespace;
    }

    /**
     * @param string $faultXpathExpression The xpath expression to extract a fault from the payload.
     * @return ExtendedSoapClientService This instance for further usage.
     */
    public function setFaultXpathExpression($faultXpathExpression) {
        $this->faultXpathExpression = $faultXpathExpression;
        return $this;
    }

}
<?php
/**
 *  <!--
 *  This file is part of the adventure php framework (APF) published under
 *  http://adventure-php-framework.org.
 *
 *  The APF is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The APF is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
 *  -->
 */
 
abstract class HttpTransaction {

    protected $headers = array();

    const HEADER_CONTENT_TYPE = 'Content-Type';
    const HEADER_CONTENT_LENGTH = 'Content-Length';

    public function addHeader($name, $value) {
        $this->headers[trim($name)] = trim($value);
        return $this;
    }
    
    public function addHeaders(array $headers) {
        foreach ($headers as $key => $value) {
            $this->addHeader($key, $value);
        }
        return $this;
    }
    
    public function setHeadersFromString($string) {
        // make empty
        $this->headers = array();
   
        $headerLines = explode("\r\n", $string); 
        
        // we don't need the first line
        unset($headerLines[0]); 
        
        foreach ($headerLines as $line) {
            $parts = explode(':', $line);
           
            $this->addHeader($parts[0], $parts[1]);
        }
        
        return $this;
    }
    
    public function getHeader($name) {
        return isset($this->headers[$name]) ? $this->header[$name] : null;
    }
    
    public function getHeaders() {
        return $this->headers;
    }    
    
    public function removeHeader($name) {
        if (isset($this->headers[$name])) {
            unset($this->headers[$name]);
        }
        return $this;
    } 
    
    public function removeHeaders() {
        $this->headers = array();
        return $this;
    }      
}

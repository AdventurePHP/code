<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
 * Defines the structure of the APF link scheme implementations. A link scheme
 * represents a kind of url formatter that is used by the <em>LinkGenerator</em>.
 * <p/>
 * Normally, link schemes are implemented together with input filters, that can
 * resolve the link formatting. The APF therefore ships two link scheme implementations
 * that follow the url structure of the <em>ChainedUrlRewritingInputFilter</em>.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.03.2011<br />
 */
interface LinkScheme {

   /**
    * @param Url $url The url to generate.
    *
    * @return string The result url.
    */
   public function formatLink(Url $url);

   /**
    * @param Url $url The url representation.
    * @param string $namespace The action's namespace.
    * @param string $name The action's name
    * @param array $params The action's parameters.
    *
    * @return string The result url.
    */
   public function formatActionLink(Url $url, $namespace, $name, array $params = []);

   public function setEncodeAmpersands($encode);

   public function getEncodeAmpersands();

}

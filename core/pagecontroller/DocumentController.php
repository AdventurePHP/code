<?php
namespace APF\core\pagecontroller;

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
use APF\core\service\APFDIService;

/**
 * @package APF\core\pagecontroller
 * @class DocumentController
 *
 * Defines the interface for APF document controller implementations.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 09.02.2013<br />
 * Version 0.2, 16.08.2013 (Document controllers are now able to be created by the DIServiceManager)<br />
 * Version 0.3, 01.04.2014 (Removed content handling passing the current document's content to the document controller)<br />
 */
interface DocumentController extends APFDIService {

   /**
    * @public
    *
    * Injects the document into the document controller. This enables the developer
    * to retrieve information and DOM elements stored in the node, the controller
    * is responsible to transform.
    *
    * @param Document $document The dom node, the controller is intended to transform.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function setDocument(Document &$document);

   /**
    * Returns the document that represents the present DOM node the
    * controller is responsible for.
    *
    * @return Document The present DOM node.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.10.2010<br />
    */
   public function &getDocument();

   /**
    * @public
    *
    * Interface definition of the transformContent() method. This function is applied to a
    * document controller during the transformation of a DOM node. It must be implemented by
    * each document controller to influence content generation.
    *
    * @return void
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    */
   public function transformContent();

}

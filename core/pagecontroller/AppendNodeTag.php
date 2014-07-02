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
namespace APF\core\pagecontroller;

use InvalidArgumentException;

/**
 * @package APF\core\pagecontroller
 * @class AppendNodeTag
 *
 * Parses a template and appends the child nodes to the parent's child list. Hence, this taglib
 * can be used to swap shared templates / forms / ... Please make sure, that the imported template
 * includes the necessary "*:addtaglib" statements for the relevant tag definitions.
 * <p/>
 * Usage:
 * <pre>&lt;core:addtaglib class="APF\core\pagecontroller\AppendNodeTag" prefix="core" class="appendnode" /&gt;
 * &lt;core:appendnode namespace="..." template="..."[ includestatic="true"]/&gt;</pre>
 * <p/>
 * In case the <em>includestatic</em> is present and set to <em>true</em>, the static content
 * (e.g. html markup) is included in the parent's content, too.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 16.11.2008<br />
 */
class AppendNodeTag extends Document {

   /**
    * @var string Indicates, whether the static content of an included templates
    * should be appended to the target template (<em>true</em>) or not (<em>false</em>).
    */
   protected static $INCLUDE_STATIC_CONTENT_ATTRIBUTE_NAME = 'includestatic';

   /**
    * @public
    *
    * Load the content and parse the template.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.11.2008<br />
    */
   public function onParseTime() {

      // check attributes
      $namespace = $this->getAttribute('namespace');
      if ($namespace === null) {
         throw new InvalidArgumentException('[AppendNodeTag::onParseTime()] Attribute '
               . '"namespace" is not present or empty! Please provide the namespace of the '
               . 'desired template.', E_USER_ERROR);
      }

      $template = $this->getAttribute('template');
      if ($template === null) {
         throw new InvalidArgumentException('[AppendNodeTag::onParseTime()] Attribute '
               . '"template" is not present or empty! Please provide the name of the desired '
               . 'template.', E_USER_ERROR);
      }

      // load the content
      $this->loadContentFromFile($namespace, $template);

      // parse known tags
      $this->extractTagLibTags();

   }

   /**
    * @public
    *
    * Appends the node's children to the parent node.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.11.2008<br />
    * Version 0.2, 16.11.2008 (Bug-fix: added a parent object reference correction for the new nodes)<br />
    * Version 0.3, 16.11.2008 (Enhancement: added a tag marker to the parent object to enable the transformOnPlace() feature)<br />
    * Version 0.4, 22.02.2010 (Added possibility to include static content)<br />
    */
   public function onAfterAppend() {

      // ID#191: extract "static" expressions (e.g. place holders)
      $this->extractExpressionTags();

      // get parent children list
      /* @var $parentChildren Document[] */
      $parentChildren = & $this->parentObject->getChildren();
      $parentContent = $this->parentObject->getContent();
      $currentObjectId = $this->getObjectId();

      // include static content, if desired.
      // code duplication is done to speed up the DOM node relocation!
      $includeStatic = $this->getAttribute(self::$INCLUDE_STATIC_CONTENT_ATTRIBUTE_NAME);
      if ($includeStatic === 'true') {

         foreach ($this->children as $objectId => $DUMMY) {

            // append node to parent object's children list
            $parentChildren[$objectId] = & $this->children[$objectId];

            // correct the parent object reference
            $parentChildren[$objectId]->setParentObject($this->parentObject);
         }

         // include complete content of the current document and append it to
         // the place holder of the present tag's marker
         $this->parentObject->setContent(
               str_replace('<' . $currentObjectId . ' />',
                     '<' . $currentObjectId . ' />' . $this->content,
                     $parentContent)
         );

      } else {

         foreach ($this->children as $objectId => $DUMMY) {

            // append node to parent object's children list
            $parentChildren[$objectId] = & $this->children[$objectId];

            // correct the parent object reference
            $parentChildren[$objectId]->setParentObject($this->parentObject);

            // add a marker tag to the parent object after the tag's marker
            $parentContent = str_replace('<' . $currentObjectId . ' />', '<' . $currentObjectId . ' /><' . $objectId . ' />', $parentContent);
            $currentObjectId = $objectId;

         }

         // include content of the current document
         $this->parentObject->setContent($parentContent);

      }
   }

   /**
    * @public
    *
    * Returns an empty string, due to the fact, that the core:appendnode tag does not have to
    * create output.
    *
    * @return string Nothing since the tag generates no output.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.11.2008<br />
    */
   public function transform() {
      return '';
   }

}

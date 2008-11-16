<?php
   /**
   *  @package core::pagecontroller
   *  @class core_taglib_appendnode
   *
   *  Parses a template and appends the child nodes to the parent's child list. Hence, this taglib
   *  can be used to swap shared templates / forms / ... Please make sure, that the imported template
   *  includes the necessary "*:addtaglib" statements for the relevant tag definitions. Usage:
   *  <pre>&lt;core:addtaglib namespace="core::pagecontroller" prefix="core" class="appendnode" /&gt;
   *  &lt;core:appendnode namespace="..." template="..." /&gt;</pre>
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 16.11.2008<br />
   */
   class core_taglib_appendnode extends Document
   {

      /**
      *  @public
      *
      *  Initializes the known taglib list.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 16.11.2008<br />
      */
      function core_taglib_appendnode(){
         parent::Document();
       // end function
      }


      /**
      *  @public
      *
      *  Load the content and parse the template.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 16.11.2008<br />
      */
      function onParseTime(){

         // check attributes
         $namespace = $this->getAttribute('namespace');
         if($namespace === null){
            trigger_error('[core_taglib_appendnode::onParseTime()] Attribute "namespace" is not present or empty! Please provide the namespace of the desired template.',E_USER_ERROR);
            exit(1);
          // end if
         }

         $template = $this->getAttribute('template');
         if($template === null){
            trigger_error('[core_taglib_appendnode::onParseTime()] Attribute "template" is not present or empty! Please provide the name of the desired template.',E_USER_ERROR);
            exit(1);
          // end if
         }

         // load the content
         $this->__loadContentFromFile($namespace,$template);

         // parse known tags
         $this->__extractTagLibTags();

       // end function
      }


      /**
      *  @public
      *
      *  Appends the node's children to the parent node.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 16.11.2008<br />
      *  Version 0.2, 16.11.2008 (Bugfix: added a parent object reference correction for the new nodes (line 88))<br />
      */
      function onAfterAppend(){

         // get parent children list
         $parentChildren = &$this->__ParentObject->getByReference('Children');

         foreach($this->__Children as $objectId => $DUMMY){

            // append node to parent object's children list
            $parentChildren[$objectId] = &$this->__Children[$objectId];

            // correct the parent object refrence
            $parentChildren[$objectId]->setByReference('ParentObject',$this->__ParentObject);

          // end foreach
         }

         // the only problem could be, that the parent node's transform method can't find
         // the remark tags (<{object_id]} />). So let's test it.

       // end function
      }


      /**
      *  @public
      *
      *  Returns an empty string, due to the fact, that the core:appendnode tag does not have to
      *  create output.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 16.11.2008<br />
      */
      function transform(){
         return (string)'';
       // end function
      }

    // end class
   }
?>
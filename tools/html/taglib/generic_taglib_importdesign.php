<?php
   /**
   *  @class generic_taglib_importdesign
   *
   *  Implements a fully generic including tag. The tag retrieves both namespace and the template
   *  name from the desired model object. Further, the developer is free to choose, which mode is
   *  used to fetch the model object from the ServiceManager. For details on the modes, please have
   *  a look at the ServiceManager documentation. To use this tag, the following attributes must be
   *  involved:
   *  <pre>&lt;generic:importdesign
   *              modelnamespace=""
   *              modelfile=""
   *              modelclass=""
   *              modelmode="NORMAL|SINGLETON|SESSIONSINGLETON"
   *              namespaceparam=""
   *              templateparam=""
   *              [getmethode=""]
   *  &gt;</pre>
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 30.10.2008<br />
   *  Version 0.2, 01.11.2008 (Added documentation and introduced the modelmode and getmethode params)<br />
   */
   class generic_taglib_importdesign extends core_taglib_importdesign
   {

      /**
      *  @public
      *
      *  Constructor of the class. Calls the parent's constructor to build the known taglib list.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 30.10.2008<br />
      */
      function generic_taglib_importdesign(){
         parent::core_taglib_importdesign();
       // end function
      }


      /**
      *  @public
      *
      *  Handles the tag's attributes (ses class documentation) and includes the desired template.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 30.10.2008<br />
      *  Version 0.2, 01.11.2008 (Added the modelmode and getmethode params)<br />
      */
      function onParseTime(){

         // modelnamespace=""
         $modelNamespace = $this->getAttribute('modelnamespace');
         if($modelNamespace === null){
            trigger_error('[generic_taglib_importdesign::onParseTime()] The attribute "modelnamespace" is empty or not present. Please provide the namespace of the model within this attribute!');
            return null;
          // end if
         }

         // modelfile=""
         $modelFile = $this->getAttribute('modelfile');
         if($modelFile === null){
            trigger_error('[generic_taglib_importdesign::onParseTime()] The attribute "modelfile" is empty or not present. Please provide the name of the model file within this attribute!');
            return null;
          // end if
         }

         // modelclass=""
         $modelClass = $this->getAttribute('modelclass');
         if($modelClass === null){
            trigger_error('[generic_taglib_importdesign::onParseTime()] The attribute "modelclass" is empty or not present. Please provide the name of the model class within this attribute!');
            return null;
          // end if
         }

         // modelmode="NORMAL|SINGLETON|SESSIONSINGLETON"
         $modelMode = $this->getAttribute('modelmode');
         if($modelMode === null){
            trigger_error('[generic_taglib_importdesign::onParseTime()] The attribute "modelmode" is empty or not present. Please provide the service type of the model within this attribute! Allowed values are NORMAL, SINGLETON or SESSIONSINGLETON.');
            return null;
          // end if
         }

         // namespaceparam=""
         $namespaceParam = $this->getAttribute('namespaceparam');
         if($namespaceParam === null){
            trigger_error('[generic_taglib_importdesign::onParseTime()] The attribute "namespaceparam" is empty or not present. Please provide the name of the model param for the namespace of the template file within this attribute!');
            return null;
          // end if
         }

         // templateparam=""
         $templateParam = $this->getAttribute('templateparam');
         if($templateParam === null){
            trigger_error('[generic_taglib_importdesign::onParseTime()] The attribute "templateparam" is empty or not present. Please provide the name of the model param for the name of the template file within this attribute!');
            return null;
          // end if
         }

         // getmethode="" (e.g. "getAttribute" or "get")
         $getMethode = $this->getAttribute('getmethode');
         if($getMethode === null){
            $getMethode = 'getAttribute';
          // end if
         }

         // include the model class
         if(!class_exists($modelClass)){
            import($modelNamespace,$modelFile);
          // end if
         }

         // get model
         $model = &$this->__getServiceObject($modelNamespace,$modelClass,$modelMode);

         // check for the get method
         if(!method_exists($model,$getMethode)){
            trigger_error('[generic_taglib_importdesign::onParseTime()] The model class ("'.$modelClass.'") does not support the method "'.$getMethode.'" provided within the "getmethode" attribute. Please provide the correct function name!');
            return null;
          // end if
         }

         // read the params from the model
         $templateNamespace = $model->$getMethode($namespaceParam);
         if(empty($templateNamespace)){
            trigger_error('[generic_taglib_importdesign::onParseTime()] The model ("'.$modelClass.'") returned an empty value when trying to get the template namespace using the "'.$getMethode.'" method! Please specify another getter or check the model class implementation!');
            return null;
          // end if
         }

         $templateName = $model->$getMethode($templateParam);
         if(empty($templateName)){
            trigger_error('[generic_taglib_importdesign::onParseTime()] The model ("'.$modelClass.'") returned an empty value when trying to get the template name using the "'.$getMethode.'" method! Please specify another getter or check the model class implementation!');
            return null;
          // end if
         }

         // import desired template
         $this->__loadContentFromFile($templateNamespace,$templateName);
         $this->__extractDocumentController();
         $this->__extractTagLibTags();

       // end function
      }

    // end class
   }
?>
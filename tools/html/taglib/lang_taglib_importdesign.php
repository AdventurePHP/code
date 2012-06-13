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
 * @package tools::html::taglib
 * @class lang_taglib_importdesign
 *
 * This is an extension of the core:importdesign taglib. With this taglib you are
 * able to use language-dependent incparams. The names of these language-dependent
 * parameters have to be stored in a config file.
 * <p/>
 * If the called template does not exist the default template is loaded.
 * <p/>
 * To use this tag, the following attributes must be involved
 * <p/>
 * <pre>&lt;lang:importdesign
 *             namespace=""
 *             template=""
 *             incparam=""
 *             [context=""]
 *             [language=""]
 *             [config=""]
 *             [configNamespace=""]
 *             [deactivateConfigException="false"]
 *             [prefix=""]
 * /&gt;</pre>
 * <p/>
 * <ul>
 *   <li>namespace: template namespace</li>
 *   <li>template: template identification (language-independent)</li>
 *   <li>incparam: name of the parameter which will be looked up in the config and replaced afterwards. (default: incparam)</li>
 *   <li>config: name of the config file which shout be used. (default: requestparameter.ini)
 *   <li>configNamespace: namespace which should be used for the config file.</li>
 *   <li>context: change context for current node (default: current context, value: Has to be a valid context)</li>
 *   <li>language: change language for current node (default: current language, value: Has to be a valid language)</li>
 *   <li>deactivateConfigException: By setting its value to "true" you will not get any exceptions if the config file is not found</li>
 *   <li>prefix: prefix which is used to build up the template file name.
 * </ul>
 * <p/>
 * Template filename sheme:
 * If prefix!=''
 * {prefix}_{lang}_{Identification}_{Name}.html
 * otherwise:
 * {lang}_{Identification}_{Name}.html
 *
 * @author Werner Liemberger wpublicmail [AT] gmail DOT com
 * @version
 * Version 0.1, 28.7.2011<br />
 */
class lang_taglib_importdesign extends core_taglib_importdesign {

   /**
    * Function builds up full name of the template. If some information are missing
    * they will be replaced by "*". This is necessary to find the files with the glob method.
    * <p/>
    * Scheme:
    * <p/>
    * If prefix!=''
    * {prefix}_{lang}_{Identification}_{Name}.html
    * otherwise:
    * {lang}_{Identification}_{Name}.html
    *
    * @author Werner Liemberger wpublicmail [AT] gmail DOT com
    * @version
    * Version 0.1, 28.7.2011<br />
    *
    * @param string $prefix
    * @param string $identification Language-independent template ID (needed to find related templates in different languages.)
    * @param string $name Language-dependent name
    * @param string $lang Language which should be used.
    * @return string Templatefilename
    */
   private function filename($prefix = '', $identification = '', $name = '', $lang = null) {
      if ($lang === null) {
         $lang = $this->getLanguage();
      }
      if ($identification == '') {
         $identification = '*';
      }
      if ($name == '') {
         $name = '*';
      }
      if ($prefix != '') {
         $prefix .= '_';
      }

      return $prefix . $lang . '_' . $identification . '_' . $name . '.html';
   }

   public function onParseTime() {

      $templateID = $this->getAttribute('template');
      if ($templateID === null) {
         throw new InvalidArgumentException('[lang_taglib_importdesign::onParseTime()] Attribute "template" is not given!');
      }

      $namespace = $this->getAttribute('namespace');
      if ($namespace === null) {
         throw new InvalidArgumentException('[lang_taglib_importdesign::onParseTime()] Attribute "namespace" is not given!');
      }

      $incparam = $this->getAttribute('incparam', 'incparam');
      $prefix = $this->getAttribute('prefix', '');
      $context = $this->getAttribute('context', null);

      if ($context !== null) {
         $this->setContext($context);
      }

      $Language = $this->getAttribute('language', null);
      if ($Language !== null) {
         $this->setLanguage($Language);
      }

      $configFile = $this->getAttribute('config', 'requestparameter.ini');
      $configNamespace = $this->getAttribute('configNamespace', 'tools::html');

      // load Config
      try {
         $config = $this->getConfiguration($configNamespace, $configFile);

         // get language-dependent parameters form config
         $sec = $config->getSection($this->getLanguage());
         if ($sec !== null) {
            $incParamName = $sec->getValue($incparam);
         }
      } catch (Exception $e) {
         if ($this->getAttribute('deactivateConfigException', 'false') != "true") {
            throw $e;
         }
      }
      if (!isset($incParamName) || $incParamName === null) {
         $incParamName = $incparam;
      }


      /*
      * Check if parameter name is in Url and load template afterwards.
      * If requested template does not exist, load default.
      */
      $template = '';

      if (isset($_REQUEST[$incParamName]) && $_REQUEST[$incParamName] !== null && $_REQUEST[$incParamName] != '') {
         $templateToTest = $this->filename($prefix, '', $_REQUEST[$incParamName]);
         $files = glob(str_replace('::', '/', APPS__PATH . '::' . $namespace) . '/' . $templateToTest);
         if (count($files) >= 1) {
            $template = substr(str_replace(str_replace('::', '/', APPS__PATH . '::' . $namespace . '::'), '', $files[0]), 0, -5);
         }
      }

      if ($template == '') {
         // load default
         $templateToTest = $this->filename($prefix, $templateID);
         $files = glob(str_replace('::', '/', APPS__PATH . '::' . $namespace) . '/' . $templateToTest);
         if (count($files) >= 1) {
            $template = substr(str_replace(str_replace('::', '/', APPS__PATH . '::' . $namespace . '::'), '', $files[0]), 0, -5);
         }
      }

      $this->__loadContentFromFile($namespace, $template);
      $this->__extractDocumentController();
      $this->__extractTagLibTags();
   }

}
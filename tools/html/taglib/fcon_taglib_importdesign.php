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
    * @class fcon_taglib_importdesign
    *
    * Implementiert ein core::importdesign-Tag, das den aktuellen View aus dem Model der <br />
    * Anwendung ausliest. Der Tag kann per Attributen f�r jede Anwendung generisch konfiguriert <br />
    * werden. Erwartet die Attribute
    * <ul>
    *   <li>templatenamespace: Namespace des Templates (Wert: g�ltiger Namespace)</li>
    *   <li>modelnamespace: Namespace des Applikationsmodels (Wert: g�ltiger Namespace)</li>
    *   <li>modelfile: Name der Datei des Models (Wert: Dateiname)</li>
    *   <li>modelclass: Name der Model-Klasse (Wert: Klassenname)</li>
    *   <li>modelparam: Name des Attributs des Models, das als Templatename verwendet werden soll</li>
    *   <li>context: Setzt den Context des Knotens (Wert: G�ltiger Context)</li>
    *   <li>sessionsingleton: defines, if the model is retrieved sessionsingleton or just singleton (values: true|false)</li>
    * </ul>
    * Alle Parameter ausser "context" sind Pflichtparameter.<br />
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.11.2007<br />
    */
   class fcon_taglib_importdesign extends core_taglib_importdesign {

      public function __construct() {
         parent::__construct();
      }

      /**
       *  @public
       *
       *  Implementierung der abstrakten Methode aus "APFObject". Bindet das Template, das in den
       *  Attributen beschreiben ist als neuen Objekt-Baum-Knoten ein.<br />
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 13.11.2007<br />
       *  Version 0.2, 30.08.2008 (Fixed bug, that model class was not included)<br />
       *  Version 0.3, 23.10.2008 (Added the sessionsingleton param to be able to handle session models)<br />
       */
      public function onParseTime() {

         $T = &Singleton::getInstance('BenchmarkTimer');
         $T->start('(fcon_taglib_importdesign) ' . $this->__ObjectID . '::onParseTime()');

         if (!isset($this->__Attributes['templatenamespace'])) {
            throw new InvalidArgumentException('[fcon_taglib_importdesign::onParseTime()] Attribute "templatenamespace" is not given!');
         } else {
            $templateNamespace = $this->__Attributes['templatenamespace'];
         }

         if (!isset($this->__Attributes['modelnamespace'])) {
            throw new InvalidArgumentException('[fcon_taglib_importdesign::onParseTime()] Attribute "modelnamespace" is not given!');
         } else {
            $modelNamespace = $this->__Attributes['modelnamespace'];
         }

         if (!isset($this->__Attributes['modelfile'])) {
            throw new InvalidArgumentException('[fcon_taglib_importdesign::onParseTime()] Attribute "modelfile" is not given!');
         } else {
            $modelFile = $this->__Attributes['modelfile'];
         }

         if (!isset($this->__Attributes['modelclass'])) {
            throw new InvalidArgumentException('[fcon_taglib_importdesign::onParseTime()] Attribute "modelclass" is not given!');
         } else {
            $modelClass = $this->__Attributes['modelclass'];
         }

         if (!isset($this->__Attributes['modelparam'])) {
            throw new InvalidArgumentException('[fcon_taglib_importdesign::onParseTime()] Attribute "modelparam" is not given!');
         } else {
            $modelParam = $this->__Attributes['modelparam'];
         }

         if (!class_exists($modelClass)) {
            import($modelNamespace, $modelFile);
         }

         // get initialization mode
         if (!isset($this->__Attributes['sessionsingleton']) || $this->__Attributes['sessionsingleton'] == 'false') {
            $initMode = 'SINGLETON';
         } else {
            $initMode = 'SESSIONSINGLETON';
         }

         // read the name of the template from the model
         $model = &$this->__getServiceObject($modelNamespace, $modelClass, $initMode);
         $templateName = $model->getAttribute($modelParam);

         if (isset($this->__Attributes['context'])) {
            $this->__Context = trim($this->__Attributes['context']);
         }

         $this->__loadContentFromFile($templateNamespace, $templateName);

         $this->__extractDocumentController();

         $this->__extractTagLibTags();

         $T->stop('(fcon_taglib_importdesign) ' . $this->__ObjectID . '::onParseTime()');

      }

    // end class
   }
?>
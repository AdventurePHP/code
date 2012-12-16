<?php

class FormLabelTag extends AbstractFormControl {

   public function __construct() {
      $this->attributeWhiteList = array('for');
   }

   public function transform() {
      if ($this->isVisible) {
         return '<label ' . $this->getSanitizedAttributesAsString($this->getAttributes()) . '>'
               . $this->getContent()
               . '</label>';
      }
      return '';
   }

}

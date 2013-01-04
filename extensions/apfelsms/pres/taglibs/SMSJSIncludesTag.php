<?php
/**
 *
 * @package APFelSMS
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version:   v0.1 (08.08.12)
 *             v0.2 (30.09.12) Removed extension appending
 */
class SMSJSIncludesTag extends Document {


   /**
    * @var string HTML-Template for JS includes
    */
   protected static $JSIncludeTemplate = '<script type="text/javascript" src="{URL}"></script>';


   /**
    * @var string
    */
   protected static $newLine = "\n";


   /**
    * @return string
    */
   public function transform() {

      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('extensions::apfelsms', 'Manager');

      $currentPage = $SMSM->getSite()->getCurrentPage();


      if ($currentPage === null) { // this is no normal operation, but ...
         return ''; // be quiet
      }

      $jsArray = $currentPage->getJS();

      if (count($jsArray) < 1) {
         return ''; // no scripts to include
      }


      $stringBuffer = '';

      foreach ($jsArray AS $urlReplacer) {


         $stringBuffer .= str_replace(
            '{URL}',
            $urlReplacer,
            self::$JSIncludeTemplate
         );

         $stringBuffer .= self::$newLine;

      }

      return $stringBuffer;

   }

}

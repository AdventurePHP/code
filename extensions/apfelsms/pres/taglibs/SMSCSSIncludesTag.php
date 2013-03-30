<?php
namespace APF\extensions\apfelsms\pres\taglibs;

/**
 *
 * @package APFelSMS
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version:   v0.1 (08.08.12)
 *             v0.2 (30.10.12) Removed extension appending
 */
class SMSCSSIncludesTag extends Document {


   /**
    * @var string HTML-Template for CSS includes
    */
   protected static $CSSIncludeTemplate = '<link rel="stylesheet" type="text/css" media="{MEDIA}" href="{URL}" />';


   /**
    * @var string
    */
   protected static $newLine = "\n";


   /**
    * @return string
    */
   public function transform() {

      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('APF\extensions\apfelsms', 'Manager');

      $currentPage = $SMSM->getSite()->getCurrentPage();


      if ($currentPage === null) { // this is no normal operation, but ...
         return ''; // be quiet
      }

      $cssArray = $currentPage->getCSS();

      if (count($cssArray) < 1) {
         return ''; // no styles to include
      }


      $stringBuffer = '';

      foreach ($cssArray AS $media => $urlReplacer) {

         $mediaReplacer = 'all';
         if (is_string($media)) {
            $mediaReplacer = $media;
         }


         $stringBuffer .= str_replace(
            array('{MEDIA}', '{URL}'),
            array($mediaReplacer, $urlReplacer),
            self::$CSSIncludeTemplate
         );

         $stringBuffer .= self::$newLine;

      }

      return $stringBuffer;

   }

}

<?php
namespace APF\tests\suites\core\pagecontroller;

use APF\core\pagecontroller\TemplateTag;

class PlaceHolderSpeedTest extends \PHPUnit_Framework_TestCase {

   /**
    * Speed test to set place holders.
    */
   public function testSetPlaceHolder() {
      $template = $this->getTemplate();

      $start = microtime(true);
      for ($i = 0; $i < 10000; $i++) {
         $template->setPlaceHolders([
               'headline'     => 'Headline',
               'sub-headline' => 'Sub line',
               'content'      => 'This is teaser content!',
               'url'          => 'http://adventure-php-framework.org',
               'label'        => 'APF web site'
         ]);
      }
      $end = microtime(true);

      echo PHP_EOL . 'Duration testSetPlaceHolder(): ' . ($end - $start) . 's';

   }

   protected function getTemplate() {
      $node = new TemplateTag();
      $node->setContent('<h2 class="...">${headline}</h2>
<h3 class="...">${sub-headline}</h3>
<p>
   ${content}
</p>
<a href="${url}">${label}</a>');
      $node->onParseTime();
      $node->onAfterAppend();

      return $node;
   }

   public function testTransform() {

      $template = $this->getTemplate();
      $template->setPlaceHolders([
            'headline'     => 'Headline',
            'sub-headline' => 'Sub line',
            'content'      => 'This is teaser content!',
            'url'          => 'http://adventure-php-framework.org',
            'label'        => 'APF web site'
      ]);

      $start = microtime(true);
      for ($i = 0; $i < 10000; $i++) {
         $template->transformTemplate();
      }
      $end = microtime(true);

      echo PHP_EOL . 'Duration testTransform(): ' . ($end - $start) . 's';

   }

}

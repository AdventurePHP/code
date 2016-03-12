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
namespace APF\tests\suites\core\pagecontroller;

use APF\core\pagecontroller\TemplateTag;

class PlaceHolderSpeedTest extends \PHPUnit_Framework_TestCase {

   const ITERATIONS = 10000;

   /**
    * Speed test to set place holders.
    */
   public function testSetPlaceHolder() {
      $template = $this->getTemplate();

      $start = microtime(true);
      for ($i = 0; $i < self::ITERATIONS; $i++) {
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
<a href="${url}">${label}</a>
<h2 class="...">${headline}</h2>
<h3 class="...">${sub-headline}</h3>
<p>
   ${content}
</p>
<a href="${url}">${label}</a>
<h2 class="...">${headline}</h2>
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
      for ($i = 0; $i < self::ITERATIONS; $i++) {
         $template->transformTemplate();
      }
      $end = microtime(true);

      echo PHP_EOL . 'Duration testTransform(): ' . ($end - $start) . 's';

   }

}

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
namespace APF\tests\suites\core\frontcontroller;

use APF\core\frontcontroller\ActionUrlMapping;
use APF\core\frontcontroller\Frontcontroller;

/**
 * Tests action mapping registration with the front controller.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 18.03.2014<br />
 */
class FrontControllerActionMappingTest extends \PHPUnit_Framework_TestCase {

   public function testMappingRegistration() {

      $fC = new Frontcontroller();

      $fooUrlToken = 'foo';
      $barUrlToken = 'bar';
      $bazUrlToken = 'baz';

      $fooNamespace = 'VENDOR\foo';
      $barNamespace = 'VENDOR\bar';
      $bazNamespace = 'VENDOR\baz';

      $fooName = 'say-foo';
      $barName = 'say-bar';
      $bazName = 'say-baz';

      $fooMapping = new ActionUrlMapping($fooUrlToken, $fooNamespace, $fooName);
      $barMapping = new ActionUrlMapping($barUrlToken, $barNamespace, $barName);
      $bazMapping = new ActionUrlMapping($bazUrlToken, $bazNamespace, $bazName);

      $fC->registerActionUrlMapping($fooMapping);
      $fC->registerActionUrlMapping($barMapping);
      $fC->registerActionUrlMapping($bazMapping);

      assertEquals([$fooUrlToken, $barUrlToken, $bazUrlToken], $fC->getActionUrlMappingTokens());
      assertCount(3, $fC->getActionUrlMappingTokens());

      assertEquals($fooMapping, $fC->getActionUrlMapping($fooUrlToken));
      assertEquals($fooMapping, $fC->getActionUrlMapping($fooNamespace, $fooName));

      assertEquals($barMapping, $fC->getActionUrlMapping($barUrlToken));
      assertEquals($barMapping, $fC->getActionUrlMapping($barNamespace, $barName));

      assertEquals($bazMapping, $fC->getActionUrlMapping($bazUrlToken));
      assertEquals($bazMapping, $fC->getActionUrlMapping($bazNamespace, $bazName));

      assertNull($fC->getActionUrlMapping('123456'));

   }

}

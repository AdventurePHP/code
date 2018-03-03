<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
namespace APF\tests\suites\tools\image;

use APF\tools\image\ImageManager;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ImageManagerTest extends TestCase {

   public function testEmptySource() {
      $this->expectException(InvalidArgumentException::class);
      ImageManager::resizeImage(null, 10, 10);
   }

   public function testEmptyTarget1() {
      $this->expectException(InvalidArgumentException::class);
      ImageManager::resizeImage('./test-image.png', 10, 10, null);
   }

   public function testEmptyTarget2() {
      $this->expectException(InvalidArgumentException::class);
      ImageManager::resizeImage('./test-image.png', 10, 10, '');
   }

   public function testImageResize() {

      $extension = 'png';
      $sourceFile = __DIR__ . '/wiki_logo.' . $extension;
      $targetFile = __DIR__ . '/wiki_logo_new.' . $extension;

      $width = 50;
      $height = 50;

      ImageManager::resizeImage($sourceFile, $width, $height, $targetFile);

      $attributes = ImageManager::getImageAttributes($targetFile);

      $this->assertEquals($width, $attributes ['width']);
      $this->assertEquals($height, $attributes ['height']);
      $this->assertEquals($extension, $attributes ['type']);

      unlink($targetFile);
   }
}

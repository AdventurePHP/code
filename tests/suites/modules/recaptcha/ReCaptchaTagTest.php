<?php
namespace APF\tests\suites\modules\recaptcha;

use APF\modules\recaptcha\pres\taglib\ReCaptchaTag;
use APF\tools\form\FormException;
use APF\tools\form\taglib\HtmlFormTag;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Tests the ReCaptchaTag.
 */
class ReCaptchaTagTest extends \PHPUnit_Framework_TestCase {

   public function testParameterCheck1() {
      $this->setExpectedException(FormException::class);
      $this->getReCaptchaTag()->onParseTime();
   }

   protected function getReCaptchaTag(array $params = []) {
      $form = new HtmlFormTag();
      $form->setAttribute('name', 'foo');

      $tag = new ReCaptchaTag();
      $tag->setParentObject($form);

      return $tag->setAttributes($params);
   }

   public function testParameterCheck2() {
      $this->setExpectedException(FormException::class);
      $this->getReCaptchaTag(['name' => 'foo'])->onParseTime();
   }

   public function testParameterCheck3() {
      $this->setExpectedException(FormException::class);
      $this->getReCaptchaTag(['name' => 'foo', 'public-key' => 'bar'])->onParseTime();
   }

   public function testParameterCheck4() {
      try {
         $this->getReCaptchaTag(['name' => 'foo', 'public-key' => 'bar', 'private-key' => 'baz'])->onParseTime();
      } catch (FormException $e) {
         $this->fail('All required attributes are set. There should be no issue!');
      }
   }

   public function testPrivateKey() {
      $this->assertEquals(
            'baz',
            $this->getReCaptchaTag(['private-key' => 'baz'])->getPrivateKey()
      );
   }

   public function testTransform() {

      /* @var $tag ReCaptchaTag|PHPUnit_Framework_MockObject_MockObject */
      $tag = $this->getMock(ReCaptchaTag::class, ['getCaptchaId', 'getPublicKey']);

      $captchaId = '12345678901234567890';

      $tag->expects($this->exactly(2))
            ->method('getCaptchaId')
            ->willReturn($captchaId);

      $publicKey = 'public key';
      $tag->expects($this->exactly(2))
            ->method('getPublicKey')
            ->willReturn($publicKey);

      // test w/ only mandatory attributes
      $actual = $tag->transform();

      $this->assertContains($captchaId, $actual);
      $this->assertContains('onload=ReCaptchaDisplay' . $captchaId, $actual);
      $this->assertContains('var ReCaptchaDisplay' . $captchaId . ' = function ()', $actual);
      $this->assertContains('\'g-recaptcha-' . $captchaId . '\'', $actual);
      $this->assertContains('\'sitekey\' : \'' . $publicKey . '\'', $actual);

      // test w/ all attributes
      $tag->setAttributes(['theme' => 'dark', 'tabindex' => '2', 'size' => 'compact']);

      $actual = $tag->transform();

      $this->assertContains($captchaId, $actual);
      $this->assertContains('onload=ReCaptchaDisplay' . $captchaId, $actual);
      $this->assertContains('var ReCaptchaDisplay' . $captchaId . ' = function ()', $actual);
      $this->assertContains('\'g-recaptcha-' . $captchaId . '\'', $actual);
      $this->assertContains('\'sitekey\' : \'' . $publicKey . '\'', $actual);
      $this->assertContains('\'theme\' : \'dark\'', $actual);
      $this->assertContains('\'tabindex\' : \'2\'', $actual);
      $this->assertContains('\'size\' : \'compact\'', $actual);

   }

}

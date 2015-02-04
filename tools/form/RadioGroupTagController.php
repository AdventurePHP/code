<?php
namespace APF\tools\form;

use APF\core\pagecontroller\BaseDocumentController;
use APF\tools\form\taglib\RadioGroupTag;

class RadioGroupTagController extends BaseDocumentController {

   public function transformContent() {

      $form = &$this->getForm('test');

      /* @var $groupFour RadioGroupTag */
      $groupFour = &$form->getFormElementByName('radio-group');
      $form->setPlaceHolder('radio-message', 'Group "radio-group" checked: ' . ($groupFour->isChecked() ? 'yes' : 'no'), true)
            ->setPlaceHolder('radio-message', '<br />Value: "' . $groupFour->getValue() . '"', true);

      // get single item via wrapper and plain
      $r2 = $form->getFormElementByID('r2');
      $form->setPlaceHolder('radio-message', '<br />r8 checked: ' . ($r2->isChecked() ? 'yes' : 'no'), true);

      $r3 = $groupFour->getButtonById('r3');
      $form->setPlaceHolder('radio-message', '<br />r9 checked: ' . ($r3->isChecked() ? 'yes' : 'no'), true);

      $form->transformOnPlace();

   }
}
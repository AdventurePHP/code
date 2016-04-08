<?php
namespace APF\core\frontcontroller;

use APF\core\pagecontroller\Page;
use APF\core\registry\Registry;

class PageGenerationAction extends AbstractFrontcontrollerAction {

   public function __construct() {
      $this->type = Action::TYPE_CREATE_CONTENT;
   }

   public function run() {

      // prepare response for page generation
      $response = $this->getResponse();
      $response->setContentType('text/html; charset=' . Registry::retrieve('APF\core', 'Charset'));

      // create new page
      $page = new Page();

      // set context
      $page->setContext($this->getContext());

      // set language
      $page->setLanguage($this->getLanguage());

      // load desired design
      $input = $this->getInput();
      $page->loadDesign($input->getParameter('namespace'), $input->getParameter('template'));

      // transform page (no pre_page_transform any more!)
      $response->setBody($page->transform());

   }

}

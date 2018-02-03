<?php
namespace APF\tools\form\taglib;

use APF\core\pagecontroller\ParserException;

/**
 * This tag allows to define re-usable form blocks. Using front-end frameworks such as Bootstrap, form markup
 * often contains boilerplate code which is hard to maintain in case it is distributed over the entire application.
 * <p/>
 * For this reason, the ReusableFormBlockTag lets you define form block skeletons via template and re-use it
 * across your application.
 * <p/>
 * Adding the tag to your form definition, it dynamically adapts the form block template code to the needs of the
 * respective use case (e.g. dynamically adapts form control names) to avoid redundant HTML and/or APF template code.
 * <p/>
 * Usage:
 * <code>
 * <form:block
 *     namespace="..."
 *     template="..."
 *     [block-abc=...
 *     block-xyz=...]
 * />
 * </code>
 * All attributes w/ prefix <em>block-</em> are injected into the template code defined for the current form block.
 * This means that the value of tag attribute <em>block-abc</em> is available as dynamic place holder <em>abc</em>
 * within the block template definition. This mechanism can be used to dynamically define form control names, labels,
 * and other form-related configuration.
 * <p/>
 *
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.02.2018<br />
 */
class ReusableFormBlockTag extends FormGroupTag {

   /**
    * @throws ParserException
    */
   public function onParseTime() {

      // Load template based on the namespace and template file definition.
      $namespace = $this->getRequiredAttribute('namespace');
      $template = $this->getRequiredAttribute('template');

      $templateFile = $this->getTemplateFilePath($namespace, $template);
      $html = file_get_contents($templateFile);

      // Inject all tag attributes starting w/ "block-" as dynamic place holders.
      // Example: block tag attribute "block-abc" is available as place holder "${abc}" within the template code.
      foreach ($this->getAttributes() as $key => $value) {
         if (strpos($key, 'block-') === 0) {
            $html = str_replace('${' . str_replace('block-', '', $key) . '}', $value, $html);
         }
      }

      $this->setContent($html);
      $this->extractTagLibTags();

   }

}

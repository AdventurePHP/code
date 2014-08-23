<?php
namespace APF\tools\form;

/**
 * Defines the interface for a form group element. Custom implementations
 * must use this interface to ensure recursive access is working.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.08.2014<br />
 */
interface FormElementGroup extends FormControlFinder, FormControl {

} 
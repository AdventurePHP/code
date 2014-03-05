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

/**
 *  TextLengthValidator for APFFormValidator
 *
 * Validates a form control to contain a string with a defined length.
 * The default min-value is three, to configure the length, please specify the
 * <em>minlength</em> and <em>maxlength</em> attribute within the target form
 * control definition.
 **/
(function ($) {
    $(document).ready(function () {
        jQuery.APFFormValidator.addClientValidator('TextLengthValidator', {
            validate:function (control, options) {
                var ctrlvalue = control.attr('value');
                var defaultOptions = {
                    maxlength:0,
                    minlength:3
                };
                var valiOptions = {};

                /* merge options*/
                $.extend(valiOptions, defaultOptions, options);

                /* the maxlength beeing null, the text may contain an infinite number of characters*/
                if (valiOptions.maxlength == 0) {
                    if (ctrlvalue != '' && ctrlvalue.length >= valiOptions.minlength) {
                        return true;
                    }
                }
                else {
                    if (ctrlvalue != '' && ctrlvalue.length >= valiOptions.minlength
                        && ctrlvalue.length <= valiOptions.maxlength) {
                        return true;
                    }
                }
                return false;
            }
        });
    });
})(jQuery);
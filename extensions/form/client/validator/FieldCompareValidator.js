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
 * FieldCompareValidator for APFFormValidator
 *
 * Implements a validator, that compares the content of two text fields. In
 * order to apply the validator, the form element that is added the validator
 * must specify the <em>ref</em> attribute to specify the reference field.
 **/
(function ($) {
    $(document).ready(function () {
        jQuery.APFFormValidator.addClientValidator('FieldCompareValidator', {
            validate: function (control, options) {
                var form = $(control).closest('form');
                var refField = $(form).find('input[name="' + options.ref + '"]');

                if ($(control).val() === refField.val()) {
                    $(refField).markAsValid();
                    return true;
                }
                $(refField).markAsInvalid();
                return false;
            }
        });
    });
})(jQuery);


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

/**
 * SimpleBirthdayValidator for APFFormValidator
 *
 * Validates a given form control to contain a syntactically correct
 * birthday date. Schema: dd.MM.YY
 **/
(function ($) {
    $(document).ready(function () {
        jQuery.APFFormValidator.addClientValidator('SimpleBirthdayValidator', {
            validate: function (control, options) {
                var ctrlvalue = control.attr('value');
                ctrlvalue = $.trim(ctrlvalue);

                var birthday = ctrlvalue.split('.');
                if (birthday.length !== 3) {
                    return false;
                }
                birthday[0] = parseInt(birthday[0], 10);
                birthday[1] = parseInt(birthday[1], 10) - 1;
                var controldate = new Date(birthday[2], birthday[1], birthday[0]);
                return !!((controldate.getDate() == birthday[0]) &&
                (controldate.getMonth() == birthday[1]) &&
                (controldate.getFullYear() == birthday[2]));

            }
        });
    });
})(jQuery);


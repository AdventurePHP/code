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
 * SimpleDateControlValidator for APFFormValidator
 *
 * Implements a simple date control validator. It expects the selected date to
 * be greater than today.
 **/
(function ($) {
    $(document).ready(function () {
        jQuery.APFFormValidator.addClientValidator('SimpleDateControlValidator', {
            validate:function (control, options) {
                var ctrlDay = $(control).find(':input[name="' + control.attr('id') + '\[Day\]"]')[0];
                var ctrlMonth = $(control).find(':input[name="' + control.attr('id') + '\[Month\]"]')[0];
                var ctrlYear = $(control).find(':input[name="' + control.attr('id') + '\[Year]"]')[0];

                var date = parseInt($(ctrlYear).val() + $(ctrlMonth).val() + $(ctrlDay).val());
                var todayDate = new Date();

                var todayDay = todayDate.getDate();
                todayDay = (todayDay < 10) ? '0' + todayDay : todayDay;

                var todayMonth = todayDate.getMonth() + 1;
                todayMonth = (todayMonth < 10) ? '0' + todayMonth : todayMonth;

                var todayYear = todayDate.getFullYear();

                var today = parseInt(todayYear + todayMonth + todayDay);
                if (today >= date) {
                    return false;
                }
                return true;
            }
        });
    });
})(jQuery);


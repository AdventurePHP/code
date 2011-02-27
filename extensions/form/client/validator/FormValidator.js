/**
 *  <!--
 *  This file is part of the adventure php framework (APF) published under
 *  http://adventure-php-framework.org.
 *
 *  The APF is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The APF is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
 *  -->
 */

/**
 * APFFormValidator is a jQuery plugin, which provides base functions for
 * validating forms on clientside.
 *
 * @author Ralf Schubert
 * @version
 * Version 1.0, 19.03.2010<br />
 **/
(function($){
    jQuery.APFFormValidator = {
        /*
        * Usages for following arrays and objects:
        * Validators: Contains the validators added to a special control.
        * ValidatorTypes: Contains all Clientvalidators, which have been loaded and could be used.
        * Valmarkerclasses: Contains the valmarkerclasses of each control .
        */
        Validators: [],
        ValidatorTypes: [],
        Valmarkerclasses: {},

        /* adds a client validator, so it can be used to validate controls */
        addClientValidator: function(name,validator){
            jQuery.APFFormValidator.ValidatorTypes[name] = validator;
            return $(this);
        },
        /*Adds a validator to the given control*/
        addValidator: function(buttonName, controlName, validatorType, validatorOptions){
            if(typeof jQuery.APFFormValidator.Validators[buttonName] == 'undefined'){
                jQuery.APFFormValidator.Validators[buttonName] = [];
            }
            if(typeof jQuery.APFFormValidator.Validators[buttonName][controlName] == 'undefined'){
                jQuery.APFFormValidator.Validators[buttonName][controlName] = [];
            }
            jQuery.APFFormValidator.Validators[buttonName][controlName][validatorType] = [];
            jQuery.APFFormValidator.Validators[buttonName][controlName][validatorType]['validatorOptions'] = validatorOptions;
            return $(this);
        },
        /* Adds a valmarkerclass to a single control */
        addValmarkerclass: function(controlName, valmarkerclass){
            jQuery.APFFormValidator.Valmarkerclasses[controlName] = valmarkerclass;
            return $(this);
        },
        /* Adds a complete set of valmarkerclasses. give an objekt like: {'fieldname':'classname','field2':'class'}*/
        addValmarkerclasses: function(valmarkerclasses){
            $.extend(jQuery.APFFormValidator.Valmarkerclasses, valmarkerclasses);
            return $(this);
        },
        /*Responsible for checking validation status of a whole form*/
        validate: function(buttonName){
            var isValid = true;
            for(var controlName in jQuery.APFFormValidator.Validators[buttonName]){
                var control = $(this).find(':input[name="'+ controlName +'"]');
                /*catch select fields with special selector:*/
                if(control.length === 0){
                    control = $(this).find(':input[name="'+ controlName +'\[\]"]');
                }
                /* Catch date-controls */
                if(control.length === 0){
                    control = $(this).find('span[id="'+controlName+'"]');
                }
                /*catch invalid validators (still didn't find a control):*/
                if(control.length === 0){
                    if(typeof(console) !== 'undefined'){
                        console.log("------!!! There's no control named '" + controlName + "' in the form!");
                    }
                    continue;
                }
                if(!$(this).validateControl(buttonName, control)){
                    isValid = false;
                }
            }
            if(!isValid){
                $(this).markAsInvalid();
                return false;
            }
            $(this).markAsValid();
            return true;
        },

        /* Responsible for checking validation status of a single form element*/
        validateControl: function(buttonName, control){
            var controlName = $(control).attr('name');
            /* We need a special check for date-controls, because we only can match the span around it */
            if(typeof(controlName) === 'undefined'){
                if((typeof(control[0]) !== 'undefined') && (control[0].tagName.toLowerCase() === 'span')){
                    controlName = $(control[0]).attr('id');
                }
            }
            /*delete [] from Name, if control is select, so we find the validators*/
            controlName = (controlName.substr(-2,2) === "[]")? controlName.substring(0,controlName.length-2): controlName;
            for(var validator in jQuery.APFFormValidator.Validators[buttonName][controlName]){
                if(typeof jQuery.APFFormValidator.ValidatorTypes[validator] != 'undefined'){
                    if(!jQuery.APFFormValidator.ValidatorTypes[validator].validate($(control), jQuery.APFFormValidator.Validators[buttonName][controlName][validator]['validatorOptions'])){
                        $(control).markAsInvalid();
                        return false;
                    }
                }
            }
            $(control).markAsValid();
            return true;
        },
        /* Marks an control as invalid and calls ValidationNotify event */
        markAsInvalid: function(){
            $(this).each(function(){
                switch(this.tagName.toLowerCase()){
                    case 'textarea':
                    case 'input':
                        /*delete [] from Name, if control is select, so we find the valmarkerclass*/
                        var controlName = $(this).attr('name')
                        controlName = (controlName.substr(-2,2) === "[]")? controlName.substring(0,controlName.length-2): controlName;
                        /*add valmarkerclass*/
                        var valmarker = (typeof(jQuery.APFFormValidator.Valmarkerclasses[controlName]) === 'undefined')? 'apf-form-error':jQuery.APFFormValidator.Valmarkerclasses[controlName];
                        $(this).addClass(valmarker);
                        break;
                    case 'span':
                        var spanName = $(this).attr('id');
                        var spanvalmarker = (typeof(jQuery.APFFormValidator.Valmarkerclasses[spanName]) === 'undefined')? 'apf-form-error':jQuery.APFFormValidator.Valmarkerclasses[spanName];
                        $(this).addClass(spanvalmarker);
                        break;
                }

                $(this).trigger('ValidationNotify', {
                    valid: false
                });
            });
            
            return $(this);
        },
        /* Marks an control as valid and calls ValidationNotify event */
        markAsValid: function(){
            $(this).each(function(){
                switch(this.tagName.toLowerCase()){
                    case 'textarea':
                    case 'input':
                        /*delete [] from Name, if control is select, so we find the valmarkerclass*/
                        var controlName = $(this).attr('name')
                        controlName = (controlName.substr(-2,2) === "[]")? controlName.substring(0,controlName.length-2): controlName;
                        /*add valmarkerclass*/
                        var valmarker = (typeof(jQuery.APFFormValidator.Valmarkerclasses[controlName]) === 'undefined')? 'apf-form-error':jQuery.APFFormValidator.Valmarkerclasses[controlName];
                        $(this).removeClass(valmarker);
                        break;
                    case 'span':
                        var spanName = $(this).attr('id');
                        var spanvalmarker = (typeof(jQuery.APFFormValidator.Valmarkerclasses[spanName]) === 'undefined')? 'apf-form-error':jQuery.APFFormValidator.Valmarkerclasses[spanName];
                        $(this).removeClass(spanvalmarker);
                        break;
                }
                
                $(this).trigger('ValidationNotify', {
                    valid: true
                });
            });
            return $(this);
        },
        /* Handles Events of clientlistener (shows or hides listener)*/
        handleClientListenerEvent: function(listener, param){
            listener = $(listener);

            var anprop = listener.data("animationproperties");
            var anopt = listener.data("animationoptions");
            
            if((listener.data("showState") === null) || (typeof(listener.data("showState")) === 'undefined')){
                listener.data("showState", false);
            }
            
            /* !! Prevent mysterious bug with "too much recursion" fired until window gets closed !! */
            var anpropClone = jQuery.extend(true, {}, anprop);
            var anoptClone = jQuery.extend(true, {}, anopt);
            
            if(param.valid === false){
                /* check if listener is already displayed*/
                if(listener.data("showState") === false){
                    listener.data("showState", true);
                    /* check if animation properties are not set */
                    if((anprop === null) || (typeof(anprop) === 'undefined')){
                        listener.removeClass("apf-form-clientlistener");
                        return null;
                    }
                    listener.animate(anpropClone, anoptClone);
                }
                return null;
            }
            /* Control is valid, check if we need to hide the listener */
            if(listener.data("showState") === true){
                listener.data("showState", false);

                /* check if animation properties are not set */
                if((anprop === null) || (typeof(anprop) === 'undefined')){
                    listener.addClass("apf-form-clientlistener");
                    return null;
                }
                listener.animate(anpropClone, anoptClone);
            }
            return null;
        }
    };
    jQuery.fn.addValidator = jQuery.APFFormValidator.addValidator;
    jQuery.fn.addValmarkerclass = jQuery.APFFormValidator.addValmarkerclass;
    jQuery.fn.addValmarkerclasses = jQuery.APFFormValidator.addValmarkerclasses;
    jQuery.fn.validate = jQuery.APFFormValidator.validate;
    jQuery.fn.validateControl = jQuery.APFFormValidator.validateControl;
    jQuery.fn.markAsInvalid = jQuery.APFFormValidator.markAsInvalid;
    jQuery.fn.markAsValid = jQuery.APFFormValidator.markAsValid;
})(jQuery);
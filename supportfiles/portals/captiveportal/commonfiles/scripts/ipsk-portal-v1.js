/*
Copyright (c) 2019 Cisco and/or its affiliates.

This software is licensed to you under the terms of the Cisco Sample
Code License, Version 1.1 (the "License"). You may obtain a copy of the
License at

			   https://developer.cisco.com/docs/licenses

All use of the material herein must be in accordance with the terms of
the License. All rights not expressly granted by the License are
reserved. Unless required by applicable law or agreed to separately in
writing, software distributed under the License is distributed on an "AS
IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express
or implied.

AUTHOR(s):	Gary Oppel (gaoppel@cisco.com)
			Hosuk Won (howon@cisco.com)
CONTRIBUTOR(s): Drew Betz (anbetz@cisco.com)
*/

function formFieldValidation(){
	var validationFailure;

	validationFailure = false;
	
	//Form Validation Object Loop
	$('.form-validation').each(function() {
		if($(this).attr('validation-state') == 'required'){
			if($(this).attr('validation-maximum-length')){
				//Check Field Max Length
				if($(this).val().length > $(this).attr('validation-maximum-length')){
					$(this).addClass('is-invalid');
					validationFailure = true;
				}else{
					$(this).removeClass('is-invalid');
					$(this).addClass('is-valid');
				}
				
				//Check Field Min Length
				if($(this).attr('validation-minimum-length')){
					if($(this).val().length < $(this).attr('validation-minimum-length')){
						$(this).addClass('is-invalid');
						validationFailure = true;
					}else{
						$(this).removeClass('is-invalid');
						$(this).addClass('is-valid');
					}
				}
			}else if($(this).attr('validation-minimum-length')){
				//Check Field Min Length
				if($(this).val().length < $(this).attr('validation-minimum-length')){
					$(this).addClass('is-invalid');
					validationFailure = true;
				}else{
					$(this).removeClass('is-invalid');
					$(this).addClass('is-valid');
				}
			}else{
				//Check Field not Empty
				if($(this).val() == ''){
					$(this).addClass('is-invalid');
					validationFailure = true;
				}else{
					$(this).removeClass('is-invalid');
					$(this).addClass('is-valid');
				}
			}
		}else if($(this).attr('validation-state') == 'minimum'){
			//Check Field not Empty
			if($(this).val() == ''){
				$(this).addClass('is-invalid');
				validationFailure = true;
			}else{
				$(this).removeClass('is-invalid');
				$(this).addClass('is-valid');
			}
		}else{
			$(this).removeClass('is-invalid');
			$(this).addClass('is-valid');
		}
	});
	
	return validationFailure;
}

function macAddressFormat(userInput) {
	//Load Existing Value, Remove ':', and convert to Uppercase
	var fieldContents = $(userInput).val().replace(/\:/g, "").toUpperCase();
	
	//Format value to comply with MAC Address Format
	var formatted = fieldContents.replace(/(.{2})/g, "$1:");
	
	//Output base on length to truncate excess ':'
	if(formatted.length >= 18){
		$(userInput).val(formatted.substring(0, 17));
	}else{
		$(userInput).val(formatted);
	}
}
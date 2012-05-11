/* javascript file used when clicking the button on the HTML editor */
// getting the "from" field form the serialized array
function tgsbFromAir(serializedFieldsArray) {
	// if the serialized array is not defined or it's length is 0 the return an empty string
	if(typeof(serializedFieldsArray) == 'undefined' || serializedFieldsArray.length == 0)
		return '';
	returnValue = '';
	jQuery.each(serializedFieldsArray, function(i, field){
		// checking for the "from" field and it's value in the serialized array
		if(field.name == 'tgsbFromAir') {
			// if the "from" field was found
			if(field.value != TG_Searchboxes_Variables.tgsbDefaultSettings.from_air) {
				// if it's value it's not equalto the default value set it as a return value
				returnValue = '"from_air":"'+field.value+'"';
				return;
			};
		};
   });
   return returnValue;
};
// getting the "to" field form the serialized array
function tgsbToAir(serializedFieldsArray) {
	// if the serialized array is not defined or it's length is 0 the return an empty string
	if(typeof(serializedFieldsArray) == 'undefined' || serializedFieldsArray.length == 0)
		return '';
	returnValue = '';
	jQuery.each(serializedFieldsArray, function(i, field){
		// checking for the "to" field and it's value in the serialized array
		if(field.name == 'tgsbToAir') {
			// if the "to" field was found
			if(field.value != TG_Searchboxes_Variables.tgsbDefaultSettings.to_air) {
				// if it's value it's not equal to the default value set it as a return value
				returnValue = '"to_air":"'+field.value+'"';
				return;
			};
		};
   });
   return returnValue;
};
// getting the "departure date" field form the serialized array
function tgsbDepartureDate(serializedFieldsArray) {
	// if the serialized array is not defined or it's length is 0 the return an empty string
	if(typeof(serializedFieldsArray) == 'undefined' || serializedFieldsArray.length == 0)
		return '';
	returnValue = '';
	jQuery.each(serializedFieldsArray, function(i, field){
		// checking for the "departure date" field and it's value in the serialized array
		if(field.name == 'tgsbDepartureDate') {
			// if the "departure date" field was found
			if(field.value != TG_Searchboxes_Variables.tgsbDefaultSettings.departure_date) {
				// if it's value it's not equal to the default value set it as a return value
				returnValue = '"departure_date":"'+field.value+'"';
				return;
			};
		};
   });
   return returnValue;
};
// getting the "return date" field form the serialized array
function tgsbReturnDate(serializedFieldsArray) {
	// if the serialized array is not defined or it's length is 0 the return an empty string
	if(typeof(serializedFieldsArray) == 'undefined' || serializedFieldsArray.length == 0)
		return '';
	returnValue = '';
	jQuery.each(serializedFieldsArray, function(i, field){
		// checking for the "return date" field and it's value in the serialized array
		if(field.name == 'tgsbReturnDate') {
			if(field.value != TG_Searchboxes_Variables.tgsbDefaultSettings.return_date) {
				// if it's value it's not equal to the default value set it as a return value
				returnValue = '"return_date":"'+field.value+'"';
				return;
			};
		};
   });
   return returnValue;
};
// getting the "adults" field form the serialized array
function tgsbAdults(serializedFieldsArray) {
	// if the serialized array is not defined or it's length is 0 the return an empty string
	if(typeof(serializedFieldsArray) == 'undefined' || serializedFieldsArray.length == 0)
		return '';
	returnValue = '';
	jQuery.each(serializedFieldsArray, function(i, field){
		// checking for the "adults" field and it's value in the serialized array
		if(field.name == 'tgsbAdults') {
			if(field.value != TG_Searchboxes_Variables.tgsbDefaultSettings.adults) {
				// if it's value it's not equal to the default value set it as a return value
				returnValue = '"adults":"'+field.value+'"';
				return;
			};
		};
   });
   return returnValue;
};

// getting the "kids" field form the serialized array
function tgsbKids(serializedFieldsArray) {
	// if the serialized array is not defined or it's length is 0 the return an empty string
	if(typeof(serializedFieldsArray) == 'undefined' || serializedFieldsArray.length == 0)
		return '';
	returnValue = '';
	jQuery.each(serializedFieldsArray, function(i, field){
		// checking for the "kids" field and it's value in the serialized array
		if(field.name == 'tgsbKids') {
			if(field.value != TG_Searchboxes_Variables.tgsbDefaultSettings.kids) {
				// if it's value it's not equal to the default value set it as a return value
				returnValue = '"kids":"'+field.value+'"';
				return;
			};
		};
   });
   return returnValue;
};

// getting the "seniors" field form the serialized array
function tgsbSeniors(serializedFieldsArray) {
	// if the serialized array is not defined or it's length is 0 the return an empty string
	if(typeof(serializedFieldsArray) == 'undefined' || serializedFieldsArray.length == 0)
		return '';
	returnValue = '';
	jQuery.each(serializedFieldsArray, function(i, field){
		// checking for the "seniors" field and it's value in the serialized array
		if(field.name == 'tgsbSeniors') {
			if(field.value != TG_Searchboxes_Variables.tgsbDefaultSettings.seniors) {
				// if it's value it's not equal to the default value set it as a return value
				returnValue = '"seniors":"'+field.value+'"';
				return;
			};
		};
   });
   return returnValue;
};

// getting the "rooms" field form the serialized array
function tgsbRooms(serializedFieldsArray) {
	// if the serialized array is not defined or it's length is 0 the return an empty string
	if(typeof(serializedFieldsArray) == 'undefined' || serializedFieldsArray.length == 0)
		return '';
	returnValue = '';
	jQuery.each(serializedFieldsArray, function(i, field){
		// checking for the "rooms" field and it's value in the serialized array
		if(field.name == 'tgsbRooms') {
			if(field.value != TG_Searchboxes_Variables.tgsbDefaultSettings.rooms) {
				// if it's value it's not equal to the default value set it as a return value
				returnValue = '"rooms":"'+field.value+'"';
				return;
			};
		};
   });
   return returnValue;
};

// getting the "roundtrip/oneway" field form the serialized array
function tgsbRTOW(serializedFieldsArray) {
	// if the serialized array is not defined or it's length is 0 the return an empty string
	if(typeof(serializedFieldsArray) == 'undefined' || serializedFieldsArray.length == 0)
		return '';
	returnValue = '';
	jQuery.each(serializedFieldsArray, function(i, field){
		// checking for the "rooms" field and it's value in the serialized array
		if(field.name == 'oneway') {
			field.value = (field.value == 'on') ? true : false;
			if(field.value != TG_Searchboxes_Variables.tgsbDefaultSettings.rtow) {
				// if it's value it's not equal to the default value set it as a return value
				returnValue = '"rtow":"'+field.value+'"';
				return;
			};
		};
   });
   return returnValue;
};

jQuery(document).ready(function(jQuery){
	var editor_toolbar=jQuery("#ed_toolbar");
	if(editor_toolbar){
		var theButton = document.createElement("input");
		theButton.type="button";
		theButton.value=TG_Searchboxes_Editor_Button.str_EditorButtonCaption;
		theButton.className="ed_button";
		theButton.title=TG_Searchboxes_Editor_Button.str_EditorButtonCaption;
		theButton.id="ed_button_tg_searchboxes";
		editor_toolbar.append(theButton);
		jQuery("#ed_button_tg_searchboxes").click(tg_searchboxes_button_click)
	};
	function tg_searchboxes_button_click(){
		var title = "Travelgrove Searchboxes";
		var url = TG_Searchboxes_Editor_Button.str_EditorButtonAjaxURL.replace(/\&amp\;/ig, '&');
		tb_show(title,url,false);
		jQuery("#TB_ajaxContent").width("auto").height("94.5%").click(function(event){
			var $target=jQuery(event.target);
			if($target.is("a.send_searchbox_to_editor") || $target.is('input.send_searchbox_to_editor')){
				var tgSearchboxMeasures = jQuery('ul.measuresChooser li a.current').text();
				tgSearchboxMeasures = (tgSearchboxMeasures.length == 0) ? '300x250' : tgSearchboxMeasures;
				var selectedTab = jQuery('.sb'+tgSearchboxMeasures+' .tg_searchbox .tg_container').find('form.sel').attr('class').match(/^(flights|hotels|cars|packages)/);
				var fields = jQuery('.sb'+tgSearchboxMeasures+' .tg_searchbox .tg_container form').serializeArray();				
				var optionsString = '';
				// getting the "from" value
				tgsb_fromAir = tgsbFromAir(fields);
				// adding the "from" value to the options string
				optionsString += (tgsb_fromAir.length) ? tgsb_fromAir+',' : tgsb_fromAir;
				// getting the "to" value
				tgsb_toAir = tgsbToAir(fields);
				// adding the "to" value to the options string
				optionsString += (tgsb_toAir.length) ? tgsb_toAir+',' : tgsb_toAir;
				// getting the "departure date" value
				tgsb_departureDate = tgsbDepartureDate(fields);
				// adding the "departure date" value to the options string
				optionsString += (tgsb_departureDate.length) ? tgsb_departureDate+',' : tgsb_departureDate;
				// getting the "return date" value
				tgsb_returnDate = tgsbReturnDate(fields);
				// adding the "return date" value to the options string
				optionsString += (tgsb_returnDate.length) ? tgsb_returnDate+',' : tgsb_returnDate;
				// getting the "adults" value
				tgsb_adults = tgsbAdults(fields);
				// adding the "adults" value to the options string
				optionsString += (tgsb_adults.length) ? tgsb_adults+',' : tgsb_adults;
				// getting the "kids" value
				tgsb_kids = tgsbKids(fields);
				// adding the "kids" value to the options string
				optionsString += (tgsb_kids.length) ? tgsb_kids+',' : tgsb_kids;
				// getting the "seniors" value
				tgsb_seniors = tgsbSeniors(fields);
				// adding the "seniors" value to the options string
				optionsString += (tgsb_seniors.length) ? tgsb_seniors+',' : tgsb_seniors;
				// getting the "rooms" value
				tgsb_rooms = tgsbRooms(fields);
				// adding the "seniors" value to the options string
				optionsString += (tgsb_rooms.length) ? tgsb_rooms+',' : tgsb_rooms;
				// getting the "roundtrip/oneway" value
				tgsb_rtow = tgsbRTOW(fields);
				// adding the "roundtrip/oneway" value to the options string
				optionsString += (tgsb_rtow.length) ? tgsb_rtow+',' : tgsb_rtow;
				// if the box measures is "300x250" then it is not needed to add the size as option because that is considered the default value
				optionsString += (tgSearchboxMeasures == '300x250') ? '' : '"size":"'+tgSearchboxMeasures+'",';
				// if the selected tab is null the set the selected tab to flights
				
				optionsString += (selectedTab == null) ? '"selectedTab":"flights"' : 
				// if the selectedTab is set and it's flights then do not set it in the options because the flights selected tab is considered by default
							((selectedTab[1] == 'flights' ) ? '' : 									'"selectedTab":"'+selectedTab[1]+'"'
							);
				if(optionsString.match(/\,$/)) {
					// if a comma is found on the end of the string then remove it
					optionsString = optionsString.replace(/\,$/, '');
				};
				// creating the shortcode string that will be added into the editor
				var tgSearchboxesShortcode = '[tg_searchboxes'+(optionsString.length ? ' options=\'{'+optionsString+'}\']' :']') ;
				send_to_editor(tgSearchboxesShortcode);
			};
			return false;
		});
		return false;
	};
});
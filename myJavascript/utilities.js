function checkNoResponseError(returnedJson) {
  if (returnedJson != "") {
    var jsonArray = jQuery.parseJSON(returnedJson);
    var dialogString = "";
    for (error in jsonArray) {
      dialogString += jsonArray[error] + "<br>";
    }
    myDialog(dialogString);
    return false;
  } else {
    return true;
  }      

}

function myDialog(dialogString) {

  // Now, this may only have one parameter in which case we display a simple OK box
  // or it might have a function and a bunch of parameters also passed in, in which case
  // we need a two button box where OK calls the function and passes in the parameters

  $("#dialog").html(dialogString);
  $("#dialog").dialog({
    draggable: false,
    modal: true,
    resizable: false,
    autoOpen: false
  });

  // Magic stuff to get the arguments list
  var args = Array.prototype.slice.call(arguments);
  if (args.length > 1) {
    var theFunction = args[1];
    var params = args.splice(2);
    
    $("#dialog").dialog( "option", "buttons", [{
      text: "OK",
      click: function() {
        // Call function by name
        theFunction.apply(window, (params));
        $(this).dialog("close");
      }
    },
    {
      text: "Cancel",
      click: function() { $(this).dialog("close"); }
    }]);    
  } else {
    $("#dialog").dialog( "option", "buttons", [{
      text: "OK",
      click: function() { $(this).dialog("close"); }
    }]);
  }

  $("#dialog").dialog("open");


}

function fillFormFromJson(formId, json)
{
  // Trun the JSON into an array
  var jsonArray= jQuery.parseJSON(json)[0];

  // Iterate through the collection of every 'input' element under the named form
  // For each one, if there is an entry in the json array for the id attribute of that 'input' element
  // then set the value of that 'input' element to the value of the json array for that same 'key'

  $(formId + ' :input').each(function(index, element) {
    if (element.id in jsonArray) {
      element.value = jsonArray[element.id];
    }
  });
}

function dump(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	
	if(typeof(arr) == 'object') { //Array/Hashes/Objects 
		for(var item in arr) {
			var value = arr[item];
			
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}
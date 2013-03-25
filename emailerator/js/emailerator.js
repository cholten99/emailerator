// jQuerys onload function
$(function() {
  // http://trentrichardson.com/examples/timepicker/
  $("#EventDateTime").datetimepicker({ dateFormat: "dd-mm-yy", ampm: true });
  var now = new Date();
  $('#EventDateTime').datetimepicker('setDate', now);
  
  // http://harvesthq.github.com/chosen/
  $(".chzn-select").chosen();

});

// Call the passed in URL as an ajax function and fill out named form with the response
function updateFormFromSelectAjax(ajaxURL, formId) {

  $.ajax({url:ajaxURL, 
    success:function(formResult) {
      fillFormFromJson(formId, formResult);
      var jsonArray = jQuery.parseJSON(formResult);

      // Update date field, if it exists
      if ('EventDateTime' in jsonArray[0]) {
        var fetchedMiliseconds = jsonArray[0]['EventDateTime'];
        var fetchedDate = new Date(parseInt(fetchedMiliseconds));
        $('#EventDateTime').datetimepicker('setDate', fetchedDate);
      }

      // Handle reloading the multiselect - including 'selected' for this event
      if ('MultiSelect' in jsonArray[0]) {
        var multiSelectString = decodeURIComponent(jsonArray[0]['MultiSelect']);
        reloadSelectorAjax("#eventForm", "EmaileratorUser", "#MultipleSelect", multiSelectString);
      }
    },
    error:function(jqXHR, textStatus, errorThrown) {
      myDialog("Failed AJAX call from updateFormFromSelectAjax.\nStatus is " + textStatus + ".\nError thrown is " + errorThrown + ".");
    } 
  }); 

}

// Update one of the log display divs filtered by one of the log DB fields
function updateLogDivAjax(divName) {

  // By default we get all of the first 50
  var ajaxURL = "../myPHP/classHandler.php?c=EmaileratorLog&a=Read&from=1&num=20&reverseSort=Timestamp";

  // However, if optional 'field' and 'value' filter parameters are passed in we add them too the ajax URL
  // 'arguments' is the automagically created object that contains all parameters passed to the function (very nice!)
  // However, before we can use it we have to use some hackery to turn it into a real array (just dont ask me how the next line works)
  
  var args = Array.prototype.slice.call(arguments);
  if (args.length > 1) {
    ajaxURL += "&field=" + args[1] + "&" + args[1] + "=" + args[2];
  }

  $.ajax({url:ajaxURL, 
    success:function(logResult) {    
      var divText = "";
      var jsonArray = jQuery.parseJSON(logResult);
      for (index in jsonArray) {
        divText += jsonArray[index].Text + "<br/>";
      }
      $(divName).html(divText);
    },
    error:function(jqXHR, textStatus, errorThrown) {
      myDialog("Failed AJAX call from updateLogDivAjax.\nStatus is " + textStatus + ".\nError thrown is " + errorThrown + ".");
    }
  });

}

// Handle select change for forms, fills in using ajax, updates associated log div and changes buttons as appropriate
function selectNameFormAutoChange() {

  var object = $("#nameFormSelect");
  var value = object[0].value;
  var ajaxURL = "../myPHP/classHandler.php?c=EmaileratorUser&a=Read&field=id&id=" + value;
  var parentForm = $("#nameForm");

  if (value == "new") {
    resetNameForm();
  }
  else {
    updateFormFromSelectAjax(ajaxURL, "#nameForm");
    updateLogDivAjax("#nameLogDiv", "UserID", value);
    updateLogDivAjax("#generalLogDiv");
    parentForm.find("#GoButton").html("Update");
    parentForm.find("#DeleteButton").css("visibility", "visible");
  }

}

// Handle select change for magic forms, fills in using ajax, updates associated log div and changes buttons as appropriate
function selectEventFormAutoChange() {

  var object = $("#eventFormSelect");
  var value = object[0].value;
  var ajaxURL = "../myPHP/classHandler.php?c=EmaileratorEvent&a=Read&MultiSelect=true&field=id&id=" + value;
  var parentForm = $("#eventForm");

  if (value == "new") {
    resetEventForm();
  }
  else {  
    updateFormFromSelectAjax(ajaxURL, "#eventForm");
    updateLogDivAjax("#eventLogDiv", "EventID", value);
    updateLogDivAjax("#generalLogDiv");
    parentForm.find("#GoButton").html("Update");
    parentForm.find("#DeleteButton").css("visibility", "visible");
  }

}

function selectNameFormGoButtonPressAjax() {
  var ajaxURL = '../myPHP/classHandler.php?c=EmaileratorUser&a=';
  var create = true;
  var id = $('#nameForm').find("#ID").val();

  if ($('#nameForm').find('#GoButton').html() == 'Create') {
    ajaxURL += 'Create';
  } else {
    ajaxURL += "UpdateByID&id=" + id;
    create = false;
  }

  $('#nameForm').find("input").each( function(index) {
    if (this.id != "ID") {
      ajaxURL += "&" + this.id + "=" + encodeURIComponent(this.value);
    }
  });

  $.ajax({url:ajaxURL, 
    success:function(goResult) {   
      if (checkNoResponseError(goResult)) {

        if (create) {
          // Fake updating the log on screen as we don't have the user ID to get since it's just been auto-created server-side
          var logUpdateString = $("#nameForm").find("#Name").val() + " has been added to the system and emailed an introduction.";
          resetNameForm();
          $("#nameLogDiv").html(logUpdateString);
        } else {
          var id = $("#nameForm").find("#ID").val();
          resetNameForm();
          updateLogDivAjax("#nameLogDiv", "UserID", id);
        }

        // Also reset the event form as it may contain unsubmitted info that now contains incorrect user data
        resetEventForm();
      }
    },
    error:function(jqXHR, textStatus, errorThrown) {
      myDialog("Failed AJAX call from selectFormGoButtonPressAjax.\nStatus is " + textStatus + ".\nError thrown is " + errorThrown + ".");
    }
  });
  
}

function selectEventFormGoButtonPressAjax() {
  var ajaxURL = "../myPHP/classHandler.php?c=EmaileratorEvent&a=";
  var create = true;

  var id = $("#eventForm").find("#ID").val();
  if ($("#eventForm").find("#GoButton").html() == "Create") {
    ajaxURL += "Create";
  } else {
    ajaxURL += "UpdateByID&id=" + id;
    create = false;
  }

  $("#eventForm").find("input").each( function(index) {  
    if ((this.id != "ID") && (this.id != "") && (this.id != "EventDateTime")) {
      ajaxURL += "&" + this.id + "=" + encodeURIComponent(this.value);
    }
  });

  var miliseconds = Date.parse($("#EventDateTime").datetimepicker('getDate'));
  ajaxURL += "&EventDateTime=" + miliseconds;
  ajaxURL += "&Description=" + encodeURIComponent($("#Description").val());
  var selectString = "";

  $("#MultipleSelect option:selected").each(function(index){ selectString += this.value.toString() + ","; });
  selectString = selectString.substr(0, (selectString.length - 1));
  ajaxURL += "&MultipleSelect=" + encodeURIComponent(selectString);

  $.ajax({url:ajaxURL, 
    success:function(goResult) {

      if (checkNoResponseError(goResult)) {
        if (create) {
          // Fake updating the log on screen as we don't have the user ID to get since it's just been auto-created server-side
          var eventName = $("#eventForm").find("#Name").val()
          var logUpdateString = eventName + " has been added to the system.<br>";
       
          // Also need to fake inviting each person
          $("#MultipleSelect option:selected").each( function(index) {
            logUpdateString += this.text + " has been invited to " + eventName + ".<br>";
          });
          
          resetEventForm();
          $("#eventLogDiv").html(logUpdateString);
        } else {
          var id = $("#eventForm").find("#ID").val();
          resetEventForm();
          updateLogDivAjax("#eventLogDiv", "EventID", id);
        }

      }
      
    },
    error:function(jqXHR, textStatus, errorThrown) {
      myDialog("Failed AJAX call from selectFormGoButtonPressAjax.\nStatus is " + textStatus + ".\nError thrown is " + errorThrown + ".");
    }
  });

}

function selectNameFormDeleteButtonPress() {
  dialogString = "Confirm remove all evidence of " + $("#nameForm").find("#Name").val() + " (can be re-added later).";
  // Note that this is passing in the /function itself/ as the second parameter!
  myDialog(dialogString, performDeleteByIdAjax, $("#nameForm").find("#ID").val(), "EmaileratorUser");
}

function selectEventFormDeleteButtonPress(formId, className, logDiv, selectId) {
  dialogString = "Confirm remove all evidence of " + $("#eventForm").find("#Name").val() + " (can be re-added later).";
  // Note that this is passing in the /function itself/ as the second parameter!
  myDialog(dialogString, performDeleteByIdAjax, $("#eventForm").find("#ID").val(), "EmaileratorEvent");
}

function performDeleteByIdAjax(id, className) {

  var ajaxURL = "../myPHP/classHandler.php?c=" + className +"&a=DeleteByID&id=" + id;
  $.ajax({url:ajaxURL, 
    success:function(deleteResult) {   
      resetEventForm();
      if (className == "EmaileratorUser") {
        resetNameForm();
      }
    },
    error:function(jqXHR, textStatus, errorThrown) {
      myDialog("Failed AJAX call from performDeleteUserAjax.\nStatus is " + textStatus + ".\nError thrown is " + errorThrown + ".");
    }
  });

}

function reloadSelectorAjax(formId, className, selectId, selectedString) {
  var ajaxURL = "../myPHP/classHandler.php?c=" + className + "&a=Read&sort=Name";
  var selectText = "";

  if (selectedString != "") {
    var splitArray = selectedString.split(";");
    var yesArray = splitArray[0].split(",");
    var noArray = splitArray[1].split(",");
    var unknownArray = splitArray[2].split(",");
    var combinedArray = yesArray.concat(noArray);
    combinedArray = combinedArray.concat(unknownArray);
  }

  if (selectId != "#MultipleSelect") {
    selectText += "<option value='new'>New</option>";
  }
  
  $.ajax({url:ajaxURL, 
    success:function(selectResult) {
      var jsonArray = jQuery.parseJSON(selectResult);

      // For colours
      var count=0;
      var blueArray = new Array();
      var tomatoArray = new Array();
      var purpleArray = new Array();

      for (index in jsonArray) {
        selectText += "<option ";
        testFlag = false;

        if (selectId == "#MultipleSelect") {

          for (key in yesArray) {
            if (yesArray[key] == jsonArray[index].ID) {
              blueArray.push(count); 
              testFlag = true;
            }
          }
        
          for (key in noArray) {
            if (noArray[key] == jsonArray[index].ID) {
              tomatoArray.push(count);
              testFlag = true;
            }
          }

          if (testFlag == false) {
            purpleArray.push(count);
          }

        }

        for (key in combinedArray) {
          if (combinedArray [key] == jsonArray[index].ID) {
            selectText += "selected data-removable='0' ";
          }
        }

        selectText += "value='" + jsonArray[index].ID + "'>" + jsonArray[index].Name + "</option>";
        count++;

      }
      
      $(selectId).html(selectText);
      $(selectId).trigger("liszt:updated");

      if (selectId == "#MultipleSelect") {

        // Set colours
        for (key in blueArray) {
          $('#MultipleSelect_chzn_c_' + blueArray[key]).css("color", "blue");
        }

        for (key in tomatoArray) {
          $('#MultipleSelect_chzn_c_' + tomatoArray[key]).css("color", "tomato");
        }

        for (key in purpleArray) {
          $('#MultipleSelect_chzn_c_' + purpleArray[key]).css("color", "purple");
        }

      }
      
    },
    error:function(jqXHR, textStatus, errorThrown) {
      myDialog("Failed AJAX call from reloadSelectorAjax.\nStatus is " + textStatus + ".\nError thrown is " + errorThrown + ".");
    }
  });
}

function resetNameForm() {
  $("#nameForm")[0].reset();
  $("#nameForm").find("#GoButton").html("Create");
  $("#nameForm").find("#DeleteButton").css("visibility", "hidden");  

  $("#nameLogDiv").html("");
  updateLogDivAjax("#generalLogDiv");

  // Reload selector
  reloadSelectorAjax('#nameForm', 'EmaileratorUser', '#nameFormSelect', '');
}

function resetEventForm() {
  $("#eventForm")[0].reset();
  $("#eventForm").find("#GoButton").html("Create");
  $("#eventForm").find("#DeleteButton").css("visibility", "hidden"); 

  var now = new Date();
  $('#EventDateTime').datetimepicker('setDate', now);
  $("#eventLogDiv").html("");
  updateLogDivAjax("#generalLogDiv");

  // Reload selectors
  reloadSelectorAjax('#eventForm', 'EmaileratorEvent', '#eventFormSelect', '');
  reloadSelectorAjax('#eventForm', 'EmaileratorUser', '#MultipleSelect', '');
}
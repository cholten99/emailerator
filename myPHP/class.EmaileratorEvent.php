<?php

class EmaileratorEvent extends MyCrud {

  function __construct() {
    parent::__construct("bowsy_emailerator", "Event");
  }
 
  public function DeleteByID($argsArray) {

    // Overwriting the baseclass function as we also need to 
    // remove all evidence of the event from 'Attending' DB  and update the log
  
    // Get the event's name /before/ we delete it
    $eventQueryString = "SELECT * FROM Event WHERE ID=" . $argsArray['id'];
    $eventResult = $this->dbHandle->query($eventQueryString);
    $eventArray = $eventResult->fetch_assoc();
    $eventName = $eventArray['Name'];
    mysqli_free_result($eventResult);
 
    // Call the baseclass function
    parent::DeleteByID($argsArray);
    
    // Email everyone who was going (or potentially going) to let them know
    $template = "../emailerator/template.EmaileratorCancel.php";

    $attendingQueryString = "SELECT * FROM Attending WHERE EventID=" . $argsArray['id'];
    $attendingResult = $this->dbHandle->query($attendingQueryString);

    while ($attendingRow = $attendingResult->fetch_object()) {

      $attending = $attendingRow->Attending;
      if (($attending == "Yes") || ($attending == "Unknown")) {
      
        $userQueryString = "SELECT * FROM User WHERE ID=" . $attendingRow->UserID;
        $userResult = $this->dbHandle->query($userQueryString);
        $userArray = $userResult->fetch_assoc();
        mysqli_free_result($userResult);

        $userName = $userArray['Name'];
        $firstName = substr($userName, 0, strpos($userName, " "));
        $miliseconds = $eventArray['EventDateTime'];
        $dateString = date('D, jS M Y \a\t g:iA', $miliseconds / 1000);

        $otherBitsArray = array("FirstName" => $firstName, "UserID" => $attendingRow->UserID, "DateTime" => $dateString);

        $cancelArgsArray = array_merge($eventArray, $otherBitsArray);

        sendOneEmail($userArray['Email'], $userArray['Name'], "Event cancellation notice!", $template, $cancelArgsArray);
      }

    }
    mysqli_free_result($attendingResult);

    // Then remove entries from Attending
    $this->dbHandle->query("DELETE FROM Attending WHERE EventID=" . $argsArray['id']);
    
    // Update the log    
    $updateLogArray = array("c" => "EmaileratorLog", 
                            "a" => "Create", 
                            "UserID" => 0, 
                            "EventID" => $argsArray['id'],
                            "Text" => "All evidence of " . $eventName . " was eliminated from the system!");

    // doClassAction is a function in classHandler.php
    doClassAction($updateLogArray); 

  }

  public function Create($argsArray) {

    $errorString = $this->validateCreate($argsArray);

    if ($errorString != "") {
      return $errorString;
    }
    
    // Multiselect part one - need to remove it from argsArray otherwise parent::Create won't work
    $multiselect = $argsArray['MultipleSelect'];
    unset($argsArray['MultipleSelect']);
    
    // Call the baseclass and get the auto-update ID
    $id = parent::Create($argsArray);
    
    // Update log
    $updateLogArray = array("c" => "EmaileratorLog", 
                            "a" => "Create", 
                            "UserID" => 0, 
                            "EventID" => $id,
                            "Text" => $argsArray['Name'] . " has been added to the system.");

    // doClassAction is a function in classHandler.php
    doClassAction($updateLogArray);

    // Multiselect part two - but then we need to returned auto-increment ID before we can add into Attending
    $attendingArray = explode(",", $multiselect);

    foreach($attendingArray as $userID) {
      $attendingArgsArray = array("c" => "EmaileratorAttending", 
                                  "a" => "Create", 
                                  "UserID" => $userID, 
                                  "EventID" => $id, 
                                  "Attending" => "Unknown", 
                                  "EventName" => $argsArray['Name'],
                                  "EventDateTime" => $argsArray['EventDateTime'],
                                  "Location" => $argsArray['Location'],
                                  "Description" => $argsArray['Description']);

      // doClassAction is a function in classHandler.php
      doClassAction($attendingArgsArray);
    }

  }

  // Overwriting the baseclass read function to add the contents of the appropriate Attending entries to the response if required
  public function Read($argsArray) {

    // Get the info from the baseclass
    $response = parent::Read($argsArray);

    // Get the required info from Attending
    if (array_key_exists("MultiSelect", $argsArray)) {

      // Handle yes
      $yesQueryString = "SELECT * FROM Attending WHERE EventID=" . $argsArray['id'] . " AND Attending='Yes'";    
      $yesResult = $this->dbHandle->query($yesQueryString);

      $yesArray = array();
      while ($yesRow = $yesResult ->fetch_assoc()) {
        $yesArray [] = $yesRow ['UserID'];
      }
      mysqli_free_result($yesResult);
      $yesString = implode(",", $yesArray );

      // Handle no
      $noQueryString = "SELECT * FROM Attending WHERE EventID=" . $argsArray['id'] . " AND Attending='No'";    
      $noResult = $this->dbHandle->query($noQueryString);

      $noArray = array();
      while ($noRow = $noResult ->fetch_assoc()) {
        $noArray [] = $noRow ['UserID'];
      }
      mysqli_free_result($noResult);
      $noString = implode(",", $noArray );

      // Handle unknown
      $unknownQueryString = "SELECT * FROM Attending WHERE EventID=" . $argsArray['id'] . " AND Attending='Unknown'";    
      $unknownResult = $this->dbHandle->query($unknownQueryString);

      $unknownArray = array();
      while ($unknownRow = $unknownResult ->fetch_assoc()) {
        $unknownArray [] = $unknownRow ['UserID'];
      }
      mysqli_free_result($unknownResult);
      $unknownString = implode(",", $unknownArray );

      $attendingString = $yesString . ";" . $noString . ";" . $unknownString;

      $responseArray = json_decode($response, true);
      $responseArray[0]['MultiSelect'] = urlencode($attendingString);

      $response = json_encode($responseArray);
    }

    return($response);

  }

  public function UpdateByID($argsArray) {

    // Validate incomging data
    $errorString = $this->validateUpdate($argsArray);
    if ($errorString != "") {
      return $errorString;
    }
    
    // Multiselect part one - need to remove it from argsArray otherwise parent::updateByID won't work
    $multiselect = $argsArray['MultipleSelect'];
    unset($argsArray['MultipleSelect']);
    $attendingArray = explode(",", $multiselect);
    
    // If there's been a change to EventDateTime, Location or Description we need to reset existing invitees
    $originalString = "SELECT * FROM Event WHERE id=" . $argsArray['id'];
    $originalResult = $this->dbHandle->query($originalString);
    $originalRow = $originalResult->fetch_assoc();

    // First get all the existing attendees
    $originalAttendingString = "SELECT * FROM Attending WHERE EventID=" . $argsArray['id'];
    $originalAttendingResult = $this->dbHandle->query($originalAttendingString);

    // Flag to update log if there's been a name, event, date or description update
    $logFlag = false;

    // Loop through them all
    while ($originalAttendingRow = $originalAttendingResult->fetch_assoc()) {

    // If something other than just a new person being added has changed
      if ( ($originalRow['Name'] != $argsArray['Name']) ||
         ($originalRow['EventDateTime'] != $argsArray['EventDateTime']) ||
         ($originalRow['Location'] != $argsArray['Location']) ||
         ($originalRow['Description'] != $argsArray['Description']) ) {

        $logFlag = true;

        $attendingArgsArray = array("c" => "EmaileratorAttending", 
                                    "a" => "EventChanged", 
                                    "UserID" => $originalAttendingRow['UserID'],
                                    "EventID" => $argsArray['id'], 
                                    "EventName" => $argsArray['Name'],
                                    "EventDateTime" => $argsArray['EventDateTime'],
                                    "Location" => $argsArray['Location'],
                                    "Description" => $argsArray['Description']);

        // doClassAction is a function in classHandler.php
        doClassAction($attendingArgsArray);

      }      

      // Either way, remove updated user from list that needs to be processed by 'create'
      $attendingArray = array_diff($attendingArray, array($originalAttendingRow['UserID']));

    }

    if (logFlag) {

      // Update log
      $dateString = date('D, jS M Y \a\t g:iA', $argsArray['EventDateTime'] / 1000);
        
      $updateLogArray = array("c" => "EmaileratorLog", 
                              "a" => "Create", 
                              "UserID" => 0, 
                              "EventID" => $argsArray['id'],
                              "Text" => "Event update: " . $argsArray['Name'] . ", " . $argsArray['Location'] . ", " . $dateString . ", " . $argsArray['Description']);

      // doClassAction is a function in classHandler.php
      doClassAction($updateLogArray); 

    }

    // Call the baseclass function
    parent::UpdateByID($argsArray);  

    // Multiselect part two - but then we need the returned auto-increment ID before we can add new folks into Attending
    foreach($attendingArray as $userID) {

      $attendingArgsArray = array("c" => "EmaileratorAttending", 
                                  "a" => "Create", 
                                  "UserID" => $userID, 
                                  "EventID" => $argsArray['id'], 
                                  "Attending" => "Unknown", 
                                  "EventName" => $argsArray['Name'],
                                  "EventDateTime" => $argsArray['EventDateTime'],
                                  "Location" => $argsArray['Location'],
                                  "Description" => $argsArray['Description']);

      // doClassAction is a function in classHandler.php
      doClassAction($attendingArgsArray);
    }
 
  }

  private function validateCreate($argsArray) {

    $errorsJson = $this->baseValidate($argsArray);

    $jsonArray = array();
        
    // Check if name already used
    $nameQueryString = "SELECT * FROM Event WHERE Name='" . addslashes($argsArray['Name']) . "'";    
    $nameResult = $this->dbHandle->query($nameQueryString);
    if (mysqli_num_rows($nameResult) != 0) {
      if ($errorsJson != "") {      
        $tmpArray = json_decode($errorsJson);
        $jsonArray = array_merge($jsonArray, $tmpArray);
      }
      array_push($jsonArray, "Event name already in system.");
      $errorsJson = json_encode($jsonArray);
      unset($jsonArray);
    }
    mysqli_free_result($nameResult);

    $jsonArray = array();

    // Check if the date and time picked is in the past
    if ($argsArray['EventDateTime'] < (time() * 1000)) {
      if ($errorsJson != "") {      
        $tmpArray = json_decode($errorsJson);
        $jsonArray = array_merge($jsonArray, $tmpArray);
      }
      array_push($jsonArray, "Event is in the past.");
      $errorsJson = json_encode($jsonArray);
      unset($jsonArray);
    }

    $jsonArray = array();

    // Make sure at least one person is selected to attend
    if ($argsArray['MultipleSelect'] == "") {
      if ($errorsJson != "") {    
        $tmpArray = json_decode($errorsJson);
        $jsonArray = array_merge($jsonArray, $tmpArray);
      }
      array_push($jsonArray, "Please select at least one person to attend.");
      $errorsJson = json_encode($jsonArray);
      unset($jsonArray);
    }

    $jsonArray = array();

    return($errorsJson);

  }

  private function validateUpdate($argsArray) {

    // Cache the current value for later cparison
    $cacheString = "SELECT * FROM Event WHERE ID=" . $argsArray['id'];
    $cacheResult = $this->dbHandle->query($cacheString );
    $cacheRow = $cacheResult->fetch_assoc();

    $errorsJson = $this->baseValidate($argsArray);
    $jsonArray = array();
        
    // Check if name already used
    if ($cacheRow['Name'] != $argsArray['Name']) {
      $nameQueryString = "SELECT * FROM Event WHERE Name='" . addslashes($argsArray['Name']) . "'";    
      $nameResult = $this->dbHandle->query($nameQueryString);
      if (mysqli_num_rows($nameResult) != 0) {
        if ($errorsJson != "") {      
          $tmpArray = json_decode($errorsJson);
          $jsonArray = array_merge($jsonArray, $tmpArray);
        }
        array_push($jsonArray, "Event name already in system.");
        $errorsJson = json_encode($jsonArray);
        unset($jsonArray);
      }
      mysqli_free_result($nameResult);
      $jsonArray = array();
    }

    // Check if the date and time picked is in the past
    if ($cacheRow['EventDateTime'] != $argsArray['EventDateTime']) {
      if ($argsArray['EventDateTime'] < (time() * 1000)) {
        if ($errorsJson != "") {      
          $tmpArray = json_decode($errorsJson);
          $jsonArray = array_merge($jsonArray, $tmpArray);
        }
        array_push($jsonArray, "Event is in the past.");
        $errorsJson = json_encode($jsonArray);
        unset($jsonArray);
      }
      $jsonArray = array();
    }

    return($errorsJson);

  }

  private function baseValidate($argsArray) {  
    $rules = array();
    $rules[] = "required,Name,Please enter an event name.";
    $rules[] = "required,Location,Please enter an event location.";
    $rules[] = "required,Description,Please enter an event description.";

    $errors = validateFields($argsArray, $rules);

    if (!empty($errors))
    {
      return(json_encode($errors));
    } else {
      return("");
    }
  }

}

?>
<?php

class EmaileratorUser extends MyCrud {

  function __construct() {
    parent::__construct("bowsy_emailerator", "User");
  }

  public function DeleteByID($argsArray) {
  
    // Overwriting the baseclass function as we also need to 
    // remove all evidence of the person from 'Attending' DB  and update the log
  
    // Get the person's name /before/ we delete them!
    $nameQueryString = "SELECT * FROM User WHERE ID=" . $argsArray['id'];
    $nameResult = $this->dbHandle->query($nameQueryString);
    $nameRow = $nameResult->fetch_object();
    $name = $nameRow->Name;
    mysqli_free_result($nameResult);
  
    // Call the baseclass function
    parent::DeleteByID($argsArray);
    
    // Then remove entries from Attending
    $this->dbHandle->query("DELETE FROM Attending WHERE UserID=" . $argsArray['id']);
    
    // Update the log
    $updateLogString = "All evidence of " . $name . " was eliminated from the system";
    if (array_key_exists("DontStalkMe", $argsArray)) {
      // Change the log string
      $updateLogString .= " via a DontStalkMe request";
      
      // But also, just in this use case, return something for the user to see
      print "<h2>Unstalkulate!</h2>" . $name . ", the Great and Powerful Emailerator has removed you from the system and wishes you a good day!<p/>";
      
    }
    $updateLogString .= "!";

    $updateLogArray = array("c" => "EmaileratorLog", 
                            "a" => "Create", 
                            "UserID" => $argsArray['id'], 
                            "EventID" => 0,
                            "Text" => $updateLogString);

    // doClassAction is a function in classHandler.php
    doClassAction($updateLogArray); 

  }

  public function Create($argsArray) {

    $errorString = $this->validateCreate($argsArray);

    if ($errorString != "") {
      return $errorString;
    }

    // Call the baseclass and get the auto-update ID
    $id = parent::Create($argsArray);
  
    // Update log        
    $updateLogArray = array("c" => "EmaileratorLog", 
                            "a" => "Create", 
                            "UserID" => $id, 
                            "EventID" => 0,
                            "Text" => $argsArray['Name'] . " has been added to the system and emailed an introduction.");

    // doClassAction is a function in classHandler.php
    doClassAction($updateLogArray); 

    // Send the welcome email
    $template = "../emailerator/template.EmaileratorJoin.php";
    $name = $argsArray['Name'];
    $firstName = substr($name, 0, strpos($name, " "));
    $emailArgsArray = array("ID" => $id, "FirstName" => $firstName);    
    sendOneEmail($argsArray['Email'], $name, "Welcome to Annie and Dave's Emailerator!", $template, $emailArgsArray);

  }

  public function UpdateByID($argsArray) {
  
    // Validate incomging data
    $errorString = $this->validateUpdate($argsArray);

    if ($errorString != "") {
      return $errorString;
    }
  
    // Call the baseclass function
    parent::UpdateByID($argsArray);  
  
    // Update log
    $updateLogArray = array("c" => "EmaileratorLog", 
                            "a" => "Create", 
                            "UserID" => $argsArray['id'],
                            "EventID" => 0,
                            "Text" => "User update: " . $argsArray['Name'] . ", " . $argsArray['Email'] . ".");

    // doClassAction is a function in classHandler.php
    doClassAction($updateLogArray); 
  
  }

  private function validateCreate($argsArray) {

    // Check the basics
    $errorsJson = $this->baseValidate($argsArray);
    $jsonArray = array();

    // Check if email already used
    $emailQueryString = "SELECT * FROM User WHERE Email='" . $argsArray['Email']. "'";    
    $emailResult = $this->dbHandle->query($emailQueryString);
    if (mysqli_num_rows($emailResult) != 0) {
      if ($errorsJson != "") {      
        $tmpArray = json_decode($errorsJson);
        $jsonArray = array_merge($jsonArray, $tmpArray);
      }
      array_push($jsonArray, "Email address already in system.");
      $errorsJson = json_encode($jsonArray);
      unset($jsonArray);
    }
    mysqli_free_result($emailResult);
    
    $jsonArray = array();

    // Check it's a full name
    if (!(strpos($argsArray['Name'], " "))) {
      if ($errorsJson != "") {      
        $tmpArray = json_decode($errorsJson);
        $jsonArray = array_merge($jsonArray, $tmpArray);
      }
      array_push($jsonArray, "Please use full name.");
      $errorsJson = json_encode($jsonArray);
      unset($jsonArray);
    }

    $jsonArray = array();

    return($errorsJson);
  }

  private function validateUpdate($argsArray) {
  
    // Cache the current value for later cparison
    $cacheString = "SELECT * FROM User WHERE ID=" . $argsArray['id'];
    $cacheResult = $this->dbHandle->query($cacheString );
    $cacheRow = $cacheResult->fetch_assoc();
  
    // Check the basics
    $errorsJson = $this->baseValidate($argsArray);
    $jsonArray = array();

    // Check if email already used
    if ($cacheRow['Email'] != $argsArray['Email']) {
      $emailQueryString = "SELECT * FROM User WHERE Email='" . $argsArray['Email']. "'";    
      $emailResult = $this->dbHandle->query($emailQueryString);
      if (mysqli_num_rows($emailResult) != 0) {
        if ($errorsJson != "") {      
          $tmpArray = json_decode($errorsJson);
          $jsonArray = array_merge($jsonArray, $tmpArray);
        }
        array_push($jsonArray, "Email address already in system.");
        $errorsJson = json_encode($jsonArray);
        unset($jsonArray);
      }
      mysqli_free_result($emailResult);
    }
    
    $jsonArray = array();

    // Check it's a full name
    if ($cacheRow['Name'] != $argsArray['Name']) {
      if (!(strpos($argsArray['Name'], " "))) {
        if ($errorsJson != "") {      
          $tmpArray = json_decode($errorsJson);
          $jsonArray = array_merge($jsonArray, $tmpArray);
        }
        array_push($jsonArray, "Please use full name.");
        $errorsJson = json_encode($jsonArray);
        unset($jsonArray);
      }
      $jsonArray = array();
    }

    return($errorsJson);
  
  }

  private function baseValidate($argsArray) {
    $rules = array();
    $rules[] = "required,Name,Please enter a name.";
    $rules[] = "required,Email,Please enter an email address.";
    $rules[] = "valid_email,Email,Please enter a valid email address.";

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
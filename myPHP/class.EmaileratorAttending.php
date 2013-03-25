<?php

class EmaileratorAttending extends MyCrud {

  function __construct() {
    parent::__construct("bowsy_emailerator", "Attending");
  }

  // Overwriting the baseclass
  public function Create($argsArray) {

    // Cache and remove stuff before the calk to the baseclass

    $eventName = $argsArray['EventName'];
    $miliseconds = $argsArray['EventDateTime'];
    $location = $argsArray['Location'];
    $description = $argsArray['Description'];
    unset($argsArray['EventName']);
    unset($argsArray['EventDateTime']);
    unset($argsArray['Location']);
    unset($argsArray['Description']);

    // Call the baseclass and get the auto-update ID
    $id = parent::Create($argsArray);

    // Update log
    $queryString = "SELECT * FROM User WHERE ID=" . $argsArray['UserID'];
    $result = $this->dbHandle->query($queryString);
    $row = $result->fetch_object();
    $userName = $row->Name;
    $email = $row->Email;

    $updateLogArray = array("c" => "EmaileratorLog", 
                            "a" => "Create", 
                            "UserID" => $argsArray['UserID'],
                            "EventID" => $argsArray['EventID'],
                            "Text" => $userName . " has been invited to " . $eventName . ".");

    // doClassAction is a function in classHandler.php
    doClassAction($updateLogArray); 

    // Send invite email
    $template = "../emailerator/template.EmaileratorFirstEmail.php";
    $inviteArgsArray['FirstName'] = substr($userName, 0, strpos($userName, " "));
    $inviteArgsArray['EventName'] = $eventName;
    $inviteArgsArray['EventDateTime'] = date('D, jS M Y \a\t g:iA', $miliseconds / 1000);
    $inviteArgsArray['Location'] = $location;
    $inviteArgsArray['Description'] = $description;
    $inviteArgsArray['ID'] = $argsArray['UserID'];
    $inviteArgsArray['EventID'] = $argsArray['EventID'];

    sendOneEmail($email, $userName, "Invite to " . $eventName, $template, $inviteArgsArray);

  }

  // An event has been updated so this person need to be re-invited
  function EventChanged($argsArray) {

    // Set their status for this event in Attending back to Unknown
    $updateString = "UPDATE Attending SET Attending='Unknown' WHERE (EventID='" . $argsArray['EventID'] . "' AND UserID ='" . $argsArray['UserID'] . ")'";
    $this->dbHandle->query($updateString);
    
    // Send a re-invite email
    $queryString = "SELECT * FROM User WHERE ID=" . $argsArray['UserID'];
    $result = $this->dbHandle->query($queryString);
    $row = $result->fetch_object();
    $userName = $row->Name;
    $email = $row->Email;

    $template = "../emailerator/template.EmaileratorReinviteEmail.php";

    $miliseconds = $argsArray['EventDateTime'];
    unset($argsArray['EventDateTime']);

    $argsArray['EventDateTime'] = date('D, jS M Y \a\t g:iA', $miliseconds / 1000);
    $argsArray['FirstName'] = substr($userName, 0, strpos($userName, " "));

    sendOneEmail($email, $userName, "Event has changed : " . $argsArray['EventName'], $template, $argsArray);
    
    // Update the log
    $updateLogArray = array("c" => "EmaileratorLog", 
                            "a" => "Create", 
                            "UserID" => $argsArray['UserID'],
                            "EventID" => $argsArray['EventID'],
                            "Text" => $userName . " has been re-invited to " . $argsArray['EventName'] . " following a change to the event.");

    // doClassAction is a function in classHandler.php
    doClassAction($updateLogArray); 

  }

  function Accept($argsArray) {

    // Start off by setting the appropriate response in the DB
    $string = "UPDATE Attending SET Attending='Yes' WHERE UserId='" . $argsArray['UserId'] . "' AND EventId='" . $argsArray['EventId'] . "'";
    $this->dbHandle->query($string);

    // Update the log - first need the user's and event's names...
    $nameQueryString = "SELECT * FROM User WHERE ID='" . $argsArray['UserId']. "'";    
    $nameResult = $this->dbHandle->query($nameQueryString);
    $nameRow = $nameResult->fetch_object();
    $userName = $nameRow->Name;
    mysqli_free_result($nameResult);

    $eventQueryString = "SELECT * FROM Event WHERE ID='" . $argsArray['EventId']. "'";    
    $eventResult = $this->dbHandle->query($eventQueryString);
    $eventRow = $eventResult->fetch_object();
    $eventName = $eventRow->Name;
    mysqli_free_result($eventResult);

    $updateLogArray = array("c" => "EmaileratorLog", 
                            "a" => "Create", 
                            "UserID" => $argsArray['UserId'],
                            "EventID" => $argsArray['EventId'],
                            "Text" => $userName . " has indicated that they are coming to " . $eventName . ".");

    // doClassAction is a function in classHandler.php
    doClassAction($updateLogArray); 

    // Then display a message for the user
    print "<h2>Thanks for letting us know.</h2>";
    print "You will receive an automatic reminder one week before the event.<p/>";
    print "If you have any questions please email Annie (annie@alobear.co.uk) or Dave (dave@bowsy.co.uk)<p/>";
    print "Looking forward to seeing you there!";
  }

  function Decline($argsArray) {

    // Start off by setting the appropriate response in the DB
    $string = "UPDATE Attending SET Attending='No' WHERE UserId='" . $argsArray['UserId'] . "' AND EventId='" . $argsArray['EventId'] . "'";
    $this->dbHandle->query($string);

    // Update the log - first need the user's and event's names...
    $nameQueryString = "SELECT * FROM User WHERE ID='" . $argsArray['UserId']. "'";    
    $nameResult = $this->dbHandle->query($nameQueryString);
    $nameRow = $nameResult->fetch_object();
    $userName = $nameRow->Name;
    mysqli_free_result($nameResult);

    $eventQueryString = "SELECT * FROM Event WHERE ID='" . $argsArray['EventId']. "'";    
    $eventResult = $this->dbHandle->query($eventQueryString);
    $eventRow = $eventResult->fetch_object();
    $eventName = $eventRow->Name;
    mysqli_free_result($eventResult);

    $updateLogArray = array("c" => "EmaileratorLog", 
                            "a" => "Create", 
                            "UserID" => $argsArray['UserId'],
                            "EventID" => $argsArray['EventId'],
                            "Text" => $userName . " has indicated that they are not coming to " . $eventName . ".");

    // doClassAction is a function in classHandler.php
    doClassAction($updateLogArray); 

    // Then display a message for the user
    print "<h2>Thanks for letting us know.</h2>";
    print "We won't contact you again about this event.<p/>";
    print "Hopefully you'll be interested (and able) to come to other events in the future - we'll be in touch!";
  }

}

?>
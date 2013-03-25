<?php

// Get the utilities libraru
require("/home/bowsy/public_html/myPHP/utilities.php");

// Get the email library
require("/home/bowsy/public_html/myPHP/spammer.php");

function build_and_send($dbHandle, $id, $template, $eventRow) {

  // Need to get their personal info
  $queryString = "SELECT * FROM User WHERE ID='" . $id . "'";
  $userResult = $dbHandle->query($queryString);
  $userRow = $userResult->fetch_assoc();

  $reminderArgsArray = array();
  $reminderArgsArray['FirstName'] = substr($userRow['Name'], 0, strpos($userRow['Name'], ' '));
  $reminderArgsArray['Name'] = $eventRow['Name'];      
  $reminderArgsArray['EventDateTime'] = date('D, jS M Y \a\t g:iA', $eventRow['EventDateTime'] / 1000);
  $reminderArgsArray['Location'] = $eventRow['Location'];
  $reminderArgsArray['Description'] = $eventRow['Description'];
  $reminderArgsArray['EventID'] = $eventRow['ID'];
  $reminderArgsArray['ID'] = $userRow['ID'];

  sendOneEmail($userRow['Email'], $userRow['Name'], "Event reminder!", $template, $reminderArgsArray);
  
}

// Get the current time
$currentTime = time();

// Loop through all the events

$dbHandle = new mysqli("localhost", "bowsy", "VU8Jc7ccirsre73", "bowsy_emailerator");

$queryString = "SELECT * FROM Event";
$result = $dbHandle->query($queryString);

while ($eventRow = $result->fetch_assoc()) {

  // Skip if event date is in the past
  if (($eventRow['EventDateTime'] / 1000) < $currentTime) { continue; }

  // Get the number days between now and the event
  $diff = ($eventRow['EventDateTime'] / 1000) - $currentTime;
  $days = (int) (($diff / 86400));

  // If it is 7 days to the event
  if ($days == 7) {

    $template = "/home/bowsy/public_html/emailerator/template.EmaileratorWeekBefore.php";

    // Get Attending for this event for everyone who is 'Yes' or 'Unknown'
    $queryString = "SELECT * FROM Attending WHERE EventID='" . $eventRow[ID] . "' AND (Attending='Yes' OR Attending='Unknown')";
    $attendingResult = $dbHandle->query($queryString);

    // For each one send a final reminder
    while ($attendingRow = $attendingResult->fetch_assoc()) {
      build_and_send($dbHandle, $attendingRow[UserID], $template, $eventRow);
    }
    
  }
  // Otherwise send a reminder to undecided people if it's mod 4 days to the event
  elseif ((($days % 4) == 0) && ($days != 0)) {

    $template = "/home/bowsy/public_html/emailerator/template.EmaileratorReminder.php";

    // Get Attending for this event for people who are 'Unknown' (yes, this is inefficient getting them twice)
    $queryString = "SELECT * FROM Attending WHERE EventID='" . $eventRow[ID] . "' AND Attending='Unknown'";
    $attendingResult = $dbHandle->query($queryString);

    // For each one send a reminder
    while ($attendingRow = $attendingResult->fetch_assoc()) {      
      build_and_send($dbHandle, $attendingRow[UserID], $template, $eventRow);      
    }

  }

}

mysqli_free_result($result);

?>
<?php

// Validate incomging data
// import the validation library - http://www.benjaminkeen.com/software/php_validation/
require("validation.php");

// For sending emails
require("spammer.php");

abstract class MyCrud
{

  // Property declarations
  protected $dbName;
  protected $tableName;
  protected $dbHandle;

  // Connect to DB -- Database and table names passed in by subclass constructor
  function __construct() {
    $argsArray = func_get_args();
    $this->dbName = $argsArray[0];
    $this->tableName = $argsArray[1];
    $this->dbHandle = new mysqli("localhost", "bowsy", "VU8Jc7ccirsre73", $this->dbName);
    if ($this->dbHandle->connect_errno) {
      echo "Failed to connect to MySQL: (" . $dbHandle->connect_errno . ") " . $dbHandle->connect_error;
    }
  }

  // Add new entries into the table
  public function Create($argsArray) {
    $string = "INSERT INTO " . $this->tableName . " (";
    foreach ($argsArray as $key => $value) {
      $string .= $key . ",";
    }
    $string = rtrim($string, ",");
    $string .= ") VALUES (";
    foreach ($argsArray as $key => $value) {
      $string .= "'" . addslashes($value) . "',";
    }
    $string = rtrim($string, ',');
    $string .= ")";
    $this->dbHandle->query($string);
    return($this->dbHandle->insert_id);
  }

  // Return data from a table with lots of optional parameters
  public function Read($argsArray) {
    
    $queryString = "SELECT * FROM " . $this->tableName . " ";

    // Introduce WHERE by one field
    if (array_key_exists("field", $argsArray)) {    
      $field = $argsArray['field'];
      $queryString .=  "WHERE " . $argsArray['field'] . "=" . $argsArray[$field] . " ";
    }

    // Sort if needed
    if (array_key_exists("sort", $argsArray)) {    
      $queryString .=  "ORDER BY " . $argsArray['sort'] . " ";
    }

    // Reverse the sort if needed
    if (array_key_exists("reverseSort", $argsArray)) {    
      $queryString .=  "ORDER BY " . $argsArray['reverseSort'] . " DESC ";
    }

    // Limit number of results if required
    if (array_key_exists("from", $argsArray)) {
      $queryString .= "LIMIT " . ($argsArray['from'] -1) . "," . $argsArray['num'];
    }

    $result = $this->dbHandle->query($queryString);
    while ($row = $result->fetch_assoc()) {
        $rowArray[] = $row;
    }

    if (! empty($rowArray)) {
      foreach($rowArray as $index => $valueArray) {
        foreach($valueArray as $key => $value) {
          $rowArray[$index][$key] = stripslashes($value);
        }
      }
    }

    return json_encode($rowArray);
    mysqli_free_result($result);
  }

  // Update entries in the table
  public function UpdateByID($argsArray) {

    $id = $argsArray['id'];
    unset($argsArray['id']);

    $string = "UPDATE " . $this->tableName . " SET ";
    foreach ($argsArray as $key => $value) {
      $string .= $key . "=" . "'" . addslashes($value) . "'" . ",";
    }
    $string = rtrim($string, ",");
    $string .= " WHERE ID=" . $id;

    $this->dbHandle->query($string);

  }

  // Remove entries from the table
  public function DeleteByID($argsArray) {
    $this->dbHandle->query("DELETE FROM " . $this->tableName . " WHERE ID=" . $argsArray['id']);
  }

  // Close DB handle
  function __destruct() {
    $this->dbHandle->close();
  }

}

?>
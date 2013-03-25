<?php

function emptyTestFile() {
  $myFile = "testFile.txt";
  $fh = fopen($myFile, 'w') or die("can't open file");
  fclose($fh);
}

function logToTestFile($outputString) {
  $myFile = "testFile.txt";
  $fh = fopen($myFile, 'a') or die("can't open file");
  fwrite($fh, $outputString);
  fclose($fh);
}

function logArrayToTestFile($testArray) {
  $myFile = "testFile.txt";
  $fh = fopen($myFile, 'a') or die("can't open file");
  logArrayRecurse($fh, $testArray, 0);
  fclose($fh);
}

function logArrayRecurse($fh, $inputArray, $level) {
  if (! empty($inputArray)) {
    foreach($inputArray as $key => $value) {
      if (is_array($value)) {
        $pad = str_pad("", ($level +1 ) * 2);
        fwrite($fh, $pad . "--\n");
        logArrayRecurse($fh, $value, $level + 1);
      } else {
        $pad = str_pad("", $level * 2);
        fwrite($fh, $pad . "Key: " . $key . ": Value : " . $value . "\n");
      }
    }
  }
}

function stripslashes_deep($value)
{
    $value = is_array($value) ?
                array_map('stripslashes_deep', $value) :
                stripslashes($value);

    return $value;
}

?>
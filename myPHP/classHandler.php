<?php

  /***********
   *
   * Note: If using from another local script just include and call doClassAction directly with arguement array
   * If called from server 'main' will set actionArray to passed in parameters
   *
  ************/

include ("utilities.php");

class ClassAutoloader {
  public function __construct() {
    spl_autoload_register(array($this, 'loader'));
  }
  
  private function loader($className) {
    include 'class.' . $className . '.php';
  }
}

// Work out which function to call and either print the result or return it
function doClassAction($actionArray,$returnType="return") {

  // Initiate auto-loading for classes
  $autoloader = new ClassAutoloader();

  // Create the appropriate subclass of mrCrud depending on what is passed in
  $className = $actionArray['c'];
  $instance = new $className();
  unset($actionArray['c']);

  // Now call the appropriate function on it
  $action = $actionArray['a'];
  unset($actionArray['a']);

  // If called by the server need to pass JSON via print, if called as a library function need to use return
  if ($returnType == "return") {
    return ($instance->$action($actionArray));
  }
  else {
    print ($instance->$action($actionArray));
  }
}

// Main
// Okay - it turns out that even if you call a function directly from another script it arrives
// as a zero-arguement get. So, if we want to be able to call this as a library *and* directly from
// the server we're going to have to do some hackery...

if (count($_GET) != 0) {
  doClassAction($_GET, "print");
}
elseif (count($_POST) != 0) {
  doClassAction($_POST, "print");
}

// Implied - else, do nothing (called as a library function)

?>
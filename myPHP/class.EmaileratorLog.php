<?php

class EmaileratorLog extends MyCrud {

  function __construct() {
    parent::__construct("bowsy_emailerator", "Log");
  }

  function Delete($argsArray) {
    // Does nothing - can't imagine how this would get called by mistake but blocking it anwyay
  }

}

?>
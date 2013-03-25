<html>

  <?php include "/home/bowsy/public_html/myPHP/classHandler.php" ?>

  <head>
    <title>The All Powerful Emailerator!</title>

    <link rel='stylesheet' type='text/css' href='css/myCss.css' />
    <link rel='stylesheet' type='text/css' href='../jquery/css/redmond/jquery-ui-1.8.17.custom.css' />
    <link rel='stylesheet' type='text/css' href='../myJavascript/chosen/chosen.css' />
    <link rel='stylesheet' type='text/css' href='http://fonts.googleapis.com/css?family=Oxygen'>

    <script type='text/javascript' src='../jquery/js/jquery-1.7.1.min.js'></script>
    <script type='text/javascript' src='../jquery/js/jquery-ui-1.8.17.custom.min.js'></script>
    <script type="text/javascript" src="../myJavascript/utilities.js"></script>
    <script type="text/javascript" src="../myJavascript/jquery-ui-timepicker-addon.js"></script>
    <script type="text/javascript" src="../myJavascript/dingram-chosen/chosen/chosen.jquery.js"></script>
    <script type="text/javascript" src="../myJavascript/dingram-chosen/chosen/chosen.jquery.min.js"></script>    
    <script type="text/javascript" src="js/emailerator.js"></script>
  </head>

  <body>
  
    <div id="dialog" title="Dialog Box"></div>

    <div id="headerDiv" class="center">
      <div id="myh2"><img width="50px" height="50px" align="middle" src="images/EmailSling.png">
      The Great and Powerful Emailerator!</div>
      <p>
    </div>
    
    <div id="peopleDiv">
    
      <div id="peopleTop">
    
        <div id="myh4">People:</div>

        <form id="nameForm">
          <input id="ID" name="ID" type="hidden" value="">
    
          <select id="nameFormSelect" class="chzn-select" data-placeholder="Choose a person..." style="width:250px" onChange="selectNameFormAutoChange()">
            <option value="new" selected="selected">New</option>

            <?php
              $argsArray = array("c" => "EmaileratorUser", "a" => "Read", "sort" => "Name");
          
              // doClassAction is a function in classHandler.php
              $resultArray  = json_decode(doClassAction($argsArray));
              for ($i = 0; $i < count($resultArray); $i++) {
                print "<option value=\"" . $resultArray[$i]->ID . "\">" . $resultArray[$i]->Name . "</option>\n";
              }
            ?>

          </select>
      
          <p/>       
          Name:<br/>
          <input type="text" id="Name" name="Name"/><br/>
          Email:<br/>
          <input type="text" id="Email" name="Email"/><p/>        
        
          <div id="clearFloat"></div>
        
          <button id="GoButton" type="button" onClick="selectNameFormGoButtonPressAjax()">Create</button>
          &nbsp;&nbsp;
          <button id="DeleteButton" type="button" style="visibility:hidden" onClick="selectNameFormDeleteButtonPress()">Delete</button>
        </form>
    
      </div>
    
      <div id="peopleBottom">
    
        <div id="myh4">People Log:</div>
        <div id="nameLogDiv"></div>
      
      </div>
      
    </div>

    <div id="eventsDiv">

      <form id="eventForm">
    
      <div id="eventsDivTop">
    
        <div id="eventsDivTopLeft">    
    
          <div id="myh4">Events:</div>

            <input id="ID" name="ID" type="hidden" value="">

            <select id="eventFormSelect" name="eventFormSelect" class="chzn-select" data-placeholder="Choose event..." style="width:250px" onChange="selectEventFormAutoChange()">
            <option value="new" selected="selected">New</option>
          
              <?php
                $argsArray = array("c" => "EmaileratorEvent", "a" => "Read", "sort" => "Name");
          
                // doClassAction is a function in classHandler.php
                $resultArray  = json_decode(doClassAction($argsArray));
                for ($i = 0; $i < count($resultArray); $i++) {
                  print "<option value=\"" . $resultArray[$i]->ID . "\">" . $resultArray[$i]->Name . "</option>\n";
                }
              ?>
          
            </select>
      
          </div> <!-- #eventsDivTopLeft -->
      
          <div id="eventsDivTopRight">
          
            <button id="GoButton" type="button" onClick="selectEventFormGoButtonPressAjax()">Create</button>
            &nbsp;&nbsp;
        
            <button id="DeleteButton" type="button" style="visibility:hidden" onClick="selectEventFormDeleteButtonPress()">Delete</button>  
          
          </div> <!-- #eventsDivTopRight -->
      
        </div> <!-- #eventsDivTop -->

        <div id="eventsDivRow">

          <div id="eventsDivLeft">
        
            Name:<br/>
            <input type="text" id="Name" name="Name"/><br/>
            Location:<br/>
            <input type="text" id="Location" name="Location"/><br/>
            Description:<br/>
            <textarea id="Description" name="Description" rows="5" cols="25"></textarea><p/>
        
          </div> <!-- #eventsDivLeft -->
        
          <div id="eventsDivMiddle">
        
            Attending:<br/>
          
            <div id="multiSelectDiv">
          
              <select id="MultipleSelect" name="MultipleSelect" data-placeholder="Choose attendees..." style="width:250px" multiple class="chzn-select">

                <?php
                  $argsArray = array("c" => "EmaileratorUser", "a" => "Read", "sort" => "Name");
          
                  // doClassAction is a function in classHandler.php
                  $resultArray  = json_decode(doClassAction($argsArray));
                  for ($i = 0; $i < count($resultArray); $i++) {
                    print "<option value=\"" . $resultArray[$i]->ID . "\">" . $resultArray[$i]->Name . "</option>\n";
                  }
                ?>

              </select><p/>
          
            </div> <!-- #multiSelectDiv -->
          
          </div> <!-- #eventsDivMiddle -->

          <div id="eventsDivRight">

            Date:<br/>          
            <input type="text" name="EventDateTime" id="EventDateTime" value=""><br/>
          
          </div> <!-- #eventsDivRight -->
        
        </div> <!-- #eventsDivRow -->
    
      <div id="eventsDivBottom">
    
        <div id="myh4">Event Log:</div>
        <div id="eventLogDiv"></div>
        
      </div> <!-- #eventsDivBottom -->
 
    </form>
 
    </div> <!-- #eventsDiv -->

    <div id="logDiv">
      <div id="myh4">General Log:</div>
      <div id="generalLogDiv"></div>
      <script>updateLogDivAjax("#generalLogDiv");</script>
    </div>
        
  </body>

</html>
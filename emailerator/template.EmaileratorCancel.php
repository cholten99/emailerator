<html>
  <body>
    <H2><img align="middle" src="http://bowsy.me.uk/emailerator/images/EmailSling.png" width="50" height="50">Greetings from the All Powerful Emailerator!</h2>
    <h4>
    
    <?php
      print "Hi " . $_GET['FirstName'] . ",<p/>";
    ?>
    
    Annie and Dave would like to inform you that unfortunately <?php print $_GET["Name"]; ?> has now been cancelled.<p/>
    
    As a reminder, this was due to take place on <?php print $_GET['DateTime']; ?> at <?php print $_GET['Location']; ?>.<p/>
    
    And was described as follows:<p/>
    
    <?php print $_GET['Description']; ?><p/>

    Annie and Dave</p>
    </h4>

  </body>
</html>
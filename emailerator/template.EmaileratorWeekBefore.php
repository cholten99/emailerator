<html>
  <body>
    <H2><img align="middle" src="/home/bowsy/public_html/emailerator/images/EmailSling.png" width="50" height="50">Greetings from the All Powerful Emailerator!</h2>
    <h4>
    
    <?php
      print "Hi " . $_GET['FirstName'] . ",<p/>";
    ?>
    
    Annie and Dave would like to remind you that it's now one week before <?php print $_GET["Name"]; ?>.<p/>
    
    This will take place on <?php print $_GET['EventDateTime']; ?> at <?php print $_GET['Location']; ?>.<p/>
        
    If you are <b>sure</b> you do not wish to recive any similar invites in future just click on the image below and we'll leave you in peace - but you'll be missing out on all kinds of fun things!<p/>
    
    <?php
      print "<a href='http://bowsy.me.uk/myPHP/classHandler.php?c=EmaileratorUser&a=DeleteById&DontStalkMe=True&id=" . $_GET['ID'] . "'>";
    ?>
    
    <img src="/home/bowsy/public_html/emailerator/images/StopStalkingMe.png" width="100" height="100"></a>
    
    <p/>

    Annie and Dave</p>
    </h4>

  </body>
</html>
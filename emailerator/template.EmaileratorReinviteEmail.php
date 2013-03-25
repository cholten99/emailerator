<html>
  <body>
    <H2><img align="middle" src="http://bowsy.me.uk/emailerator/images/EmailSling.png" width="50" height="50">Greetings from the All Powerful Emailerator!</h2>
    <h4>
    
    <?php
      print "Hi " . $_GET['FirstName'] . ",<p/>";
    ?>
    
    Annie and Dave would like to notify you that <?php print $_GET["EventName"]; ?> has now changed. You now may not be able to attend if you could previously and vice versa.<p/>
    
    It will now take place on <?php print $_GET['EventDateTime']; ?> at <?php print $_GET['Location']; ?>.<p/>
    
    A full description follows:<p/>
    
    <?php print $_GET['Description']; ?><p/>
    
    Please let us know if you would now like to attend or not by clicking on the appropriate icon below.<p/>
    If you do not wish to decide now you will be emailed a reminder automatically in a few days.<p/>

    <?php
      print "<a href='http://bowsy.me.uk/myPHP/classHandler.php?c=EmaileratorAttending&a=Accept&UserId=" . $_GET['UserID'] . "&EventId=" . $_GET['EventID'] . "'>";
    ?>

    <img align="middle" src="http://bowsy.me.uk/emailerator/images/TickIcon.png" width="50" height="50"></a>
    &nbsp;&nbsp;&nbsp;&nbsp;

    <?php
      print "<a href='http://bowsy.me.uk/myPHP/classHandler.php?c=EmaileratorAttending&a=Decline&UserId=" . $_GET['UserID'] . "&EventId=" . $_GET['EventID'] . "'>";
    ?>

    <img align="middle" src="http://bowsy.me.uk/emailerator/images/CrossIcon.png" width="50" height="50"></a> 
    <p/>

    [
    <?php
      print "<a href='http://bowsy.me.uk/myPHP/classHandler.php?c=EmaileratorAttending&a=Accept&UserId=" . $_GET['ID'] . "&EventId=" . $_GET['EventID'] . "'>";
    ?>

    Yes</a>]
    &nbsp;&nbsp;&nbsp;&nbsp;

    [
    <?php
      print "<a href='http://bowsy.me.uk/myPHP/classHandler.php?c=EmaileratorAttending&a=Decline&UserId=" . $_GET['ID'] . "&EventId=" . $_GET['EventID'] . "'>";
    ?>

    No</a>] 
    <p/>

    If you indicate you'd like to take part you will be emailed a reminder a week before the event takes place.<p/>
    
    If you are <b>sure</b> you do not wish to go and <b>never</b> want to recive any similar invites in future just click on the image below and we'll leave you in peace - but you'll be missing out on all kinds of fun things!<p/>
    
    <?php
      print "<a href='http://bowsy.me.uk/myPHP/classHandler.php?c=EmaileratorUser&a=DeleteById&DontStalkMe=True&id=" . $_GET['UserID'] . "'>";
    ?>
    
    <img src="http://bowsy.me.uk/emailerator/images/StopStalkingMe.png" width="100" height="100"></a>
    
    <p/>

    Annie and Dave</p>
    </h4>

  </body>
</html>
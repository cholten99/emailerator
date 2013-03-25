<html>
  <body>
    <H2><img align="middle" src="http://bowsy.me.uk/emailerator/images/EmailSling.png" width="50" height="50">Welcome to the All Powerful Emailerator!</h2>
    <h4>
    
    <?php
      print "Hi " . $_GET['FirstName'] . ",<p/>";
    ?>
    
    As you probably know Annie and Dave like to invite people to lots of cool things. 
    To make that easier I've written a website for us to use that enables us to easily <del>spam people</del> invite people to stuff.
    In future you may receive invitation emails from us via this system. <p/>
    If you fail to say whether or not you'd like to come to an event you'll get a reminder every few days until you do!<p/>
    If you sign up for something you'll receive a reminder email a week before it happens.<p/>
    If you are <b>sure</b> you <b>never</b> want to recive these invites just click on the image below and we'll leave you in peace - but you'll be missing out on all kinds of fun things!<p/>
    
    <?php
      print "<a href='http://bowsy.me.uk/myPHP/classHandler.php?c=EmaileratorUser&a=DeleteById&DontStalkMe=True&id=" . $_GET['ID'] . "'>";
    ?>
    
    <img src="http://bowsy.me.uk/emailerator/images/StopStalkingMe.png" width="100" height="100"></a>
    
    <p/>

    Annie and Dave</p>
    </h4>

  </body>
</html>
<?php

include("class.phpmailer.php");

function processTemplate($path) {
 ob_start();
 include($path);
 return ob_get_clean();
}

function sendOneEmail($email, $name, $subject, $template, $emailArgsArray) {

  // Fix the 'magic slashes' problem
  $name = stripslashes_deep($name);
  $subject = stripslashes_deep($subject);
  $emailArgsArray = stripslashes_deep($emailArgsArray);

  // Now replace returns with <br/> and <p/>
  $description = str_replace("\n", "<br/>", $emailArgsArray['Description']);
  $description = str_replace("<br/><br/>", "<p/>", $description);
  $emailArgsArray['Description'] = $description;

  $mail             = new PHPMailer();

  // $mail->SMTPDebug  = 2;

  // Before we read in the template (including processing it) we need to add the 
  // passed-in params to $_GET so they are available to the template script
  $_GET = array_merge($_GET, $emailArgsArray);

  // Load in template - processing it through the interpreter
  $body             = processTemplate($template);
  //$body             = eregi_replace("[\]",'',$body);

  $mail->IsSMTP();
  $mail->SMTPAuth   = true;                  // enable SMTP authentication
  $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
  $mail->Host       = "secure.quksdns10.net";      // sets five quid host as the SMTP server
  $mail->Port       = 465;                   // set the SMTP port

  $mail->Username   = "cholten99@bowsy.me.uk";  // username
  $mail->Password   = "Rotwang1";     // password

  $mail->From       = "dave@bowsy.co.uk";
  $mail->FromName   = "Dave Durant";
  $mail->Subject    = $subject;
  $mail->AltBody    = "Apologies, expected an HTML capable email reader!"; //Text Body
  $mail->WordWrap   = 50; // set word wrap

  $mail->MsgHTML($body);

  $mail->AddReplyTo("dave@bowsy.co.uk","Dave Durant");

  // $mail->AddAttachment("/path/to/file.zip");             // attachment
  // $mail->AddAttachment("/path/to/image.jpg", "new.jpg"); // attachment

  $mail->AddAddress($email, $name);

  $mail->IsHTML(true); // send as HTML

  $mail->Send();

  /*
  if(!$mail->Send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
  } else {
    echo "Message has been sent";
  }
  */

}

?>
<?php

 require_once("classes/Sentry.php");
 require_once("classes/DBLink.php");
 require_once("classes/Validator.php");
 require_once("classes/SMS.php");
 require_once("classes/Mapper.php");
 require_once("classes/Logger.php");
 require_once("classes/JSON.php");
 require_once('calendar/classes/tc_calendar.php');
 require_once("classes/Mailer.php");

 // These objects are now available for use on every page.... 
 $theSentry = new Sentry();
 $theDB = new DBLink();
 $theLogger = new Logger($theDB);
 $theValidator = new Validator();
 $theSMS = new SMS();
 $json = new Services_JSON();
 $mailer = new Mailer();

 echo '<!doctype html>';
 echo '<html lang="en">';
 echo '<head>';
 echo '<meta charset="utf-8">';
 echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
 echo '<title>ICRO - Irish Cave Rescue Organisation</title>';
 echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">';
 echo '<LINK rel="stylesheet" type="text/css" href="css/default.css">';
 echo '<link href="css/bootstrap.min.css" rel="stylesheet">';

 // The ICRO Ajax Library
 echo "<script type='text/javascript' src='scripts/icro_ajax.js'></script>";
 echo "<script type='text/javascript' src='calendar/calendar.js'></script>";
 
 date_default_timezone_set('Europe/Dublin');

 echo '</head><body><div class="mainframe">';
 echo "<div class='dateheader'>".date('l jS \of F Y')." **</div>";
 echo "<div class='maintitle'><br/></div>";
 echo "<div class ='bodyheader'>";
 echo "<div style='float:left'>";

 if (isset($_SESSION['username']) )
 {
        echo 'Logged in as '.$_SESSION['username'].' - <a href="logout.php">Logout?</a>';
 }

 if (isset($_SESSION['username']))
 {
     echo "</div><div style='float:right'><a href='index.php'>Home</a>";
     echo '&nbsp;~&nbsp;<a href="profile.php">My Profile</a>';
     echo '&nbsp;~&nbsp;<a href="https://www.windy.com/53.016/-9.378?52.638,-9.377,8" target="_blank">Clare Weather</a>';
     echo '&nbsp;~&nbsp;<a href="https://www.windy.com/54.292/-7.876?53.925,-7.877,8" target="_blank">Fermanagh Weather</a>';
     echo '&nbsp;~&nbsp;<a href="gen_calloutlist.php">Callout List</a>';
 }

 echo "</div><div style='clear:both'></div></div>";
 echo "<div class='content'>";

?>

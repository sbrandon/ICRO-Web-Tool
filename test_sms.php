<?php    
 require("template/header.php");
?>

<div class='newsbox'>
<div class='newstitle'>Mass SMS Tool</div>
<div class='newscontent'>

<?php
 if ($theSentry->login())
 {
     if (isset($_GET['message']) and isset($_GET['commit']))
     {
         if ($theSentry->hasPermission(1))
         {
                     // Send an SMS to the user, alerting him rescue is over
                     $res = $theSMS->send('353876275737',$_GET['message']);

                     if ($res)
                     {
                         echo "Stephen was sucessfully messaged via SMS<br/>";
                     }
                     else
                     {
                         echo "Stephen was NOT messaged via SMS - please inform manually! - ";
                         echo $theSMS->lastError();
                         echo "<br/>";
                     }

             echo "<br/>Return to <a href='index.php'>Main Page?</a><br/>";
         }
         else
         {
             echo "You don't have permission to do this<br/>";
         }
     }
     else
     {
         echo "<b>WARNING</b> - This will SMS Stephen - are you sure?<br/><br/>";
         echo "<form action='test_sms.php' method='get'>";
         echo "<input type='text' name='message' style='width: 85%;' length='120' value='[ICRO] message goes here'>";
         echo "<input type='submit' name='commit' value='Send SMS!'>";
         echo "</form>"; 
     }
 }
 else
 {
     echo "You need to be logged in to view this - use the links above...";
 }
?>


</div>
</div>

<?php
 require("template/footer.html");
?>


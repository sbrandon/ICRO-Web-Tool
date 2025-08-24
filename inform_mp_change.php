<?php    
 require("template/header.php");
?>

<div class='newsbox'>
<div class='newstitle'>Meeting Point Change - Inform Rescuers</div>
<div class='newscontent'>

<?php
 if ($theSentry->login())
 {
     if (isset($_GET['id']) and isset($_GET['commit']))
     {
         if ($theSentry->hasPermission(2) || $theSentry->hasPermission(8))
         {
             // Get rescue meeting point from the rescue record
             $meet_data = $theDB->fetchQuery("SELECT meetpoint_name,meetpoint_loc FROM rescues WHERE rescue_id = '".$_GET['id']."'");

             if (!$meet_data)
             {
                     die("Error - no meet point data found - ".$theDB->lastError());
             }
             else
             {
                     $meetingpoint = $meet_data[0]['meetpoint_name'] . " (" . $meet_data[0]['meetpoint_loc'] . ")";
             }
             
             // Get the list of called-out users
             $result = $theDB->fetchQuery("select * from users u, user_status us where us.status_id in (5,6,7,8,9) and us.user_id = u.user_id and us.rescue_id = ".$_GET['id']." order by last_name");

             if(!$result)
             {
                 echo "No users attached to this rescue to alert...";
             }
             else
             {
                 for ($i=0; $i < count($result); $i++)
                 {
                     // Send an SMS to the user, alerting him rescue is over
                     $res = $theSMS->send($result[$i]['mobile_phone'],"[ICRO] Meeting Point for incident has been updated - new one is $meetingpoint");

                     if ($res)
                     {
                         echo $result[$i]['first_name']." ".$result[$i]['last_name']." was sucessfully told of the update via SMS<br/>";
                     }
                     else
                     {
                         echo $result[$i]['first_name']." ".$result[$i]['last_name']." was NOT sucessfully told of the update via SMS - please inform manually!<br/>";
                     }
                 }
             }
             
             if ($theDB->doQuery("insert into rescue_log set rescue_id = ".$_GET['id'].", time=now(), message='Meeting Point update circulated via SMS by user ".$_SESSION['username']."'"))
             {
                 echo "Action recorded into log<br/>";
             }
             else 
             {
                 echo $theDB->lastError()."<br/>";
             }

             echo "<br/>Return to <a href='view_callout.php?id=".$_GET['id']."'>Incident Page?</a><br/>";
         }
         else
         {
             echo "You don't have permission to do this<br/>";
         }
     }
     else if (isset($_GET['id']))
     {
         echo "<b>WARNING</b> - You are about to send an SMS to all <b>called-out</b> rescuers, informing them of a meeting point change - are you sure?<br/><br/>";
         echo "<form action='inform_mp_change.php' method='get'>";
         echo "<input type='hidden' name='id' value='".$_GET['id']."'>";
         echo "<input type='submit' name='commit' value='Send SMS!'>";
         echo "</form>"; 
     }
     else
     {
         echo "No rescue specified!";
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


<?php
 require("template/header.php");

 echo "<div class='newsbox'>";
 echo "<div class='newstitle'>Process Incoming SMS</div>";
 echo "<div class='newscontent'>";

 if (isset($_GET['from']) && isset($_GET['text']))
 {
     $theLogger->log("Received SMS ".$_GET['text']." from ".$_GET['from']);

     // Get the userid based on the number - search all 4 fields
     $id_data = $theDB->fetchQuery("SELECT * FROM users WHERE (mobile_phone = '".$_GET['from']."' OR home_phone = '".$_GET['from']."' OR work_phone = '".$_GET['from']."' OR other_phone = '".$_GET['from']."');");
     
     if (!$id_data)
     {
         $theSMS->send($_GET['from'],"[ICRO] ERROR - Unrecognised Number - you must use a number associated with your profile");
         $theLogger->log("SMS error - unrecognised number - ".$theDB->lastError());
         die("ERROR - unrecognised number - ".$theDB->lastError());
     }

     // Get current state - find last state from DB for this user
     $state_data = $theDB->fetchQuery("SELECT * FROM user_status WHERE user_id = '".$id_data[0]['user_id']."'");

     if (!$state_data)
     {
         $theSMS->send($_GET['from'],"[ICRO] ERROR - Internal error - your user has no state data");
         $theLogger->log("SMS error - no state data found - ".$theDB->lastError());
         die("Error - no state data found - ".$theDB->lastError());
     }
     else
     {
         $cstate = $state_data[0]['status_id'];
     }
   
     $name  = $id_data[0]['first_name']." ".$id_data[0]['last_name'];
     $rid   = $state_data[0]['rescue_id'];
     $uid   = $id_data[0]['user_id'];
     $error = "Internal error"; 
     $from  = $_GET['from']; 
   
   
    // Parse the message for valid commands
    if (preg_match("/READY/i", $_GET['text']))
    {
        // Process READY command - signals user wants to move to standby status (status 3)
        // works if current state is 2
        if ($cstate == 2)
        {
             $res = $theDB->doQuery("UPDATE user_status SET status_id = '3' WHERE user_id = '$uid'");
             
             if ($res)
             {
                 $theDB->doQuery("INSERT into rescue_log set rescue_id = $rid ,time=now(),message='$name has accepted standby'");
                 $theSMS->send($from,"[ICRO] You are now on standby - get ready, rest and await instructions    ");
                 echo "User $name ($from) marked on Standby";
             }
             else
             {
                 $theSMS->send($from,"[ICRO] ERROR - $error    ");
                 echo "ERROR - ".$theDB->lastError();
             }
        }
        else
        {
            $theSMS->send($from,"[ICRO] ERROR - READY is an invalid message in your current state, message ignored    ");
            echo "ERROR - Invalid state change";
        }
    }
    else if (preg_match("/ABLE/i", $_GET['text']))
    {
        // Process ABLE command - signals user wants to move to callout status (status 5)
        // works if current state is 4
        if ($cstate == 4)
        {
             $res = $theDB->doQuery("UPDATE user_status SET status_id = '5' WHERE user_id = '$uid'");

             if ($res)
             {
                  // Get rescue meeting point from the rescue record
                  $meet_data = $theDB->fetchQuery("SELECT meetpoint_name,meetpoint_loc FROM rescues WHERE rescue_id = '".$rid."'");

                  if (!$meet_data)
                  {
                          $theSMS->send($_GET['from'],"[ICRO] ERROR - Internal error - ring Dave Masterson +353879648274    ");
                          $theLogger->log("SMS error - no meeting point data found for rescue id $rid - ".$theDB->lastError());
                          die("Error - no meet point data found - ".$theDB->lastError());
                  }
                  else
                  {
                          $meetingpoint = "go to " . $meet_data[0]['meetpoint_name'] . " (" . $meet_data[0]['meetpoint_loc'] . ")";
                  }
     
                 $theDB->doQuery("INSERT into rescue_log set rescue_id = $rid ,time=now(),message='$name has accepted callout'");
                 $theSMS->send($from,"[ICRO] You are called out - $meetingpoint - txt ONTHEWAY with travel time in mins when leaving e.g. ONTHEWAY 120    ");
                 echo "User $name ($from) marked on callout";
             }
             else
             {
                 $theSMS->send($from,"[ICRO] ERROR - $error    ");
                 echo "ERROR - ".$theDB->lastError();
             }
        }
        else
        {
            $theSMS->send($from,"[ICRO] ERROR - ABLE is an invalid message in your current state, message ignored    ");
            echo "ERROR - Invalid state change";
        }
    }
    else if (preg_match("/ONTHEWAY/i", $_GET['text']))
    {
        // Process ONTHEWAY command - signals user is on the way, and can optionally provide an ETA
        // Works if current state is 5 or 6
        if ($cstate == 5 || $cstate == 6)
        {
             // is their a usable ETA attached?
 
             $eta = 0;
             if (preg_match('/ONTHEWAY\s*([0-9]+)/i', $_GET['text'], $matches))
             {
                 $eta = $matches[1];
             }

             $eta_time       = date("Y-m-d H:i", strtotime("NOW + $eta minutes"));
             $eta_time_print = date("H:i", strtotime($eta_time));
             
             $res = $theDB->doQuery("UPDATE user_status SET status_id = '6', eta = '$eta_time' WHERE user_id = '$uid'");

             if ($res)
             {
                 $theDB->doQuery("INSERT into rescue_log set rescue_id = $rid ,time=now(),message='$name is on the way - ETA $eta_time_print'");
                 $theSMS->send($from,"[ICRO] Update noted - You are on the way - ETA $eta_time_print - thanks    ");
                 echo "User $name ($from) is on the way, ETA $eta_time_print";
             }
             else
             {
                 $theSMS->send($from,"[ICRO] ERROR - $error    ");
                 echo "ERROR - ".$theDB->lastError();
             }
        }
        else
        {
            $theSMS->send($from,"[ICRO] ERROR - ONTHEWAY is an invalid message in your current state, message ignored    ");
            echo "ERROR - Invalid state change";
        }
    }
    else if (preg_match("/OFFLINE/i", $_GET['text']))
    {
        // Process OFFLINE command - signals user is not available (status 1)
        // Works if current state is 0,1,2,3,4,5,6
        if ($cstate >=0 && $cstate <= 6)
        {
             $res = $theDB->doQuery("UPDATE user_status SET status_id = '1', rescue_id = '0' WHERE user_id = '$uid'");

             if ($res)
             {
                 $theDB->doQuery("INSERT into rescue_log set rescue_id = $rid ,time=now(),message='$name is unavailable for rescue duty'");
                 $theSMS->send($from,"[ICRO] You are now marked as unavailable for callout - reply ONLINE to become available again    ");
                 echo "User $name ($from) is unavailable";
             }
             else
             {
                 $theSMS->send($from,"[ICRO] ERROR - $error    ");
                 echo "ERROR - ".$theDB->lastError();
             }
        }
        else
        {
            $theSMS->send($from,"[ICRO] ERROR - OFFLINE is an invalid message in your current state, message ignored    ");
            echo "ERROR - Invalid state change";
        }
    }
    else if (preg_match("/ONLINE/i", $_GET['text']))
    {
        // Process ONLINE command - signals user is available (status 0)
        // Only works if current state is 1 (not available)
        if ($cstate == 1)
        {
             $res = $theDB->doQuery("UPDATE user_status SET status_id = '0' WHERE user_id = '$uid'");

             if ($res)
             {
                 $theDB->doQuery("INSERT into rescue_log set rescue_id = $rid ,time=now(),message='$name is now available for rescue duty'");
                 $theSMS->send($from,"[ICRO] You are now marked as available for callout - reply OFFLINE to become unavailable again    ");
                 echo "User $name ($from) is available";
             }
             else
             {
                 $theSMS->send($from,"[ICRO] ERROR - $error    ");
                 echo "ERROR - ".$theDB->lastError();
             }
        }
        else
        {
            $theSMS->send($from,"[ICRO] ERROR - ONLINE is an invalid message in your current state, message ignored    ");
            echo "ERROR - Invalid state change - $cstate to 0";
        }
    }
    else if (preg_match("/^OK/i", $_GET['text']))
    {
        // Process OK command - reply to standard sms test
        $theSMS->send($from,"[ICRO] Reply noted - system working - many thanks for testing!    ");

        $theLogger->log("SMS Test - OK reply recieved from $name ($from)");
    }
    else
    {
        // Unrecognised command
        $theSMS->send($from,"[ICRO] ERROR - sorry, I do not recognise your command - check the usage guide at ICRO.ie    ");
        echo "ERROR - Invalid command";
    }
 }
 else
 {
     echo "<form action='rescue_sms_in.php' method='get'>";
     echo "From:<br/><input type='text' name='from' maxlength='50'><br/><br/>";
     echo "Message:<br/><input type='text' name='text' maxlength='50'><br/><br/>";
     echo "<input type='submit' value='Send Message'>";
     echo "</form>";
 }

 echo "</div></div>";
 require("template/footer.html");
?>


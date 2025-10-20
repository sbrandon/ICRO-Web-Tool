<?php    
 require("template/header.php");

 echo "<div class='newsbox'>";
 echo "<div class='newstitle'>Rescue Log</div>";
 echo "<div class='newscontent'>";

 if ($theSentry->login())
 {

     // Get and check the rescue data for this ID
     if (isset($_GET['id']))
     {
         // Insert new record if message is set - but only if the form_id matches the 
         // one stored in the session variable (to prevent duplicate inserts on page refresh) 
         if (isset($_GET['message']))
         {
             //Retrieve the value of the hidden field
             $form_id = isset($_GET["form_id"])?$_GET["form_id"]:'';

             // Retrieve the saved session variable from last page load
             if(isset($_SESSION["FORM_ID"])) 
             {         
                 // if the two match, then we have a new form submission - otherwise, ignore, its a dupe
                 if(strcasecmp($form_id, $_SESSION["FORM_ID"]) == 0) 
                 {
                     if (! $theDB->doQuery("insert into rescue_log set rescue_id=".$_GET['id'].",time=NOW(),message='".mysqli_real_escape_string($theDB->db_object, $_GET['message'])."';"))
                 {
                     echo "ERROR - Couldn't record Log - ".$theDB->lastError()."<br/>";
                     }
               
                     unset($_SESSION["FORM_ID"]);
                 }
             }           
         }
     
         // get the rescue status
         $res = $theDB->fetchQuery("select status from rescues where rescue_id = ".$_GET['id']);
         $status = ($res) ? $res[0]['status'] : 0;

         //display the form to add new entries, if you are a warden or callout officer
         if ($status && ($theSentry->hasPermission(2) || $theSentry->hasPermission(8)))
         {
             $currdatetime = date('Y-m-d H:i:s');
             
             // create form_id - used to prevent duplicate postings being made on page refresh
             $formid = md5(uniqid(rand(), true));
             $_SESSION['FORM_ID'] = $formid;
                     
             echo "<center>";
             echo "<form method='get' action='rescue_log.php'>";
             echo "Enter Log Message: ";
             echo "<input type=text name='message' size=90 value = '' maxlength=500/>";
             echo "<input type=hidden name='form_id' id='form_id' value='".$_SESSION['FORM_ID']."'/>";
             echo "<input type=hidden name=id value='".$_GET['id']."'/>";
             echo "<input type=submit value='Log Message'/>";
             echo "</form>";
             echo "</center>";
         }

         // Display current log  
         $log_data = $theDB->fetchQuery("select distinct time,message from rescue_log where rescue_id=".$_GET['id']." order by time DESC");
   
         if (!$log_data)
         {
             echo 'No log entries for this rescue yet!';
         }
         else
         {
             print "<table width=100%>";
             
             for ($i=0; $i<count($log_data); $i++)
             {
                 if (preg_match("/has accepted/",$log_data[$i]['message']) || preg_match("/is on the way/",$log_data[$i]['message']))
                 {
                     print "<tr>";
                     print "<td style='border:1px solid #999999; background:#eeeeee;' width=20%>".$log_data[$i]['time']."</td>";
                     print "<td style='border:1px solid #999999; background:#eeeeee;color:#00FF11;' width=80%>".$log_data[$i]['message']."</td>";
                     print "</tr>";
                 }
                 else if (preg_match("/is unavailable/",$log_data[$i]['message']))
                 {
                     print "<tr>";
                     print "<td style='border:1px solid #999999; background:#eeeeee;' width=20%>".$log_data[$i]['time']."</td>";
                     print "<td style='border:1px solid #999999; background:#eeeeee;color:#FF0000;' width=80%>".$log_data[$i]['message']."</td>";
                     print "</tr>";
                 }
                 else
                 {
                     print "<tr>";
                     print "<td style='border:1px solid #999999; background:#eeeeee;' width=20%>".$log_data[$i]['time']."</td>";
                     print "<td style='border:1px solid #999999; background:#eeeeee;' width=80%>".$log_data[$i]['message']."</td>";
                     print "</tr>";
                 }                 
             }
              
             print "</table>";
             
             echo "<center>";
             echo "<br/>Return to <a href='view_callout.php?id=".$_GET['id']."'>Incident Page?</a>";
             echo "</center>";
         
         }
     }
     else
     {
         echo "Invalid Rescue ID selected";
     }
 }
 else
 {
     echo "You need to be logged in to view this page - use the links above...";
 }

 // End the page
 echo "</div></div>";
 require("template/footer.html");
?>


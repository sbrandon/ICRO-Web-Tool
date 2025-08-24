<?php    

 // Start the page
 require("template/header.php");
 echo "<div class='newsbox'>";
 echo "<div class='newstitle'>Modify User Profile</div>";
 echo "<div class='newscontent'>";

 // If its a non-logged in user, display public text
 if (!$theSentry->login())
 {
     echo "You need to be logged in to view this page"; 
 }
 else
 {
     if ($theSentry->hasPermission(2) || $_SESSION['user_id'] == $_GET['user_id'] || $_SESSION['user_id'] == $_POST['user_id'])
     {
         if (isset($_POST['do_modify']))
         {
             if (!$_POST['email'] | !$_POST['fname'] | !$_POST['lname'] | !$_POST['location1'] | !$_POST['locationTown'] | !$_POST['county'] | !$_POST['mobile'])
             {
                 echo('You did not fill in a required field. - <a href="user_mod.php">try again?</a>');
             }
             else
             {
                 $_POST['location1'] = strip_tags($_POST['location1']);
                 $_POST['location2'] = strip_tags($_POST['location2']);
                 $_POST['email'] = addslashes($_POST['email']);                 

                 $update = "update users set first_name = '".$_POST['fname']."',
                                              last_name = '".$_POST['lname']."',
                                                  email = '".$_POST['email']."',
                                          address_line1 = '".$_POST['location1']."',
                                          address_line2 = '".$_POST['location2']."',
                                                   town = '".$_POST['locationTown']."',
                                                 county = '".$_POST['county']."',
                                               postcode = '".$_POST['postcode']."',
                                           mobile_phone = '".$_POST['mobile']."',
                                             home_phone = '".$_POST['home']."',
                                             work_phone = '".$_POST['work']."',
                                            other_phone = '".$_POST['other']."',
                                                ffs_num = '".$_POST['ffsno']."'
                                          where user_id = '".$_POST['user_id']."'";

                 $result = $theDB->doQuery($update);

                 if (!$result)
                 {
                     print 'Error updating user in DB - '.$theDB->lasterror().' - <a href="user_add.php">try again?</a>';
                 }
                 else
                 {
                     $theLogger->log("Modified profile for ".$_POST['fname']." ".$_POST['lname']);
                     echo '<br/>User details successfully updated in DB<br/><br/>';
                 }
             }
         }
         else if (isset($_GET['user_id']))
         {
             // select the entry associated with that name....

             $res = $theDB->fetchQuery("select * from users where user_id = '".$_GET['user_id']."';");

             if (!$res)
             {
                 echo "Invalid user_id!";
                 die();
             }
             else
             {
                 echo "<form action=\"edit_profile.php\" method=\"post\">";
                 echo "<table align=\"center\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\" width=95%>";
                 echo "<tr><td width=50%>First Name*:</td><td width=50%>";
                 echo "<input type=\"hidden\" name=\"user_id\" value='".$_GET['user_id']."'>";
                 echo "<input type=\"text\" name=\"fname\" value='".$res[0]['first_name']."' maxlength=\"50\" size=40>";
                 echo "</td></tr>";
                 echo "<tr><td>Last Name*:</td><td>";
                 echo "<input type=\"text\" name=\"lname\" value='".$res[0]['last_name']."' maxlength=\"50\" size=40>";
                 echo "</td></tr>";
                 echo "<tr><td>E-Mail*:</td><td>";
                 echo "<input type=\"text\" name=\"email\" value='".$res[0]['email']."' maxlength=\"100\" size=40>";
                 echo "</td></tr>";
                 echo "<tr><td>Address Line 1:*</td><td>";
                 echo "<input type=\"text\" name=\"location1\" value='".$res[0]['address_line1']."' maxlength=\"100\" size=40>";
                 echo "</td></tr>";
                 echo "<tr><td>Address Line 2:</td><td>";
                 echo "<input type=\"text\" name=\"location2\" value='".$res[0]['address_line2']."' maxlength=\"100\" size=40>";
                 echo "</td></tr>";
                 echo "<tr><td>Town*:</td><td>";
                 echo "<input type=\"text\" name=\"locationTown\" value='".$res[0]['town']."' maxlength=\"100\" size=40>";
                 echo "</td></tr>";
                 echo "<tr><td>County*:</td><td>";
                 echo "<select name=\"county\">";
                 echo "<option value='".$res[0]['county']."' selected='selected'>".$res[0]['county']."</option>";
                 echo "<option value=\"Antrim\">Antrim</option>";
                 echo "<option value=\"Armagh\">Armagh</option>";
                 echo "<option value=\"Carlow\">Carlow</option>";
                 echo "<option value=\"Cavan\">Cavan</option>";
                 echo "<option value=\"Clare\">Clare</option>";
                 echo "<option value=\"Cork\">Cork</option>";
                 echo "<option value=\"Derry\">Derry</option>";
                 echo "<option value=\"Donegal\">Donegal</option>";
                 echo "<option value=\"Down\">Down</option>";
                 echo "<option value=\"Dublin\">Dublin</option>";
                 echo "<option value=\"Fermanagh\">Fermanagh</option>";
                 echo "<option value=\"Galway\">Galway</option>";
                 echo "<option value=\"Kerry\">Kerry</option>";
                 echo "<option value=\"Kildare\">Kildare</option>";
                 echo "<option value=\"Kilkenny\">Kilkenny</option>";
                 echo "<option value=\"Laois\">Laois</option>";
                 echo "<option value=\"Leitrim\">Leitrim</option>";
                 echo "<option value=\"Limerick\">Limerick</option>";
                 echo "<option value=\"Longford\">Longford</option>";
                 echo "<option value=\"Louth\">Louth</option>";
                 echo "<option value=\"Mayo\">Mayo</option>";
                 echo "<option value=\"Meath\">Meath</option>";
                 echo "<option value=\"Monaghan\">Monaghan</option>";
                 echo "<option value=\"Offaly\">Offaly</option>";
                 echo "<option value=\"Roscommon\">Roscommon</option>";
                 echo "<option value=\"Sligo\">Sligo</option>";
                 echo "<option value=\"Tipperary\">Tipperary</option>";
                 echo "<option value=\"Tyrone\">Tyrone</option>";
                 echo "<option value=\"Waterford\">Waterford</option>";
                 echo "<option value=\"Westmeath\">Westmeath</option>";
                 echo "<option value=\"Wexford\">Wexford</option>";
                 echo "<option value=\"Wicklow\">Wicklow</option>";
                 echo "</select>";
                 echo "</td></tr>";
                 echo "<tr><td>Postcode:</td><td>";
                 echo "<input type=\"text\" name=\"postcode\" value='".$res[0]['postcode']."' maxlength=\"100\" size=40>";
                 echo "</td></tr>";
                 echo "<tr><td>Mobile Contact Number (must begin with 353 or 44, no spaces):*</td><td>";
                 echo "<input type=\"text\" name=\"mobile\" value='".$res[0]['mobile_phone']."' maxlength=\"100\" size=40>";
                 echo "</td></tr>";
                 echo "<tr><td>Home Contact Number (must begin with 353 or 44, no spaces):</td><td>";
                 echo "<input type=\"text\" name=\"home\" value='".$res[0]['home_phone']."' maxlength=\"100\" size=40>";
                 echo "</td></tr>";
                 echo "<tr><td>Work Contact Number (must begin with 353 or 44, no spaces):</td><td>";
                 echo "<input type=\"text\" name=\"work\" value='".$res[0]['work_phone']."' maxlength=\"100\" size=40>";
                 echo "</td></tr>";
                 echo "<tr><td>Other Contact Number (must begin with 353 or 44, no spaces):</td><td>";
                 echo "<input type=\"text\" name=\"other\" value='".$res[0]['other_phone']."' maxlength=\"100\" size=40>";
                 echo "</td></tr>";
                 echo "<tr><td>FFS Insurance No:</td><td>";
                 echo "<input type=\"text\" name=\"ffsno\" value='".$res[0]['ffs_num']."' maxlength=\"100\" size=40>";
                 echo "</td></tr>";
                 echo "<tr><td colspan=\"2\" align=\"right\">";
                 echo "<input type=\"submit\" name=\"do_modify\" value=\"Modify User\">";
                 echo "</td></tr>";
                 echo "</table>";
                 echo "</form>";
            }
         }
         else
         {
             echo "<br/>Missing parameter<br/><br/>";
         }
     }
     else
     {
         echo "<br/>Permission Denied - Sorry!<br/><br/>";
     }
         
 }
 
 // End the page
 echo "<div id='clear_both' style='clear:both;'></div></div>";
 require("template/footer.html");
?>


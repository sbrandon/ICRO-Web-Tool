<?php  

 require("template/header.php");

 echo "<div class='newsbox'>";
 echo "<div class='newstitle'>Login to the System</div>";
 echo "<div class='newscontent'>";
 
 if(isset($_SESSION['username'])) 
 {
    if(isset($_SESSION['login_retval'])) 
    {
        echo 'You are logged in, '.$_SESSION['username'].' - continue to the <a href="'.$_SESSION['login_retval'].'">page you were looking for</a> or go to <a href="index.php">the main menu?</a><br/>';
    } 
    else
    {
        header('Location:index.php');
        die();
    }
 }

 if (isset($_POST['submit']))
 {
    if(!$_POST['uname'] | !$_POST['passwd']) 
    {
      echo 'You did not fill in a required field - <a href="login.php">try again?</a>';
    }
    else
    {
        if (!$theSentry->login($_POST['uname'],sha1($_POST['passwd'])))
        {
            print 'Login error - <a href="login.php">try again?</a>';
        }
        else
        {
            $theLogger->log("User ".$_SESSION['username']." logged in");

            if(isset($_SESSION['login_retval'])) 
            {
                echo 'You are logged in, '.$_SESSION['username'].' - continue to the <a href="'.$_SESSION['login_retval'].'">page you were looking for</a> or go to <a href="index.php">the main menu?</a><br/>';
            } 
            else
            {
                header('Location:index.php');
                die();
            }
        }
    }
 } 
 else 
 {
      echo "<div class='alert alert-danger' id='error-message' role='alert'>Invalid username or password.</div>";
      echo '<br/>';
      echo '<form action="' . $_SERVER['PHP_SELF'] .'" method="post">';
      echo '<table align="center" border="1" cellspacing="0" cellpadding="3">';
      echo '<tr><td><label for="username" class="form-label">Username</label></td><td>';
      echo '<input id="username" type="text" class="form-control" name="uname" maxlength="40">';
      echo '</td></tr>';
      echo '<tr><td><label for="password" class="form-label">Password</label></td><td>';
      echo '<input id="password" type="password" class="form-control" name="passwd" maxlength="50">';
      echo '</td></tr>';
      echo '<tr><td colspan="2" align="right">';
      echo '<input type="submit" name="submit" value="Login" class="btn btn-primary">';
      echo '</td></tr>';
      echo '</table>';
      echo '</form>';
      echo '<center>ICRO Member? Need an Account? Contact ICRO and we\'ll sort you out...</center><br/>';
      echo '<center>If you are interested in donating to ICRO, you\'ll find <a href="donations.php">all the info here</a></center><br/>';
 }

 echo '</div></div>';

 require("template/footer.html");
?>
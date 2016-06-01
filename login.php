<?php
session_start();
require_once('classes/pdo_ccims_connect.php');

/*
.-----------------------------------------------------.
| This code would be uncommented to enable checking   |
| for evil strings augmenting and/or replacing the    |
| use of mysql_real_escape_string() for 'security'.   |
.-----------------------------------------------------.
| require_once('classes/class.serverVarsUtility.php');|
| require_once('classes/class.sqlEvilStrings.php');   |
| require_once('classes/class.phpmailer.php');        |
.-----------------------------------------------------.
*/

// check for loggedin status
if(isset($_SESSION['loggedin']))
{
    header("Location: index.php");
    //die("You are already logged in!");
}

// only access the database if logging in via POST
if(isset($_POST['submit']))
{
  $dbh = ccims_connect();

  $username = mysql_real_escape_string($_POST['username']);
  $pass = mysql_real_escape_string($_POST['password']);
  $sth = $dbh->query ("SELECT * FROM ccims_users WHERE username = '{$username}' AND password = '{$pass}'");
  if( $sth->columnCount () < 1 )
  {
    die("Password was probably incorrect!");
  }
  $_SESSION['loggedin'] = "YES";
  $_SESSION['name'] = $username;
  header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Welcome to the CCIMS Home Page</title>
  <link href="http://www.cci.edu/css/main.css" rel="stylesheet" type="text/css" />

  <link href="http://www.cci.edu/css/print.css" rel="stylesheet" type="text/css" media="print" />
  <link href="http://www.cci.edu/css/sIFR.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="css/ccims.css" rel="stylesheet" type="text/css" />

  <!--[if lt IE 7]>
  <script src="/js/IE7.js" type="text/javascript"></script>
  <link href="/css/IE6_fixes.css" rel="stylesheet" type="text/css" />
  <![endif]-->

  <script type="text/javascript" src="http://www.cci.edu/js/prototype.js"></script>
  <script type="text/javascript" src="http://www.cci.edu/js/scriptaculous/scriptaculous.js?load=effects"></script>
</head>
<body id="homepage">
<div id="wrapper">
<!-- Header section -->
    <div class="pageHeader">
        <a href="http://www.cci.edu"><img src="http://www.cci.edu/images/logo.gif" /></a>
    </div>
<!-- ends #header -->

    <div id="main" style="padding: 25px; min-height: 550px;">

    <h1>Welcome to the CCIMS Home Page</h2>
    <form type='login.php' method='POST'>
      <table cellspacing="0" cellpadding="0" border="0">
        <tr>
          <td align="right" class="tdLabel"><label for="username">Username</label></td>
          <td><input type="text" id="username" name="username" /></td>
        </tr>
        <tr>
          <td align="right" class="tdLabel"><label for="password">Password</label></td>
          <td><input type="password" id="password" name="password" /></td>
        </tr>
        <tr>
          <td colspan="2" align="right" class="tdLabel">
            <input type='submit' name='submit' value='Login' />
          </td>
        </tr>
    </form>
  </div>
</div>
<script type="text/javascript">
document.forms[0].username.focus();
</script>
</body>
</html>

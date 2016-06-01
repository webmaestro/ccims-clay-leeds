<?php
session_start(); // This starts the session which is like a cookie, but it isn't saved on your hdd and is much more secure.
$myRowId = ( isset($_GET['id']) && is_numeric($_GET['id']) ) ? $_GET['id'] : 0;
if( isset($_SESSION['loggedin']) && $myRowId > 0 )
{

	require_once('classes/pdo_ccims_connect.php');
	$dbh = ccims_connect();
	$sth = $dbh->query ("SELECT id, username, password, first_name, last_name, email, favorite_movie FROM ccims_users WHERE id = " . $myRowId . ";") ;
    //echo '<tt>' . $select . '</tt>';

    $rowCount = 0;
    $formRow = '<tr class="tableData">';

    $sth->setFetchMode (PDO::FETCH_ASSOC);
    while ($row = $sth->fetch ()) {
    //while ($row = mysql_fetch_assoc($result)) {

        foreach ($row as $key => $value) {
        	if ( $key != 'id' ) {
				$formRow .= '<tr>' . "\n" . '<td align="right" class="tdLabel"><label for="' . $key . '">' . ucwords(str_replace('_', ' ', $key)) . '</label></td><td><input type="text" id="' . $key . '" name="' . $key . '" value="' . $value . '" /></td>' . "\n" . '</tr>' . "\n";
			} else {
				$myRowId = $value;
			}

		}
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>CCIMS Admin - Edit User</title>
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

		<h1>CCIMS Admin - Edit User</h2>
<?php
    echo '<form id="CCIMS" action="index.php" method="post">' . "\n";
    echo '<table cellspacing="0" cellpadding="0" border="0">' . "\n";
    echo $tHead . "</tr>\n";
    echo $formRow . "\n";
    echo '<td colspan="2" align="right"><input type="hidden" name="id" value="' . $myRowId . '" /><input type="hidden" name="action" value="3" /><input type="submit" value="Update User" name="submit" /></td></tr>' . "\n";
    echo '</table><br />' . "\n";
    echo '</form>' . "\n";
?>
		<a href="index.php">&laquo; Back to User Listing</a>
	</div>
</div>
</body>
</html>
<?php
} else {
	header("Location: home.php"); // Make sure they are logged in!
}
?>
<?php
session_start();
if(!isset($_SESSION['loggedin']))
{
    header("Location: home.php");
}

// load PDO db connect class
require_once('classes/pdo_ccims_connect.php');

// load sqlEvilStrings class
require_once('classes/class.sqlEvilStrings.php');

// show how sqlEvilStrings() class works (try using one of the items in the class or use 'WAITFOR' in *ANY* 'Create User' field)
$checkString = new sqlEvilStrings();
foreach ($_REQUEST as $key => $value) {
    if ( $checkString->isEvilString( mysql_real_escape_string($value) ) ) {
        die('Don\'t be naughty! The "' . htmlentities($key) . '" value of "' . htmlentities($value) . '" is not allowed! <a href="javascript:history.back();">&laquo;Go back</a>');
    }
}

if ( isset($_REQUEST['action']) ){
    $dbh = ccims_connect();

    if ( $_REQUEST['action'] > 1 ) {
        $myId = isset($_REQUEST['id'])&&is_numeric($_REQUEST['id'])?$_REQUEST['id']:exit();
    }
    switch ($_REQUEST['action']) {
        case 1: // $myAction = 'add';
            $dbInsertColumns = " '" . mysql_real_escape_string($_POST['username']) . "', '" . mysql_real_escape_string($_POST['password']) . "', '" . mysql_real_escape_string($_POST['first_name']) . "', '" . mysql_real_escape_string($_POST['last_name']) . "', '" . mysql_real_escape_string($_POST['email']) . "', '" . mysql_real_escape_string($_POST['favorite_movie']) . "' ";
            $sth = $dbh->query (" INSERT INTO ccims_users ( username, password, first_name, last_name, email, favorite_movie ) VALUES ( $dbInsertColumns ) ;");
            break;

        case 2: // $myAction = 'delete';
            $sth = $dbh->query (" DELETE FROM ccims_users WHERE id = " . mysql_real_escape_string($myId) . "  LIMIT 1 ;");
            break;

        default: // $myAction = 'edit';
            $dbUpdateColumns = " username = '" . mysql_real_escape_string($_POST['username']) . "', password = '" . mysql_real_escape_string($_POST['password']) . "', first_name = '" . mysql_real_escape_string($_POST['first_name']) . "', last_name = '" . mysql_real_escape_string($_POST['last_name']) . "', email = '" . mysql_real_escape_string($_POST['email']) . "', favorite_movie='" . mysql_real_escape_string($_POST['favorite_movie']) . "' ";
            $sth = $dbh->query (" UPDATE ccims_users SET $dbUpdateColumns WHERE id = $myId  ;");
    }

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>CCIMS Admin</title>
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

<script>
function confirmDelete(user) {
    return confirm('Are you sure you want to DELETE ['+user+']?');
}
</script>
</head>

<body id="homepage">
<div id="wrapper">
<!-- Header section -->
    <div class="welcomeMessage">
        Welcome, <?php echo ucfirst($_SESSION['name']); ?> ~ <a href="logout.php">Logout</a>
    </div>

    <div class="pageHeader">
        <a href="http://www.cci.edu"><img src="http://www.cci.edu/images/logo.gif" /></a>
    </div>
<!-- ends #header -->

    <div id="main" style="padding: 25px;;">

        <h1>Welcome to <a href="index.php">CCIMS Admin</a></h1>

<?php

	$dbh = ccims_connect();
    $dbFields = array(
        'id',
        'username',
        'password',
        'first_name',
        'last_name',
        'email',
        'favorite_movie'
        );
	$sth = $dbh->query ("SELECT id, username, password, first_name, last_name, email, favorite_movie FROM ccims_users;") ;

    $tHead = '<tr class="tableHeader">';
    $tRow = '<tr class="tableData">';
    $tCreateUserFields = '';
    $rowCount = 0;

    $sth->setFetchMode (PDO::FETCH_ASSOC);
    while ($row = $sth->fetch ()) {

        foreach ($row as $key => $value) {
        	$mailContent = htmlentities('About that movie: ' . $row['favorite_movie'] . '&body=Nice choice!');
            switch ($key) {
				case 'id':
                case 'password':// skip ID + Password
        			break;
				case 'username':
		        	$tRow .= '<td align="center"><a href="edit.php?id=' . $row['id'] . '&amp;action=3" class="editMe">' . $value . '<br /><span style="font-size:75%;">[edit]</span></a></td>' . "\n";
        			break;
        		case 'email':
		        	$tRow .= '<td><a href="mailto:' . $value . '?subject=' . $mailContent . '" class="emailMe">' . $value . '</a></td>' . "\n";
        			break;
        		case 'favorite_movie':
		        	$tRow .= '<td><a href="http://www.imdb.com/search/title?title=' . urlencode($value) . '&s=all" class="watchMe" target="_blank">' . $value . '</a></td>' . "\n";
        			break;
        		default:
        			$tRow .= '<td>' . $value . '</td>' . "\n";
        			break;
        	}
		}

        $tRow .= '<td align="center"><a href="?id=' . $row['id'] . '&amp;action=2" class="deleteMe" onclick="return confirmDelete(\'' . $row['username'] . '\');" title="Click to DELETE user \'' . $row['username'] . '\'">[x]</a></td>' . "\n";
        $tRow .= '</tr>';

        if ( $rowCount < 1 ) {
            foreach ( $dbFields as $key ) {
                if ( $key != 'id' ) { // skip ID + Password
                    if ( $key != 'password' ) {
                        $tHead .= '<th>' . ucwords(str_replace('_', ' ', $key)) . '</th>' . "\n";
                    }
                    $tCreateUserFields .= '          <tr>' . "\n";
                    $tCreateUserFields .= '              <td align="right" class="tdLabel"><label for="' . $key . '">' . ucwords(str_replace('_', ' ', $key)) . '</label></td><td><input type="text" id="' . $key . '" name="' . $key . '" /></td>' . "\n";
                    $tCreateUserFields .= '          </tr>' . "\n";
                }
            }
        }
        $rowCount++;
    }
    $tHead .= '<th>Delete User</th>' . "\n";

    echo '<div style="width: 250px; float: right; margin-left: 15px;">';
    echo '<h2 class="createUser"><a href="#" onclick="Effect.toggle(\'toggle_appear\', \'blind\'); return false;">Create User &raquo;</a></h2>' . "\n";
    echo '<div id="toggle_appear" style="background:#ccc;display: none;">' . "\n";
    echo '  <div>' . "\n";

    echo '  <form id="CCIMS" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . "\n";
    echo '      <table cellspacing="0" cellpadding="0" border="0">' . "\n";
    echo $tCreateUserFields;
    echo '          <tr>' . "\n";
    echo '              <td colspan="2" align="right"><input type="hidden" name="action" value="1" /><input type="submit" value="Create User" name="submit" /></td>' . "\n";
    echo '          </tr>' . "\n";
    echo '      </table>' . "\n";
    echo '  </form>' . "\n";

    echo '  </div>' . "\n";
    echo '</div>' . "\n";
    echo '</div>' . "\n";
    echo '<table cellspacing="0" cellpadding="0" border="0">' . "\n";
    echo $tHead . "</tr>\n";
    echo $tRow . "\n";
    echo '</table><br />' . "\n";

?>
        </div>
    </div>
</div>
</body>
</html>

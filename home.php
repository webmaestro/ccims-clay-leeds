<?php
session_start(); // This starts the session which is like a cookie, but it isn't saved on your hdd and is much more secure.
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
		<p>
			Please <a href="login.php">login</a> to begin using the CCIMS Content Management System!<br /><br />
		</p>
	</div>
</div>
</body>
</html>

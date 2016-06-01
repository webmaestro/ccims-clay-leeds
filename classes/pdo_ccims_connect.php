<?php
# pdo_ccims_connect.php - function for connecting to the "test" database

function ccims_connect ()
{
 $dbh = new PDO("mysql:host=localhost;dbname=ccims", "ccims_user", "ccims_user");
 return ($dbh);
}
?>

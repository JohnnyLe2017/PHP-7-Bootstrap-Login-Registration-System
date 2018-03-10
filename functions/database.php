<?php

$con = mysqli_connect('localhost', 'root', '', 'login_db');



//function to escape data
function escape($string) {
	global $con;
	mysqli_real_escape_string($con, $string);
}

function query($query) {
global $con;
return mysqli_query($con, $query);
}

function fetch_array($result) {
	global $con;
	 return mysqli_fetch_array($result);
}












 ?>

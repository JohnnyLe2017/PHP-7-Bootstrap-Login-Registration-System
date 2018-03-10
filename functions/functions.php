<?php

//These are helper functions

//clear things coming from form
function clean($string) {
	return htmlentities($string);
}

function redirect($location) {
	return header("Location: {$location}");
}

function set_message($message) {
  if(!empty($message)) {
    $_SESSION['message'] = $message;
  } else {
    $message = "";
  }
}

function display_message() {
  if(isset($_SESSION['message'])) {
    echo $_SESSION['message'];
     unset( $_SESSION['message']);
  }
}

//function to make forms very secure using MD5
function token_generator() {
$token = $_SESSION['token'] =  md5(uniqid(mt_rand(), true));
}

return $token;


/* Validation functions - validates users */
function validate_user_registration() {
	//access server variable to check if we have any POST requests
	if($_SERVER['REQUEST_METHOD'] == "POST") {
		$first_name       = clean($_POST['first_name']);
		$last_name        = clean($_POST['last_name']);
		$username         = clean($_POST['username']);
		$email            = clean($_POST['email']);
		$password         = clean($_POST['password']);
		$confirm_password = clean($_POST['confirm_password']);
	}
}





 ?>
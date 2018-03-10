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
 md5(uniqid(mt_rand(), true));
}











 ?>
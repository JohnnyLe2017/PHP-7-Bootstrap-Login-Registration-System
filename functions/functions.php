<?php

/****************helper functions ********************/

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
     unset($_SESSION['message']);
  }
}

//function to make forms very secure using MD5
function token_generator() {
$token = $_SESSION['token'] =  md5(uniqid(mt_rand(), true));
return $token;
}

function validation_errors($error_message) {
	$error_message = <<<DELIMITER

<div class="alert alert-danger alert-dismissible" role="alert">
  	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  	<strong>Warning!</strong> $error_message
 </div>
DELIMITER;

return $error_message;
}



//check if an email address already exists in database
function email_exists($email) {
	$sql = "SELECT id FROM users WHERE email = '$email'";
	$result = query($sql);
	if(row_count($result) == 1) {
		return true;
	} else {
		return false;
	}
}

//check if a username already exists in database
function username_exists($username) {
	$sql = "SELECT id FROM users WHERE username = '$username'";
	$result = query($sql);
	if(row_count($result) == 1) {
		return true;
	} else {
		return false;
	}
}

function send_email($email, $subject, $message, $header) {
return mail($email, $subject, $message, $header);
}


/* Validation functions - validates users */
function validate_user_registration() {
	//access server variable to check if we have any POST requests

	$errors = [];
	$min = 3;
	$max = 50;

	if($_SERVER['REQUEST_METHOD'] == "POST") {
		$first_name       = clean($_POST['first_name']);
		$last_name        = clean($_POST['last_name']);
		$username         = clean($_POST['username']);
		$email            = clean($_POST['email']);
		$password         = clean($_POST['password']);
		$confirm_password = clean($_POST['confirm_password']);


		if(strlen($first_name) < $min) {
			$errors[] = "Your first name cannot be less than {$min} characters";
		}

		if(strlen($first_name) > $max) {
			$errors[] = "Your first name cannot be more than {$max} characters";
		}

		if(strlen($last_name) < $min) {
			$errors[] = "Your last name cannot be less than {$min} characters";
		}

		if(strlen($last_name) > $max) {
			$errors[] = "Your last name cannot be more than {$max} characters";
		}

		if(strlen($username) < $min) {
			$errors[] = "Your username cannot be less than {$min} characters";
		}

		if(strlen($username) > $max) {
			$errors[] = "Your username cannot be more than {$max} characters";
		}

		if(username_exists($username)) {
			$errors[] = "This username is already registered";
		}

		if(email_exists($email)) {
			$errors[] = "This email address is already registered";
		}

		if(strlen($email) > $max) {
			$errors[] = "Your email address cannot be more than {$max} characters";
		}

		if($password !== $confirm_password) {
			$errors[] = "Your password does not match";
		}

		if(!empty($errors)) {
			foreach ($errors as $error) {
				echo validation_errors($error);
			}
		} else {
			if(register_user($first_name, $last_name, $username, $email, $password)) {
				set_message("<p class='bg-success text-center'>Please check your email for the activation link</p>");
				redirect("index.php");
				
			} else {
				set_message("<p class='bg-danger text-center'>Sorry we could not register the user</p>");
				redirect("index.php");
			}
		}
	}
}

/****************Register user functions ********************/


function register_user($first_name, $last_name, $username, $email, $password) {

	//escape data to prevent sql injection
	$first_name      = escape($first_name);
	$last_name       = escape($last_name);
	$username        = escape($username);
	$email           = escape($email);
	$password        = escape($password);

	if(email_exists($email)) {
		return false;
	} else if (username_exists($username)) {
		return false;
	} else {
		$password        = password_hash($password, PASSWORD_BCRYPT, array('cost'=>12));
		$validation_code = md5($username + microtime());
		$sql = "INSERT INTO users(first_name, last_name, username, email, password, validation_code, active)";
		$sql.= " VALUES('$first_name', '$last_name', '$username', '$email', '$password', '$validation_code', 0)";
		$result = query($sql);
		confirm($result);

		$subject = "Activate your Account";
		$message = "Please click the link below to active your Account
		http://localhost/login/activate.php?email=$email&code=$validation_code";
		$header = "From: noreply@mywebsite.com";
		send_email($email, $subject, $message, $header);

		return true;
	}
} 

/****************Activate user functions ********************/


function activate_user() {

	if($_SERVER['REQUEST_METHOD'] == "GET") {
	if(isset($_GET['email'])) {
	$email = clean($_GET['email']);
	$validation_code = clean($_GET['code']);
	$sql = "SELECT id FROM users WHERE email = '".escape($_GET['email'])."' AND validation_code = '".escape($_GET['code'])."' ";
	$result = query($sql);
	confirm($result);

	if(row_count($result) == 1) {
	$sql2 = "UPDATE users SET active = 1, validation_code = 0 WHERE email = '".escape($email)."' AND validation_code = '".escape($validation_code)."' ";
	$result2 = query($sql2);
	confirm($result2);

	set_message("<p class='bg-success'>Your account has been activated. You may now login.</p>");
	redirect("login.php");
	} else {
	set_message("<p class='bg-danger'>Your account could not be activated.</p>");
	redirect("login.php");
				}
			} 
		}
}




 ?>
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
     unset($_SESSION['message']);
  }
}

//function to make forms very secure using MD5
function token_generator() {
$token = $_SESSION['token'] =  md5(uniqid(mt_rand(), true));
}

return $token;

//check if an email address already exists in database
function email_exists($email) {
	$sql = "SELECT id FROM users WHERE email = '$email'";
	$result = query($sql);
	if(row_count($result) === 1) {
		return true;
	} else {
		return false;
	}
}

//check if a username already exists in database
function username_exists($username) {
	$sql = "SELECT id FROM users WHERE username = '$username'";
	$result = query($sql);
	if(row_count($result) === 1) {
		return true;
	} else {
		return false;
	}
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
				echo $error;
			}
		} else {
			if(register_user($first_name, $last_name, $username, $email, $password)) {
				set_message("<p class='bg-success text-center'>Please check your email for the activation link</p>");
				redirect("index.php");
				echo "User is registered";
			}
		}
	}
}

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
		$password        = md5($password);
		$validation_code = md5($username + microtime());
		$sql = "INSERT INTO users(first_name, last_name, username, email, password, validation_code, active)";
		$sql.= " VALUES('$first_name', '$last_name', '$username', '$email', '$password', '$validation_code', 0)";
		$result = query($sql);
		confirm($result);
		return true;
	}
}



 ?>
<?php
require "config.php";

$response = NULL;  //This variable holds the JSON data which we will send to the client

function checkRequestType() {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		return true;
	}
	else {
		return false;
	}
}

function addUser() {
	
	if(checkRequestType()) {
	
		$email = $_POST["email"]; 
		$password = $_POST["password"];
		$username = $_POST["username"];
		$profilepicture = $_POST["profilepicture"];
		
		$email = filter_var($email,FILTER_SANITIZE_EMAIL);
		$password = filter_var($password,FILTER_SANITIZE_STRING);
		$username = filter_var($username,FILTER_SANITIZE_STRING);
		$profilepicture = filter_var($profilepicture,FILTER_SANITIZE_STRING);
		
		$email = hash('md5', $email);
	    $password = hash('md5', $password);
		$username = hash('md5', $username);
		$profilepicture = hash('md5', $profilepicture);
		
		try {
			$conn = new PDO("mysql:host=$DATABASEHOSTNAME;dbname=$DATABASENAME", $DATABASEUSERNAME, $DATABASEPASSWORD);
			// set the PDO error mode to exception
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			echo "Connected successfully";
			} catch(PDOException $e) {
				echo "Connection failed: " . $e->getMessage();
		}
		
		$stmt = $conn->prepare("SELECT id, firstname, lastname FROM MyGuests");
		

	}
	else {
		$response->message = "Invalid request";
		echo (json_encode($response));	
	}
}

function removeUser() {
	
}

function updateUserProfilePicture() {
	
}

function updateUserPassword() {
	
}

function fetchUser() {
	
}

function fetchUserLeaderboard() {
	
}

function fetchGlobalLeaderboard() {
	
}

?>
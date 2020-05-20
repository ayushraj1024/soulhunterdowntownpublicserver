<?php
require_once('config.php');

//error displaying statement for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//error displaying statement for debugging


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
		
		//Hashing the password
	    $password = hash('md5', $password);
		
		//Opening the connection to the database
		try {
			$conn = new PDO("mysql:host=$DATABASEHOSTNAME;dbname=$DATABASENAME", $DATABASEUSERNAME, $DATABASEPASSWORD);
			// set the PDO error mode to exception
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$response->message = "Connection succeeded.";
			} catch(PDOException $e) {
				$response->message = "Connection failed: " . $e->getMessage();
		}
		
		//Preparing and executing the MySQL query on the database
		$stmt = $conn->prepare('INSERT INTO users (email,password,username,profilepicture,bulletfiringcooldown,attackpower,maxhp,points,coins,stage1unlocked,stage2unlocked,healthupgradecost,damageupgradecost,attackspeedupgradecost,healthupgradeindicatoranimationnumber,damageupgradeindicatoranimationnumber,attackspeedupgradeindicatoranimationnumber,validation,valid) VALUES (:email,:password,:username,:profilepicture," "," "," "," "," "," "," "," "," "," "," "," "," "," "," ")');
		$stmt->bindParam(':email', $email);
		$stmt->bindParam(':password', $password);
		$stmt->bindParam(':username', $username);
		$stmt->bindParam(':profilepicture', $profilepicture);
		if($stmt->execute()) {
			$response->message = "Record inserted";
		} else {
			$response->message = "Record not inserted";
		}
		
		//Relaying the response to the client
		echo (json_encode($response));
		
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
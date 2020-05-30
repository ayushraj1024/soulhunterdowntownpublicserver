<?php
require_once('config.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$response = NULL; //This variable holds the JSON data which we will send to the client
$noDuplicatesFound = True; //This variable tells whether we found a duplicate email address or a username
$code = NULL; //This variable will hold the secret code for resetting passwords

$lengthOfCode = 10;
$cryptoStrong = False;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
		$password = $_POST["password"];
		$username = $_POST["username"];
		$profilepicture = $_POST["profilepicture"];
		
		$password = filter_var($password,FILTER_SANITIZE_STRING);
		$username = filter_var($username,FILTER_SANITIZE_STRING);
		$profilepicture = filter_var($profilepicture,FILTER_SANITIZE_STRING);
		
		//Hashing the password
	    $password = hash('md5', $password);
		
		//Generate password reset code
		$code = rand(100000,999999);
		$code = hash('md5', $code);
		
		$code = substr($code,0,9);
		
		//Opening the connection to the database
		try {
			$conn = new PDO("mysql:host=$DATABASEHOSTNAME;dbname=$DATABASENAME", $DATABASEUSERNAME, $DATABASEPASSWORD);
			// set the PDO error mode to exception
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$response->success = "True";
			$response->message = "Connection succeeded.";
			} catch(PDOException $e) {
				$response->success = "False";
				$response->message = "Connection failed: " . $e->getMessage();
		}
		
		//Checking for duplicate username
		$stmt = $conn->prepare('SELECT * FROM users WHERE username = :username');
		$stmt->bindParam(':username',$username);
		if($stmt->execute()) {
			if($stmt->rowCount() > 0) {
				$response->success = "False";
				$response->message = "This username is already taken.";
				$noDuplicatesFound = False;
			}
		} else {
			$response->success = "False";
			$response->message = "Error occured. Please try again.";
			$noDuplicatesFound = False;
		}
		
		
		if($noDuplicatesFound) { //Checking if duplicate usernames or emails found or not
			
			//Preparing and executing the MySQL query on the database
			$stmt = $conn->prepare('INSERT INTO users (password,username,profilepicture,validation) VALUES (:password,:username,:profilepicture,:validation)');
			$stmt->bindParam(':password', $password);
			$stmt->bindParam(':username', $username);
			$stmt->bindParam(':profilepicture', $profilepicture);
			$stmt->bindParam(':validation', $code);
			if($stmt->execute()) {
				$response->success = "True";
				$response->message = "Account Created";
				$response->code = $code;
			} else {
				$response->success = "False";
				$response->message = "Account cannot be created. Please try again later.";
			}
		}
		//Relaying the response to the client
		$conn = null;
		echo (json_encode($response));
}
else {
		$response->success = "False";
		$response->message = "Invalid request";
		echo (json_encode($response));
}

?>
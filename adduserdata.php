<?php
require_once('config.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$response = NULL; //This variable holds the JSON data which we will send to the client
$noDuplicatesFound = True; //This variable tells whether we found a duplicate email address or a username

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
		
		//Checking for duplicate username
		$stmt = $conn->prepare('SELECT * FROM users WHERE username = :username');
		$stmt->bindParam(':username',$username);
		if($stmt->execute()) {
			if($stmt->rowCount() > 0) {
				$response->message = "This username is already taken.";
				$noDuplicatesFound = False;
			}
		} else {
			$response->message = "Error occured. Please try again.";
			$noDuplicatesFound = False;
		}
		
		//Checking for duplicate email
		$stmt = $conn->prepare('SELECT * FROM users WHERE email = :email AND valid = "1"');
		$stmt->bindParam(':email',$email);
		if($stmt->execute()) {
			if($stmt->rowCount() > 0) {
				$response->message."\nThis email is already taken.";
				$noDuplicatesFound = False;
			}
		} else {
			$response->message = "Error occured. Please try again.";
			$noDuplicatesFound = False;
		}
		
		
		if($noDuplicatesFound) { //Checking if duplicate usernames or emails found or not
			//Preparing and executing the MySQL query on the database
			$stmt = $conn->prepare('INSERT INTO users (email,password,username,profilepicture) VALUES (:email,:password,:username,:profilepicture)');
			$stmt->bindParam(':email', $email);
			$stmt->bindParam(':password', $password);
			$stmt->bindParam(':username', $username);
			$stmt->bindParam(':profilepicture', $profilepicture);
			if($stmt->execute()) {
				$response->message = "Record inserted";
			} else {
				$response->message = "Record not inserted";
			}
		}
		
		//Relaying the response to the client
		echo (json_encode($response));
}
else {
		$response->message = "Invalid request";
		echo (json_encode($response));
}

?>
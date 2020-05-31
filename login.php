<?php
require_once('config.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$nullValueFound = False; //This variable will keep track of the fact whether we found any null value or not

if($_SERVER['REQUEST_METHOD'] === 'POST') {

	//Receiving values from POST variables
	$username = $_POST["username"];
	$password = $_POST["password"];
	
	//Sanitizing the received values
	$username = filter_var($username,FILTER_SANITIZE_STRING);
	$password = filter_var($password,FILTER_SANITIZE_STRING);
	
	$password = preg_replace("/\s+/", "", $password);
	$username = preg_replace("/\s+/", "", $username);
	
	if($password == NULL OR $password == "")  {
		$nullValueFound = True;
	}
	if($username == NULL OR $username == "") {
		$nullValueFound = True;
	}
	
	if($nullValueFound) {
			$response->success = "False";
			$response->message = "Empty values are not allowed. Please enter another value.";
			echo (json_encode($response));
	} else {
	
		//Hashing the password
		$password = hash('md5', $password);
	
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
		
		//SQL command to login
		$stmt = $conn->prepare('SELECT * FROM users WHERE username = :username AND password = :password');
		$stmt->bindParam(':username',$username);
		$stmt->bindParam(':password',$password);
	
		if($stmt->execute()) {
			if($stmt->rowCount() == 1) {
				$response->success = "True";
				$response->message = "Code verified";
				$response->username = $username;
				$response->password = $password;
			}
		} else {
				$response->success = "False";
				$response->message = "Error occured. Please try again.";
		}
	
		$conn = null;
		echo (json_encode($response));
	}
	
}
else {
		$response->success = "False";
		$response->message = "Invalid request";
		echo (json_encode($response));
}

?>
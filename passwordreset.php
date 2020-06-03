<?php
require_once('config.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$response = NULL; //This variable holds the JSON data which we will send to the client
$proceedWithTransaction = False; /*This variable will store the boolean value which will tell us whether we can proceed
with our transaction or not*/

$nullValueFound = False; //This variable will keep track of the fact whether we found any null value or not

if($_SERVER['REQUEST_METHOD'] === 'POST') {
	
	//Receiving values from POST variables
	$username = $_POST["username"];
	$code = $_POST["code"];
	$password = $_POST["password"];
	
	//Sanitizing the received values
	$username = filter_var($username,FILTER_SANITIZE_STRING);
	$code = filter_var($code,FILTER_SANITIZE_STRING);
	$password = filter_var($password,FILTER_SANITIZE_STRING);
	
	//Remove whitespaces from the received values (tabs/spaces/newlines)
	$password = preg_replace("/\s+/", "", $password);
	$username = preg_replace("/\s+/", "", $username);
	$code = preg_replace("/\s+/", "", $code);
	
	if($password == NULL OR $password == "")  {
		$nullValueFound = True;
	}
	if($username == NULL OR $username == "") {
		$nullValueFound = True;
	}
	if($code == NULL OR $code == "") {
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
		
		//SQL command to check whether this pair of username and code exists or not
		$stmt = $conn->prepare('SELECT * FROM users WHERE username = :username AND validation = :code');
		$stmt->bindParam(':username',$username);
		$stmt->bindParam(':code',$code);
	
		if($stmt->execute()) {
			if($stmt->rowCount() == 1) {
				$response->success = "True";
				$response->message = "Code verified";
				$proceedWithTransaction = True;
			}
		} else {
				$response->success = "False";
				$response->message = "Error occured. Please try again.";
				$proceedWithTransaction = False;
		}
	
		//SQL command to update the password with the new password
		if($proceedWithTransaction) {
			$stmt = $conn->prepare('UPDATE users SET password = :password WHERE username = :username');
			$stmt->bindParam(':password',$password);
			$stmt->bindParam(':username',$username);
			if($stmt->execute()) {
				$response->success = "True";
				$response->message = "Password changed successfully.";
			}
			else {
				$response->success = "False";
				$response->message = "Error occured. Please try again.";
			}
		}
		else {
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
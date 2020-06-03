<?php
require_once('config.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$response = NULL; //This variable holds the JSON data which we will send to the client
$nullValueFound = False; //This variable will keep track of the fact whether we found any null value or not
$verifiedUser = False; //This varialbe will keep track of the fact that we are dealing with the correct user

if($_SERVER['REQUEST_METHOD'] === 'POST') {
	
	//Receiving the POST variables
	$username = $_POST["username"];
	$password = $_POST["password"];
	$profilepicture = $_POST["profilepicture"];
	
	//Sanitizing the received values
	$username = filter_var($username,FILTER_SANITIZE_STRING);
	$password = filter_var($password,FILTER_SANITIZE_STRING);
	$profilepicture = filter_var($profilepicture,FILTER_SANITIZE_STRING);
	
	//Remove whitespaces from the received values (tabs/spaces/newlines)
	$password = preg_replace("/\s+/", "", $password);
	$username = preg_replace("/\s+/", "", $username);
	$profilepicture = preg_replace("/\s+/", "", $profilepicture);
	
	//Remove from my profilepicture variable anything that is not 0,1,2,3,4
	$profilepicture = preg_replace("/[^0-4]/", "",$profilepicture);
	
	if($password == NULL OR $password == "")  {
		$nullValueFound = True;
	}
	if($username == NULL OR $username == "") {
		$nullValueFound = True;
	}
	if($profilepicture == NULL OR $profilepicture == "") {
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
				$response->message = "User verified";
				$verifiedUser = True;
			}
		} else {
				$response->success = "False";
				$response->message = "Error occured. Please try again.";
				$verifiedUser = False;
		}
		
		if($verifiedUser) {
			
			//Update profile picture
			$stmt = $conn->prepare('UPDATE users SET profilepicture = :profilepicture WHERE username = :username AND password = :password');
			$stmt->bindParam(':password',$password);
			$stmt->bindParam(':username',$username);
			$stmt->bindParam(':profilepicture',$profilepicture);
			
			if($stmt->execute()) {
				$response->success = "True";
				$response->message = "Profile picture changed successfully.";
			}
			else {
				$response->success = "False";
				$response->message = "Error occured. Please try again.";
			}
			
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
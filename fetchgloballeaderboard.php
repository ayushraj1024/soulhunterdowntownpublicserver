<?php
require_once('config.php');

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

$response = NULL; //This variable holds the JSON data which we will send to the client
$responseArray = []; //This variable holds an array of JSON objects (JSON Array)

$recordCounter = 0; //This is a counter which will help us count how many records we have put in our JSON Array

$nullValueFound = False; //This variable will keep track of the fact whether we found any null value or not
$verifiedUser = False; //This varialbe will keep track of the fact that we are dealing with the correct user

if($_SERVER['REQUEST_METHOD'] === 'POST') {
	
	$username = $_POST["username"];
	$password = $_POST["password"];
	
	$username = filter_var($username,FILTER_SANITIZE_STRING);
	$password = filter_var($password,FILTER_SANITIZE_STRING);
	
	$username = preg_replace("/\s+/", "", $username);
	$password = preg_replace("/\s+/", "", $password);
	
	if($username == NULL OR $username == "") {
		$nullValueFound = True;
	}
	if($password == NULL OR $password == "")  {
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
		else {
				$response->success = "False";
				$response->message = "Error occured. Please try again.";
				$verifiedUser = False;
			}
	} else {
		$response->success = "False";
		$response->message = "Error occured. Please try again.";
		$verifiedUser = False;
	}
	
	if($verifiedUser) {
		$stmt = $conn->prepare('SELECT username,profilepicture,points FROM users ORDER BY points DESC LIMIT 5');
		$stmt->execute();
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$response->username0 = $row[0]["username"];
		$response->profilepicture0 = $row[0]["profilepicture"];
		$response->points0 = $row[0]["points"];
		
		$response->username1 = $row[1]["username"];
		$response->profilepicture1 = $row[1]["profilepicture"];
		$response->points1 = $row[1]["points"];
		
		$response->username2 = $row[2]["username"];
		$response->profilepicture2 = $row[2]["profilepicture"];
		$response->points2 = $row[2]["points"];
		
		$response->username3 = $row[3]["username"];
		$response->profilepicture3 = $row[3]["profilepicture"];
		$response->points3 = $row[3]["points"];
		
		$response->username4 = $row[4]["username"];
		$response->profilepicture4 = $row[4]["profilepicture"];
		$response->points4 = $row[4]["points"];
		
		/*while($recordCounter < 5) {
			$response->username = $row[$recordCounter]["username"];
			$response->profilepicture = $row[$recordCounter]["profilepicture"];
			$response->points = $row[$recordCounter]["points"];
			$recordCounter++;
			array_push($responseArray,$response);	
		}*/
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
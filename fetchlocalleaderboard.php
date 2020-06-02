<?php
require_once('config.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$nullValueFound = False; //This variable will keep track of the fact whether we found any null value or not
$verifiedUser = False; //This varialbe will keep track of the fact that we are dealing with the correct user

$userScore = 0; //This variable will hold the score of the user requesting the list
$topcounter = 0; //This variable will keep track of how many higher score records we have put into the response variable
$bottomcounter = 0; //This variable will keep track of how many lower score records we have put into the response variable

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
	} else {
		$response->success = "False";
		$response->message = "Error occured. Please try again.";
		$verifiedUser = False;
	}
	
	if($verifiedUser) {
		$stmt = $conn->prepare('SELECT * FROM users WHERE username = :username AND password = :password');
		$stmt->bindParam(':username',$username);
		$stmt->bindParam(':password',$password);
		
		if($stmt->execute()) {
			$response->success = "True";
			$response->message = "Points retrieved";
			$row = $stmt->fetch();
			$userScore = $row["points"];
			
			//Fetching two users above the current user's points
			$stmt = $conn->prepare('SELECT username,profilepicture,points FROM users WHERE points >= :userScore AND username != :username ORDER BY points ASC');
			$stmt->bindParam(':userScore',$userScore);
			$stmt->bindParam(':username',$username);
			
			if($stmt->execute()) {
				if($stmt->rowCount() > 0) {
					$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
					
					$response->topusername = $row[0]["username"];
					$response->toppoints = $row[0]["points"];
					$response->topprofilepicture = $row[0]["profilepicture"];
					$topcounter++;
					$response->topcounter = $topcounter;
					
					if($row[1] != NULL) {
					$response->secondtopusername = $row[1]["username"];
					$response->secondtoppoints = $row[1]["points"];
					$response->secondtopprofilepicture = $row[1]["profilepicture"];
					$topcounter++;
					$response->topcounter = $topcounter;
					}
					
				}
			} else {
				$response->success = "False";
				$response->message = "Error occured. Please try again.";
			}
			
			
			//Fetching two users below the current user's points
			$stmt = $conn->prepare('SELECT username,profilepicture,points FROM users WHERE points < :userScore ORDER BY points DESC');
			$stmt->bindParam(':userScore',$userScore);
			
			if($stmt->execute()) {
				if($stmt->rowCount() > 0) {
					$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
					
					$response->bottomusername = $row[0]["username"];
					$response->bottompoints = $row[0]["points"];
					$response->bottomprofilepicture = $row[0]["profilepicture"];
					$bottomcounter++;
					$response->bottomcounter = $bottomcounter;
					
					if($row[1] != NULL) {
					$response->bottomtopusername = $row[1]["username"];
					$response->bottomtoppoints = $row[1]["points"];
					$response->bottomtopprofilepicture = $row[1]["profilepicture"];
					$bottomcounter++;
					$response->bottomcounter = $bottomcounter;
					}
					
				}
			} else {
				$response->success = "False";
				$response->message = "Error occured. Please try again.";
			}
			
		
		} else {
			$response->success = "True";
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
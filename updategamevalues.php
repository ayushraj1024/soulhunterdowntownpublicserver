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
	$points = $_POST["points"];
	$coins = $_POST["coins"];
	$bulletfiringcooldown = $_POST["bulletfiringcooldown"];
	$attackpower = $_POST["attackpower"];
	$stage1unlocked = $_POST["stage1unlocked"];
	$stage2unlocked = $_POST["stage2unlocked"];
	$maxhealth = $_POST["maxhealth"];
	$healthupgradecost = $_POST["healthupgradecost"];
	$damageupgradecost = $_POST["damageupgradecost"];
	$attackspeedupgradecost = $_POST["attackspeedupgradecost"];
	$healthupgradeindicatoranimationnumber = $_POST["healthupgradeindicatoranimationnumber"];
	$damageupgradeindicatoranimationnumber = $_POST["damageupgradeindicatoranimationnumber"];
	$attackspeedupgradeindicatoranimationnumber = $_POST["attackspeedupgradeindicatoranimationnumber"];
	
	//Sanitizing the received values
	$username = filter_var($username,FILTER_SANITIZE_STRING);
	$password = filter_var($password,FILTER_SANITIZE_STRING);
	$points = filter_var($points,FILTER_SANITIZE_STRING);
	$coins = filter_var($coins,FILTER_SANITIZE_STRING);
	$bulletfiringcooldown = filter_var($bulletfiringcooldown,FILTER_SANITIZE_STRING);
	$attackpower = filter_var($attackpower,FILTER_SANITIZE_STRING);
	$stage1unlocked = filter_var($stage1unlocked,FILTER_SANITIZE_STRING);
	$stage2unlocked = filter_var($stage2unlocked,FILTER_SANITIZE_STRING);
	$maxhealth = filter_var($maxhealth,FILTER_SANITIZE_STRING);
	$healthupgradecost = filter_var($healthupgradecost,FILTER_SANITIZE_STRING);
	$damageupgradecost = filter_var($damageupgradecost,FILTER_SANITIZE_STRING);
	$attackspeedupgradecost = filter_var($attackspeedupgradecost,FILTER_SANITIZE_STRING);
	$healthupgradeindicatoranimationnumber= filter_var($healthupgradeindicatoranimationnumber,FILTER_SANITIZE_STRING);
	$damageupgradeindicatoranimationnumber = filter_var($damageupgradeindicatoranimationnumber,FILTER_SANITIZE_STRING);
	$attackspeedupgradeindicatoranimationnumber = filter_var($attackspeedupgradeindicatoranimationnumber,FILTER_SANITIZE_STRING);
	
	//Remove whitespaces from the received values (tabs/spaces/newlines)
	$password = preg_replace("/\s+/", "", $password);
	$username = preg_replace("/\s+/", "", $username);
	
	//Remove characters other than numbers from received values
	$points = preg_replace("/[^0-9]/", "",$points);
	$coins = preg_replace("/[^0-9]/", "",$coins);
	$bulletfiringcooldown = preg_replace("/[^0-9.]/", "",$bulletfiringcooldown);
	$attackpower = preg_replace("/[^0-9]/", "",$attackpower);
	$stage1unlocked = preg_replace("/[^0-9]/", "",$stage1unlocked);
	$stage2unlocked = preg_replace("/[^0-9]/", "",$stage2unlocked);
	$maxhealth = preg_replace("/[^0-9]/", "",$maxhealth);
	$healthupgradecost = preg_replace("/[^0-9]/", "",$healthupgradecost);
	$damageupgradecost = preg_replace("/[^0-9]/", "",$damageupgradecost);
	$attackspeedupgradecost = preg_replace("/[^0-9]/", "",$attackspeedupgradecost);
	$healthupgradeindicatoranimationnumber = preg_replace("/[^0-9]/", "",$healthupgradeindicatoranimationnumber);
	$damageupgradeindicatoranimationnumber = preg_replace("/[^0-9]/", "",$damageupgradeindicatoranimationnumber);
	$attackspeedupgradeindicatoranimationnumber = preg_replace("/[^0-9]/", "",$attackspeedupgradeindicatoranimationnumber);
	
	if($password == NULL OR $password == "")  {
		$nullValueFound = True;
	}
	if($username == NULL OR $username == "") {
		$nullValueFound = True;
	}
	if($points == NULL OR $points == "")  {
		$nullValueFound = True;
	}
	if($coins == NULL OR $coins == "") {
		$nullValueFound = True;
	}
	if($bulletfiringcooldown == NULL OR $bulletfiringcooldown == "")  {
		$nullValueFound = True;
	}
	if($attackpower == NULL OR $attackpower == "") {
		$nullValueFound = True;
	}
	if($stage1unlocked == NULL OR $stage1unlocked == "") {
		$nullValueFound = True;
	}
	if($stage2unlocked == NULL OR $stage2unlocked == "") {
		$nullValueFound = True;
	}
	if($maxhealth == NULL OR $maxhealth == "") {
		$nullValueFound = True;
	}
	if($healthupgradecost == NULL OR $healthupgradecost == "") {
		$nullValueFound = True;
	}
	if($damageupgradecost == NULL OR $damageupgradecost == "") {
		$nullValueFound = True;
	}
	if($attackspeedupgradecost == NULL OR $attackspeedupgradecost == "") {
		$nullValueFound = True;
	}
	if($healthupgradeindicatoranimationnumber == NULL OR $healthupgradeindicatoranimationnumber == "") {
		$nullValueFound = True;
	}
	if($damageupgradeindicatoranimationnumber == NULL OR $damageupgradeindicatoranimationnumber == "") {
		$nullValueFound = True;
	}
	if($attackspeedupgradeindicatoranimationnumber == NULL OR $attackspeedupgradeindicatoranimationnumber == "") {
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
			
			//Update values received from the game in the database 
			$stmt = $conn->prepare('UPDATE users SET points = :points, coins = :coins, attackpower = :attackpower, bulletfiringcooldown = :bulletfiringcooldown, stage1unlocked = :stage1unlocked, stage2unlocked = :stage2unlocked, maxhealth = :maxhealth, healthupgradecost = :healthupgradecost, damageupgradecost = :damageupgradecost, attackspeedupgradecost = :attackspeedupgradecost, healthupgradeindicatoranimationnumber = :healthupgradeindicatoranimationnumber, damageupgradeindicatoranimationnumber = :damageupgradeindicatoranimationnumber, attackspeedupgradeindicatoranimationnumber = :attackspeedupgradeindicatoranimationnumber  WHERE username = :username AND password = :password');
			$stmt->bindParam(':password',$password);
			$stmt->bindParam(':username',$username);
			$stmt->bindParam(':points',$points);
			$stmt->bindParam(':coins',$coins);
			$stmt->bindParam(':attackpower',$attackpower);
			$stmt->bindParam(':bulletfiringcooldown',$bulletfiringcooldown);
			$stmt->bindParam(':stage1unlocked',$stage1unlocked);
			$stmt->bindParam(':stage2unlocked',$stage2unlocked);
			$stmt->bindParam(':maxhealth',$maxhealth);
			$stmt->bindParam(':healthupgradecost',$healthupgradecost);
			$stmt->bindParam(':damageupgradecost',$damageupgradecost);
			$stmt->bindParam(':attackspeedupgradecost',$attackspeedupgradecost);
			$stmt->bindParam(':healthupgradeindicatoranimationnumber',$healthupgradeindicatoranimationnumber);
			$stmt->bindParam(':damageupgradeindicatoranimationnumber',$damageupgradeindicatoranimationnumber);
			$stmt->bindParam(':attackspeedupgradeindicatoranimationnumber',$attackspeedupgradeindicatoranimationnumber);
			
			if($stmt->execute()) {
				$response->success = "True";
				$response->message = "Game values updated successfully.";
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
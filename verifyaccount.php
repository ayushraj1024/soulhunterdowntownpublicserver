<?php
require_once('config.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$response = NULL; //This variable holds the JSON data which we will send to the client

if($_SERVER['REQUEST_METHOD'] === 'POST') {
	
	//Opening the connection to the database
	try {
		$conn = new PDO("mysql:host=$DATABASEHOSTNAME;dbname=$DATABASENAME", $DATABASEUSERNAME, $DATABASEPASSWORD);
		// set the PDO error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$response->message = "Connection succeeded.";
		} catch(PDOException $e) {
				$response->message = "Connection failed: " . $e->getMessage();
	}
	
}
else {
		$response->message = "Invalid request";
		echo (json_encode($response));
}
?>
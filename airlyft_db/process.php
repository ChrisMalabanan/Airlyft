<?php 
require_once('config.php');

if (isset($_POST)) {
	$username     = $_POST['username'];
	$email        = $_POST['email'];
	$phonenumber  = $_POST['phonenumber'];
	$password     = password_hash($_POST['password'], PASSWORD_DEFAULT);

	try {
		$sql = "INSERT INTO users (username, email, phonenumber, password) VALUES (?, ?, ?, ?)";
		$stmt = $db->prepare($sql);
		$result = $stmt->execute([$username, $email, $phonenumber, $password]);

		if ($result) {
			echo "Successfully saved.";
		} else {
			echo "Failed to save user.";
		}
	} catch (PDOException $e) {
		echo "Database Error: " . $e->getMessage();
	}
}

<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
</head>
<body>

<?php

// Create connection
include('../../database.php');
$conn = getConnection();

if($_SESSION["KID"]==NULL) {
	
	header('Location: \Index.php');
	
} else {
	$sql = "DELETE FROM kontakte_sportstaette WHERE KPID=".$_SESSION["KID"];
	$result = $conn->query($sql);
	
	$sql = "DELETE FROM kontakte_unterkunft WHERE KPID=".$_SESSION["KID"];
	$result = $conn->query($sql);
	
	if($result===false) {echo "FEHLER: ".$conn->error;}
	else {
		$sqlneu="DELETE FROM kontaktpersonen WHERE ID=".$_SESSION["KID"];
		$resultneu = $conn->query($sqlneu);
		
		if($resultneu===false) {echo "FEHLER: ".$conn->error;}
		else {
			session_unset();
			session_destroy();
		
			header('Location: \Index.php'); 
		}
			
	}
}

$conn->close();
?>



</body>
</html>
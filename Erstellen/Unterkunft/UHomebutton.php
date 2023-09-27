<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
</head>
<body>

<?php

include('../../database.php');
$conn = getConnection();

if($_SESSION["UID"]==NULL) {
	
	header('Location: \Index.php');
	
} else {
		$sql = "DELETE FROM ubesitzt_bool WHERE UID=".$_SESSION["UID"];
		$result = $conn->query($sql);
		
		$sql = "DELETE FROM ubesitzt_char WHERE UID=".$_SESSION["UID"];
		$result = $conn->query($sql);
		
		$sql = "DELETE FROM ubesitzt_int WHERE UID=".$_SESSION["UID"];
		$result = $conn->query($sql);
	
	

	
	if($result===false) {echo "FEHLER: ".$conn->error;}
	else {
		$sql="DELETE FROM unterkunft where ID=".$_SESSION["UID"];
		$result = $conn->query($sql);
		
		session_unset();
		session_destroy();
		
		header('Location: \Index.php');
			
		}
}

$conn->close();
?>



</body>
</html>
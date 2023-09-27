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

if($_SESSION["SID"]==NULL) {
	
	header('Location: ../../Index.php');
	
} else {
		$sql = "DELETE FROM ssbesitzt_bool WHERE SID=".$_SESSION["SID"];
		$result = $conn->query($sql);
		
		$sql = "DELETE FROM ssbesitzt_char WHERE SSID=".$_SESSION["SID"];
		$result = $conn->query($sql);
		
		$sql = "DELETE FROM ssbesitzt_int WHERE SSID=".$_SESSION["SID"];
		$result = $conn->query($sql);
		
		$sql = "DELETE FROM eignungsass WHERE SSID=".$_SESSION["SID"];
		$result = $conn->query($sql);
	
	

	
	if($result===false) {echo "FEHLER: ".$conn->error;}
	else {
		$sql="DELETE FROM sportstaette where ID=".$_SESSION["SID"];
		$result = $conn->query($sql);
		
		session_unset();
		session_destroy();
		
		header('Location: ../../Index.php');
			
		}
}
$conn->close();
?>



</body>
</html>
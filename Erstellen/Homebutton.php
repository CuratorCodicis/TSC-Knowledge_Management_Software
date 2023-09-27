<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
</head>
<body>

<?php
	session_unset();
	session_destroy();
		
	header('Location: ../Index.php');
?>

</body>
</html>
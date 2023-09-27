<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
<title>Unterkunft Fertig</title>
  <meta charset="UTF-8">
  <LINK rel="stylesheet" href="\general.css">
  <LINK rel="stylesheet" href="\colors.css">
 <LINK rel="stylesheet" href="\Icons/css/all.css">
</head>
<style>
.font{
	font-size:100%;
	font face:verdana;
	margin-left: auto;
	margin-right:auto;
	margin-top:40px;
	width: 50%;
	height:auto;
    background-color: #f1f1f1;
	border: 1px solid #ccc;

}
.Ramen{
	border: 2px solid white;
	margin-left: auto;
	margin-right:auto;
	margin-top:5%;
	margin-bottom:5%;
	width: 90%;
	height:auto;
}
body{
	 text-align: center; 
 }
  table, th, td {
	margin-left: auto;
	margin-right: auto;
	margin-top: 5%;
	margin-bottom: 5%;
    border: none;
    border-collapse: collapse;
}

td {
    text-align: left; 	
}
th {
    text-align: left;
}
</style>
<body>

<script>
function leave_site() {
	window.location.replace("../Homebutton.php");
}
</script>

<div class="color" id="colorU1">
<button type="button" onclick="leave_site()" class="HomeB">Home</button></br></br>
<h1>Unterkunft</h1></br></br>
</div>
<div class="circle2"><i style='font-size:50px;margin-top:35%;color:grey;' class='fas'>&#xf65e;</i></div>

<?php
include('../../database.php');
$conn = getConnection();

if($_SESSION["UID"]==NULL) {
	header('Location: \Index.php');
}

//echo "Unterkunft: <br>";
echo '<div class="font"><div class="Ramen">
</br>
	<table style="width:40%">
<colgroup>
    <col style="width: 50%" />
    <col style="width: 50%" />
  </colgroup>';

$sql = "SELECT * FROM unterkunft where ID=".$_SESSION["UID"];
$result = $conn -> query($sql);

if($result === false) {
	echo "FEHLER: ".$conn->error;
} else {
	while($row=$result->fetch_assoc()){
		echo '<tr><th>Name:</th><td>'.$row["Name"].'</td></tr>';
		if(!is_null($row["Telefonnummer"])) {echo '<tr><th>Telefonnummer:</th><td> '.$row["Telefonnummer"].'</td></tr>';}
		if(!is_null($row["MailAdresse"])) {echo '<tr><th>E-Mail-Adresse:</th><td> '.$row["MailAdresse"].'</td></tr>';}
		if(!is_null($row["Internetseite"])) {echo '<tr><th>Internetseite:</th><td> '.$row["Internetseite"].'</td></tr>';}
		echo '<tr><th>Straße:</th><td> '.$row["Strasse"].'</td></tr>';
		if(!is_null($row["Hausnummer"])) {echo '<tr><th>Hausnummer:</th><td> '.$row["Hausnummer"].'</td></tr>';}
		if(!is_null($row["Postleitzahl"])) {echo '<tr><th>Postleitzahl:</th><td> '.$row["Postleitzahl"].'</td></tr>';}
		echo '<tr><th>Ort:</th><td> '.$row["Ort"].'</td></tr>';
		if(!is_null($row["Kommentar"])) {echo '<tr><th>Kommentar:</th><td> '.$row["Kommentar"].'</td></tr>';}
	}
}

echo '<tr style="height: 20px"><td></td></tr>';

//bool Attribute
$sql = "SELECT * FROM ubesitzt_bool WHERE UID=".$_SESSION["UID"];
$result = $conn -> query($sql);

if($result === false) {
	echo "FEHLER: ".$conn->error;
} else {
	while($row=$result->fetch_assoc()){
		if($row["Wert"]==1) {
			echo '<tr><th>'.$row["AName"].':</th><td> Ja </td></tr>';
		} else {
			echo '<tr><th>'.$row["AName"].':</th><td> Nein </td></tr>';	//wenn wir auch negative bool Aussagen haben - wird zuzeit nicht benötigt
		}

	}
}

//char Attribute
$sql = "SELECT * FROM ubesitzt_char WHERE UID=".$_SESSION["UID"];
$result = $conn -> query($sql);

if($result === false) {
	echo "FEHLER: ".$conn->error;
} else {
	while($row=$result->fetch_assoc()){
		echo '<tr><th>'.$row["AName"].':</th><td> '.$row["Wert"].'</td></tr>';
	}
}

//int Attribute
$sql = "SELECT * FROM ubesitzt_int WHERE UID=".$_SESSION["UID"];
$result = $conn -> query($sql);

if($result === false) {
	echo "FEHLER: ".$conn->error;
} else {
	while($row=$result->fetch_assoc()){
		echo '<tr style="height: 10px"><td></td></tr>';
		echo '<tr><th>'.$row["AName"].':</th><td> '.$row["Wert"].'</td></tr>';
	}
}
echo '</table></div></div>';

$conn->close();
?>

</br></br>
<form action ="UnterkunftEnde.php" method="Post">
<input type = submit value="Anlegen beenden">
</form>

<?php
if($_SERVER["REQUEST_METHOD"]=="POST") {
	session_unset();
	session_destroy();
	
	header("Location: \Verwaltung.php");
}

?>

</body>
</html>

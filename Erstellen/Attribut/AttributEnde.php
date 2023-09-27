<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
<title>Attribut Fertig</title>
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

<div class="color" id="colorSuche">
<button type="button" onclick="leave_site()" class="HomeB">Home</button></br></br>
<h1>Attribut</h1></br></br>
</div>
<div class="circle2"><i style='font-size:50px;margin-top:35%;color:grey;' class='fas'>&#xf65e;</i></div>

<?php
include('../../database.php');
$conn = getConnection();

if($_SESSION["att"]==NULL) {
	header('Location: \Index.php');
}

echo '<div class="font"><div class="Ramen">
</br>
	<table style="width:40%">
<colgroup>
    <col style="width: 50%" />
    <col style="width: 50%" />
  </colgroup>';

$object = substr($_SESSION["att"],0, 1);
$att = substr($_SESSION["att"], 1);
$charc = false;

if($object == "U"){
	$sql = 'SELECT * FROM attributefuerunterkunft WHERE AName="'.$att.'";';
	$result = $conn -> query($sql);

	if($result === false) {
		echo "FEHLER: ".$conn->error;
	} else {
		while($row=$result->fetch_assoc()){
			echo '<tr><th>Name:</th><td>'.$row["AName"].'</td></tr>';
			echo '<tr><th>Eingabetyp:</th><td>';
			if($row["Typ"]=="charc"){echo 'Auswahlwerte'; $charc = true;}
			if($row["Typ"]=="int"){echo 'Zahlenwerte';}
			if($row["Typ"]=="bool"){echo 'Wahrheitswerte';}
			echo '</td></tr>';
		}
	}


	
	if($charc){
		$sql = 'SELECT * FROM attributefuerunterkunftauswahlwerte WHERE AName="'.$att.'";';
		$result = $conn -> query($sql);
		
		if($result === false) {
			echo "FEHLER: ".$conn->error;
		} else {
			echo '<tr><th>Auswahlwerte:</th><td>';
			
			while($row=$result->fetch_assoc()){
				echo $row["Wert"].'<br>';
			}
			
			echo '</td></tr>';
		}
	}
}
if($object == "S") {
	$sql = 'SELECT * FROM attributefuersportstaette WHERE AName="'.$att.'";';
	$result = $conn -> query($sql);

	if($result === false) {
		echo "FEHLER: ".$conn->error;
	} else {
		while($row=$result->fetch_assoc()){
			echo '<tr><th>Name:</th><td>'.$row["AName"].'</td></tr>';
			echo '<tr><th>Eingabetyp:</th><td>';
			if($row["Typ"]=="charc"){echo 'Auswahlwerte'; $charc = true;}
			if($row["Typ"]=="int"){echo 'Zahlenwerte';}
			if($row["Typ"]=="bool"){echo 'Wahrheitswerte';}
			echo '</td></tr>';
		}
	}


	
	if($charc){
		$sql = 'SELECT * FROM attributefuersportstaetteauswahlwerte WHERE AName="'.$att.'";';
		$result = $conn -> query($sql);
		
		if($result === false) {
			echo "FEHLER: ".$conn->error;
		} else {
			echo '<tr><th>Auswahlwerte:</th><td>';
			
			while($row=$result->fetch_assoc()){
				echo $row["Wert"].'<br>';
			}
			
			echo '</td></tr>';
		}
	}
		
	echo '<tr style="height: 10px"><td></td></tr>';
	
	$Keine = true;
	
	$sql = 'SELECT * FROM zuordnungsaa WHERE AName="'.$att.'";';
	$result = $conn -> query($sql);
	
	if($result === false) {
		echo "FEHLER: ".$conn->error;
	} else {
		while($row=$result->fetch_assoc()){
			$Keine = false;
			echo '<tr><th>Zuordnung:</th><td>'.$row["SAName"].'</td></tr>';
		}
	}
	
	if($Keine) {
		echo '<tr><th>Zuordnung:</th><td>Keine Zuordnung</td></tr>';
	}
}

echo '</table></div></div>';

$conn->close();
?>

</br></br>
<form action ="AttributEnde.php" method="Post">
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
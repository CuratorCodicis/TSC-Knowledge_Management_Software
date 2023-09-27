<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
<title>Kontaktperson Fertig</title>

  <meta charset="UTF-8">
  <LINK rel="stylesheet" href="\general.css">
  <LINK rel="stylesheet" href="\colors.css">
 <LINK rel="stylesheet" href="\Icons/css/all.css">
</head>
<style>
.fontbox{
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
	margin-top: 10%;
	margin-bottom: 10%;
    border: none;
    border-collapse: collapse;
}
td {
    text-align: left; 	
}
th {
    text-align: left;
}
tr{
	padding: 0 10px;
}

</style>
<body>

<script>
function leave_site() {
	window.location.replace("../Homebutton.php");
}
</script>

<div class="color" id="colorK3">
<button type="button" onclick="leave_site()" class="HomeB">Home</button></br></br>
<h1>Kontaktperson</h1></br></br>
</div></br>
<div class="circle2"><i style='font-size:50px;margin-top:35%;color:grey;' class='fas'>&#xf65e;</i></div>
<?php

include('../../database.php');
$conn = getConnection();

if($_SESSION["KID"]==NULL) {
	header('Location: \Index.php');
}
else
{
	$KPID = $_SESSION["KID"];
}
$unterkunftDabei = false;
$sportstaetteDabei = false;
if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST["IDs"])){
	$decode = json_decode($_POST["IDs"]);
	for($i = 0; $i < count($decode);$i++)
	{
		$typ = substr($decode[$i],0,1);
		$ID = substr($decode[$i],1,strlen($decode[$i])-1);
		$sql = "";
		if ($typ == "U")
		{
			$sql = "INSERT INTO kontakte_unterkunft(KPID,UID) VALUES(".$KPID.",".$ID.");";
			$unterkunftDabei = true;
		}
		if ($typ == "S")
		{
			$sql = "INSERT INTO kontakte_sportstaette(KPID,SSID) VALUES(".$KPID.",".$ID.");";
			$sportstaetteDabei = true;
		}
		try
		{
			$er = $conn -> query($sql);
			if ($er === FALSE)
			{
				echo $conn -> error;
			}
		}
		catch(Exception $e)
		{
			
		}
	}
}
echo '<div class="fontbox"><div class="Ramen">
	<table style="width:70%">
	<colgroup>
    <col style="width: 50%" />
    <col style="width: 50%" />
	</colgroup>';

$sql = "SELECT * FROM kontaktpersonen WHERE ID=".$_SESSION["KID"];
$result = $conn -> query($sql);

if($result === false) {
	echo "FEHLER: ".$conn->error;
} else {
	while($row=$result->fetch_assoc()){
		if(!is_null($row["Vorname"])) {echo '<tr><th>Vorname:</th><td> '.$row["Vorname"].'</td></tr>';}
		echo '<tr><th>Nachname:</th><td>'.$row["Nachname"].'</td></tr>';
		if(!is_null($row["Telefonnummer"])) {echo '<tr></br><th>Telefonnummer:</th><td> '.$row["Telefonnummer"].'</td></br></tr>';}
		if(!is_null($row["Mobilnummer"])) {echo '<tr><th>Mobilnummer:</th><td> '.$row["Mobilnummer"].'</td></tr>';}
		if(!is_null($row["MailAdresse"])) {echo '<tr><th>E-Mail-Adresse:</th><td> '.$row["MailAdresse"].'</td></tr>';}
		if(!is_null($row["Fax"])) {echo '<tr><th>Faxnummer:</th><td> '.$row["Fax"].'</td></tr>';}
		if(!is_null($row["Funktion"])) {echo '<tr><th>Funktion:</th><td> '.$row["Funktion"].'</td></tr>';}
		if(!is_null($row["Kommentar"])) {echo '<tr><th>Kommentar:</th><td> '.$row["Kommentar"].'</td></tr>';}
	}
}

if ($unterkunftDabei)
{
	//zugeordnete Unterkünfte
	$sql = "SELECT * FROM kontakte_unterkunft WHERE KPID=".$_SESSION["KID"];
	$result = $conn -> query($sql);

	echo '<tr style="height: 10px"><td></td></tr>';
	echo '<tr><th>Zugeordnete Unterkünfte: </th> <td>';
	if($result === false) {
		echo "FEHLER: ".$conn->error;
	} else {
		while($row=$result->fetch_assoc()){
			$sql2 = "SELECT * FROM unterkunft WHERE ID=".$row["UID"];
			$result2 = $conn -> query($sql2);
			
			if($result2 === false) {
				echo "FEHLER: ".$conn->error;
			} else {
				while($row2=$result2->fetch_assoc()){
					echo $row2["Name"].'<br>';
				}
			}
		}
	}
	echo '</td> </tr> <br>';
}
if ($sportstaetteDabei)
{
	//zugeordnete Sportstätten
	$sql = "SELECT * FROM kontakte_sportstaette WHERE KPID=".$_SESSION["KID"];
	$result = $conn -> query($sql);

	echo '<tr style="height: 10px"><td></td></tr>';
	echo '<tr><th>Zugeordnete Sportstätte: </th> <td>';
	if($result === false) {
		echo "FEHLER: ".$conn->error;
	} else {
		while($row=$result->fetch_assoc()){
			$sql2 = "SELECT * FROM sportstaette WHERE ID=".$row["SSID"];
			$result2 = $conn -> query($sql2);
			
			if($result2 === false) {
				echo "FEHLER: ".$conn->error;
			} else {
				while($row2=$result2->fetch_assoc()){
					echo $row2["Name"].'<br>';
				}
			}
		}
	}
	echo '</td> </tr>';
}
echo '</table></div></div>';


$conn->close();

?>

</br>
<form action ="KontaktEnde.php" method="Post">
<input type = "submit" value="Anlegen beenden">
<input type="hidden" name="delegate" value="1">
</form>

<?php
if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST['delegate']) && $_POST['delegate'] == "1") {
	session_unset();
	session_destroy();
	
	header("Location: \Verwaltung.php");
}

?>

</body>
</html>
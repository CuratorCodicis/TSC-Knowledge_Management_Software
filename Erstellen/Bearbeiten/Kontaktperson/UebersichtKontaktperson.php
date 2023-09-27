<?php
session_start();
?>

<html>
<head> 
  <title>Kontakt</title>
  <meta charset="UTF-8">
  <LINK rel="stylesheet" href="\general.css">
  <LINK rel="stylesheet" href="\colors.css">
  <LINK rel="stylesheet" href="../../Anlegenstyle.css">
   <LINK rel="stylesheet" href="\Icons/css/all.css">
   <style>
	 a:link, a:visited{
	width: 70%;
    background-color: #3c3c3c;
    color: white;
    padding: 9px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
	margin: 8px 0;
    border-radius: 4px;
    cursor: pointer;
	}

	a:hover, a:active {
		background-color: #A9A9A9;
	}
	  .buttons, th, td {
		border: none;
		border-collapse: collapse;
		margin-left: auto;
		margin-right: auto;
		border:none;
	}
	  #ramen{
	  width:50%;
	  margin-left:2%;
	  text-align:left;
	  
  }
</style>
	<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
</head>
<body> 

<script>
function leave_site() {
	window.location.replace("../../Homebutton.php");
}
</script>

<div class="color" id="colorK1">
<button type="button" onclick="leave_site()" class="HomeB">Home</button></br></br>
<h1><b>Kontakt<b></h1></br></br>
</div>
<div class="circle"><i style='font-size:50px;margin-top:35%;color:grey;' class='far'>&#xf044;</i></div>

<?php
include('../../../database.php');
$conn = getConnection();

//check required fields
$NachnameErr = "";
$Vorname = $Nachname = $TelNum = $EMail = $Mobil = $Fax = $Funktion = $Kommentar = "";
$VornameP = $NachnameP = $TelNumP = $EMailP = $MobilP = $FaxP = $FunktionP = $KommentarP = "";


if($_SERVER["REQUEST_METHOD"]=="POST") {
	if(empty($_POST["Vorname"])){$Vorname = "NULL";} else {$Vorname = "'".$_POST["Vorname"]."'";$VornameP = $_POST["Vorname"];}
	if(empty($_POST["Nachname"])) {$NachnameErr = "Nachname wird benötigt";} else {$Nachname = "'".$_POST["Nachname"]."'";$NachnameP = $_POST["Nachname"];}
	if(empty($_POST["TelNum"])){$TelNum = "NULL";} else {$TelNum = "'".$_POST["TelNum"]."'";$TelNumP = $_POST["TelNum"];}
	if(empty($_POST["EMail"])){$EMail = "NULL";} else {$EMail = "'".$_POST["EMail"]."'";$EMailP = $_POST["EMail"];}
	if(empty($_POST["Mobil"])){$Mobil = "NULL";} else {$Mobil = "'".$_POST["Mobil"]."'";$MobilP = $_POST["Mobil"];}
	if(empty($_POST["Fax"])){$Fax = "NULL";} else {$Fax = "'".$_POST["Fax"]."'";$FaxP = $_POST["Fax"];}
	if(empty($_POST["Funktion"])){$Funktion = "NULL";} else {$Funktion = "'".$_POST["Funktion"]."'";$FunktionP = $_POST["Funktion"];}
	if(empty($_POST["Kommentar"])){$Kommentar = "NULL";} else {$Kommentar = "'".$_POST["Kommentar"]."'";$KommentarP = $_POST["Kommentar"];}
}

if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST["ID"])) {
	$_SESSION["KID"] = $_POST["ID"];
}

$ID = $_SESSION["KID"];


//DB Operationen

$errorPflicht = false;
if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"])) {
	
	if(empty($_POST["Nachname"]))
	{
		echo '<span class="error">Nicht alle relevanten Felder wurden ausgefüllt</span>';
		$errorPflicht = true;
	}
	else
	{
		$sql = 'UPDATE kontaktpersonen 
				SET Vorname='.$Vorname.', Nachname='.$Nachname.', Telefonnummer='.$TelNum.', Mobilnummer='.$Mobil.', Fax='.$Fax.', MailAdresse='.$EMail.', Funktion='.$Funktion.', Kommentar='.$Kommentar.' 
				WHERE ID='.$ID.';';
				
		$result = $conn->query($sql);
		
		if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
		
		//else: Zuordnungen von KP zu Unterkunft und Sportstaette löschen und neu einfügen?
	}
}

//=============================================================================================
//form

echo '
<div>
<form action ="UebersichtKontaktperson.php" method="Post">
<table style="width:100%">
<colgroup>
    <col style="width: 30%" />
    <col style="width: 40%" />
    <col style="width: 30%" />
</colgroup> ' ;

$sql = "SELECT * FROM kontaktpersonen where ID=".$ID.";";
$result = $conn -> query($sql);

if($result === false) {
	echo "FEHLER: ".$conn->error;
} else {
	while($row=$result->fetch_assoc()){
		if(!is_null($row["Vorname"])) {echo '<tr><th>Vorname</th><td><input type="text" name="Vorname" value="';
			if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"])) {echo $_POST["Vorname"].'" ';} else {echo $row["Vorname"].'" ';}
			echo '></td></tr>';}
			
		echo '<tr><th>Nachname*</th><td> <input type="text" name="Nachname" value="';
		if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"])){echo $_POST["Nachname"].'" ';} else {echo $row["Nachname"].'" ';}
		if($errorPflicht == true){echo "border: 2px solid red;";}
		echo '></td></tr>';
		
		if(!is_null($row["Telefonnummer"])) {echo '<tr><th>Telefonnummer</th><td><input type="text" name="TelNum" value="';
			if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"])) {echo $_POST["TelNum"].'" ';} else {echo $row["Telefonnummer"].'" ';}
			echo '></td></tr>';}
		
		if(!is_null($row["Mobilnummer"])) {echo '<tr><th>Mobilnummer</th><td><input type="text" name="Mobil" value="';
			if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"])) {echo $_POST["Mobil"].'" ';} else {echo $row["Mobilnummer"].'" ';}
			echo '></td></tr>';}
		
		if(!is_null($row["Fax"])) {echo '<tr><th>Fax</th><td><input type="text" name="Fax" value="';
			if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"])) {echo $_POST["Fax"].'" ';} else {echo $row["Fax"].'" ';}
			echo '></td></tr>';}
		
		if(!is_null($row["MailAdresse"])) {echo '<tr><th>E-Mail-Adresse</th><td><input type="text" name="EMail" value="';
			if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"])) {echo $_POST["EMail"].'" ';} else {echo $row["MailAdresse"].'" ';}
			echo '></td></tr>';}
		
		if(!is_null($row["Funktion"])) {echo '<tr><th>Funktion</th><td><input type="text" name="Funktion" value="';
			if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"])) {echo $_POST["Funktion"].'" ';} else {echo $row["Funktion"].'" ';}
			echo '></td></tr>';}
			
		if(!is_null($row["Kommentar"])) {echo '<tr><th>Kommentar</th><td><textarea rows="4" cols="50" name="Kommentar">';
			if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"])) {echo $_POST["Kommentar"];} else {echo $row["Kommentar"];}
			echo '</textarea></td></tr>';}
	}
}

//Zurodnung KP zu Unterkunft und Sportstaette anzeigen

$unterkunftDabei = false;
$sportstaetteDabei = false;

$sql = "SELECT * FROM kontakte_unterkunft WHERE KPID=".$_SESSION["KID"];
$result = $conn -> query($sql);

while($row=$result->fetch_assoc()){
	$unterkunftDabei = true;
}

$sql = "SELECT * FROM kontakte_sportstaette WHERE KPID=".$_SESSION["KID"];
$result = $conn -> query($sql);

while($row=$result->fetch_assoc()){
	$sportstaetteDabei = true;
}


if($unterkunftDabei){
	//zugeordnete Unterkünfte
	$sql = "SELECT * FROM kontakte_unterkunft WHERE KPID=".$_SESSION["KID"];
	$result = $conn -> query($sql);

	echo '<tr style="height: 10px"><td></td></tr>';
	echo '<tr><th style="vertical-align:top">Zugeordnete Unterkünfte </th> <td><div id="ramen"><ul>';
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
					echo '<li>'.$row2["Name"].'</li><br>';
				}
			}
		}
	}
	echo '</ul><div id="ramen"></td> </tr> <br>';
}

if ($sportstaetteDabei)
{
	//zugeordnete Sportstätten
	$sql = "SELECT * FROM kontakte_sportstaette WHERE KPID=".$_SESSION["KID"];
	$result = $conn -> query($sql);

	echo '<tr style="height: 10px"><td></td></tr>';
	echo '<tr><th style="vertical-align:top">Zugeordnete Sportstätte </th> <td><div id="ramen"><ul>';
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
					echo '<li>'.$row2["Name"].'</li><br>';
				}
			}
		}
	}
	echo '</ul></div></td></tr>';
}

echo '<tr style="height: 20px"><td></td></tr>';

echo '</table>';

echo '<table class="buttons" style="width:90%">
<colgroup>
    <col style="width: 25%" />
    <col style="width: 25%" />
    <col style="width: 25%" />
	<col style="width: 25%" />
</colgroup><tr>
<td><button class="reset" type="reset" value="Reset" style="width:70%;padding:9px;">Änderungen zurücksetzen</button></td>
<!-- weiterleitung zu Details -->
<td><a href="DatenBKontaktperson.php">Alle Daten bearbeiten</a></td>
<td><a href="LoeschenKontaktperson.php" id="deleteB">Kontaktperson löschen</a></td>
<td><input type=submit value="Eingabe speichern" id="submitB" style="margin-right:0;float:none;width:70%;padding:9px;font-family: Verdana, Helvetica, Arial, sans-serif;"></td></tr>
</table>
</form></br></br>';



echo '</div>';
$conn->close();
?>





<script>
//frage nur, falls Aenderungen und nicht submittet
function sicher(e) {
	if(!window.btn_clicked && changed){
        e.preventDefault();
    }
}
document.getElementById("submitB").onclick = function(){
    window.btn_clicked = true;
};
document.getElementById("deleteB").onclick = function(){
    window.btn_clicked = true;
};

//tracke Aenderungen
var changed = false;
var inputs = document.getElementsByTagName('input')
for (var i = 0; i < inputs.length; i++) {
	inputs[i].addEventListener("click", function(){changed = true});
}
inputs = document.getElementsByTagName('select')
for (var i = 0; i < inputs.length; i++) {
	inputs[i].addEventListener("click", function(){changed = true});
}
inputs = document.getElementsByTagName('textarea')
for (var i = 0; i < inputs.length; i++) {
	inputs[i].addEventListener("click", function(){changed = true});
}

// falls versucht wird, die Seite zu verlassen, warne vor
window.addEventListener('beforeunload', function(e){sicher(e)});
</script>


</body>
</html>
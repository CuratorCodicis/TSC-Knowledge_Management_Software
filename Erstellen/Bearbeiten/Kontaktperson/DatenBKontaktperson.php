<?php
session_start();
?>

<html>
<head>
  <title>Daten</title>
  <meta charset="UTF-8">
  <LINK rel="stylesheet" href="\general.css">
  <LINK rel="stylesheet" href="\colors.css">
  <LINK rel="stylesheet" href="../../Anlegenstyle.css">
  <LINK rel="stylesheet" href="\Icons/css/all.css">
  <style>
  .reset{
	  float:left;
	  margin-left:10%;
  }
  </style>
</head>
<body>

<script>
function leave_site() {
	window.location.replace("../../Homebutton.php");
}
</script>

<div class="color" id="colorK1">
<button type="button" onclick="leave_site()" class="HomeB">Home</button></br></br>
<h1>Kontaktperson bearbeiten</h1></br></br>
</div>
<div class="circle"><i style='font-size:50px;margin-top:35%;color:grey;' class='far'>&#xf044;</i></div>
<?php

include('../../../database.php');
$conn = getConnection();

//check required fields
$NachnameErr = "";
$Vorname = $Nachname = $TelNum = $EMail = $Mobil = $Fax = $Funktion = $Kommentar = "";
$VornameP = $NachnameP = $TelNumP = $EMailP = $MobilP = $FaxP = $FunktionP = $KommentarP = "";
$ID = $_SESSION["KID"];

$sql = 'SELECT * FROM kontaktpersonen WHERE ID='.$ID.';';
$result = $conn->query($sql);

if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}

while ($row=$result->fetch_assoc()) {
	$VornameP = $row["Vorname"];
	$NachnameP = $row["Nachname"];
	$TelNumP = $row["Telefonnummer"];
	$MobilP = $row["Mobilnummer"];
	$FaxP = $row["Fax"];
	$EMailP = $row["MailAdresse"];
	$FunktionP = $row["Funktion"];
	$KommentarP = $row["Kommentar"];
}

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

$errorPflicht = false;
if($_SERVER["REQUEST_METHOD"]=="POST") {
	if(empty($_POST["Nachname"]))
	{
		echo '<span class="error" style="color: red; font-size:1.4em;">Nicht alle relevanten Felder wurden ausgefüllt!</span>';
		$errorPflicht = true;
	}
	else
	{
		$sql = 'UPDATE kontaktpersonen SET Vorname='.$Vorname.', Nachname='.$Nachname.', Telefonnummer='.$TelNum.', Mobilnummer='.$Mobil.', Fax='.$Fax.', MailAdresse='.$EMail.', Funktion='.$Funktion.', Kommentar='.$Kommentar.' WHERE ID='.$ID.';';
		$result = $conn->query($sql);
	
		if ($result === TRUE) 
		{
			echo "New record created successfully";
			
			header('Location: VerbundBKontaktperson.php');
		} 
		else 
		{
		echo "Error: " . $sql . "<br>" . $conn->error;
		}
	}
}

$conn->close();
?>

<div>
<form action ="DatenBKontaktperson.php" method="Post">
<table style="width:100%">
<colgroup>
    <col style="width: 30%" />
    <col style="width: 40%" />
    <col style="width: 30%" />
  </colgroup>
<tr>
<th>Vorname</th><td><input type="text" name="Vorname" value="<?php echo $VornameP;?>"/></td>
</tr>
<tr>
<th>Nachname*</th><td><input type="text" name="Nachname" value="<?php echo $NachnameP;?>" style="<?php if($errorPflicht == true){echo "border: 2px solid red;";}?>"/></td>
</tr>
<tr>
<th>Telefonnummer</th><td><input type="text" name="TelNum" value="<?php echo $TelNumP;?>"/></td>
</tr>
<tr>
<th>E-Mail Adresse</th><td><input type="text" name="EMail" value="<?php echo $EMailP;?>"/></td>
</tr>
<tr>
<th>Mobilnummer</th><td><input type="text" name="Mobil" value="<?php echo $MobilP;?>"/></td>
</tr>
<tr>
<th>Fax</th><td><input type="text" name="Fax" value="<?php echo $FaxP;?>"/></td>
</tr>
<tr>
<th>Funktion</th><td><input type="text" name="Funktion" value="<?php echo $FunktionP;?>"/></td>
</tr>
<tr>
<th></br>Kommentar</th><td></br><textarea rows="4" cols="50" name="Kommentar"><?php echo $KommentarP;?></textarea></td></tr>
</table>

<input id="submitB" type=submit value="Eingabe speichern">
<button class="reset" type="reset" value="Reset">Änderungen zurücksetzten</button>
</br></br></br></br>
</form>
</div>

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
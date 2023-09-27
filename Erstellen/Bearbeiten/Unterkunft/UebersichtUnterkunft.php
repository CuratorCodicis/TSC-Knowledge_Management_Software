<?php
session_start();
require("../../../APIKey.php");
?>

<html>
<head>
  <title>Übersicht</title>
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

  </style>
</head>
<body>

<script>
function leave_site() {
	window.location.replace("../../Homebutton.php");
}
</script>

<div class="color" id="colorU1">
<button type="button" onclick="leave_site()" class="HomeB">Home</button></br></br>
<h1 style="text-align:center;">Unterkunft bearbeiten</h1></br></br>
</div>
<div class="circle"><i style='font-size:50px;margin-top:35%;color:grey;' class='far'>&#xf044;</i></div>


<?php

include('../../../database.php');
$conn = getConnection();

$NameErr = $StrErr = $PlzErr = $OrtErr = $LandErr = "";
$Name = $TelNum = $EMail = $Internet = $Str = $Hausnummer = $Plz = $Ort = $Land = $Kommentar = "";
$NameP = $TelNumP = $EMailP = $InternetP = $StrP = $HausnummerP = $PlzP = $OrtP = $LandP = $KommentarP= "";

if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_SESSION["UID"])) {
	if(empty($_POST["Name"])) {$NameErr = "Name wird benötigt";} else {$Name = "'".$_POST["Name"]."'";$NameP = $_POST["Name"];}
	if(empty($_POST["TelNum"])){$TelNum = "NULL";} else {$TelNum = "'".$_POST["TelNum"]."'";$TelNumP = $_POST["TelNum"];}
	if(empty($_POST["EMail"])){$EMail = "NULL";} else {$EMail = "'".$_POST["EMail"]."'";$EMailP = $_POST["EMail"];}
	if(empty($_POST["Internet"])){$Internet = "NULL";} else {$Internet = "'".$_POST["Internet"]."'";$InternetP = $_POST["Internet"];}
	if(empty($_POST["Str"])) {$StrErr = "Straße wird benötigt";} else {$Str ="'". $_POST["Str"]."'";$StrP =$_POST["Str"];}
	if(empty($_POST["Hausnummer"])){$Hausnummer = "NULL";} else {$Hausnummer = "'".$_POST["Hausnummer"]."'";$HausnummerP = $_POST["Hausnummer"];}
	if(empty($_POST["Plz"])) {$Plz = "NULL";} else {$Plz = "'".$_POST["Plz"]."'";$PlzP = $_POST["Plz"];}
	if(empty($_POST["Ort"])) {$OrtErr = "Ort wird benötigt";} else {$Ort = "'".$_POST["Ort"]."'";$OrtP = $_POST["Ort"];}
	if(empty($_POST["Land"])){$Land = "DEFAULT";} else {$Land = "'".$_POST["Land"]."'";$LandP = $_POST["Land"];}
	if(empty($_POST["Kommentar"])){$Kommentar = "NULL";} else {$Kommentar = "'".$_POST["Kommentar"]."'";$KommentarP = $_POST["Kommentar"];}
}

if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST["ID"])) {
	$_SESSION["UID"] = $_POST["ID"];
}

$ID = $_SESSION["UID"];

//Restrict area to the specific country
$countryCode = 'DE';
if (isset($_POST['Land']) && $_POST['Land'] != "" && !empty($_POST['Land']))
{
	$countryCode = GetLandCode($_POST['Land']);
}


//Datenbank update
$updatedDB = false;
$errorPflicht = false;
if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"])) {
	
	if(empty($_POST["Name"]) || empty($_POST["Str"]) || empty($_POST["Ort"]))
	{
		echo '<span class="error">Nicht alle relevanten Felder wurden ausgefüllt</span>';
		$errorPflicht = true;
	}
	else
	{
		$PLZInner = "";
		if (isset($_POST["Plz"])) $PLZInner = $_POST["Plz"];
		$HNInner = "";
		if (isset($_POST["Hausnummer"])) $HNInner = $_POST["Hausnummer"];
		//Add the geolocation to the Dataset
		$query = http_build_query(
		array(
				'api_key' => GetAPIKey(),
				'text' => $_POST["Str"].' '.$HNInner.' '.$PLZInner.' '.$_POST["Ort"],
				'boundary.country' => $countryCode
				//'layers' => "address"

			)
		);

		if ($countryCode == "0")
		{			
			echo '<span class="error">Das angegebene Land konnte nicht gefunden werden!<br> Mögliche Länder sind am Ende der Seite aufgelistet.</span>';
			$LandErr = "Ungültige Eingabe";
		}
		else
		{
			$options = array('http' =>
				array(
					'method'  => 'GET',
					'header'  => 'Content-type: application/x-www-form-urlencoded'
				)
			);
			//Get result as JSON
			$res = json_decode(file_get_contents('https://api.openrouteservice.org/geocode/search?' . $query, false, stream_context_create($options)));
			if (count($res->{'features'})<= 0)
			{
				echo '<span class="error">Die gewünschte Adresse konnte nicht gefunden werden!</span>';
				$StrErr = "Ungültige Eingabe";
				$OrtErr = "Ungültige Eingabe";
			}
			else
			{
				$i=0;
				$found = 0;
				$error = "";
				for($i = 0;(($found == 0) && ($i < count($res->{'features'}))) ;$i++)
				{
					//Überprüfen auf den LOrt
					//TODO: PLZ fehlt, da von der Angabe nicht zurück gegeben
					//Überprüfen LOrt
					if (isset($res->{'features'}[$i]->{'properties'}->{'locality'}))
					{
						$ROrt = trim($res->{'features'}[$i]->{'properties'}->{'locality'});
						$LOrt = trim($_POST['Ort']);
						$VOrt = false;
						if (strlen($ROrt) > strlen($LOrt))
							$VOrt = strpos($ROrt,$LOrt) !== false;
						else
							$VOrt = strpos($LOrt,$ROrt) !== false;
						//echo $VOrt;
						if ($VOrt == true)			
						{
							//Überprüfen auf die Straße
							if (isset($res->{'features'}[$i]->{'properties'}->{'street'}))
							{							
								$RStr = trim($res->{'features'}[$i]->{'properties'}->{'street'});
								$LStr = trim($_POST['Str']);
								$VStr = false;
								if (strlen($RStr) > strlen($LStr))
									$VStr = strpos($RStr,$LStr) !== false;
								else
									$VStr = strpos($LStr,$RStr) !== false;
								//echo $VStr;
								if ($VStr == true)
								{
									$found = 1;
									//echo "found";
								}
							}
							else 
								$error="Straße";
						
						}
						else if ($error != "Straße")
							$error="Ort";
					}
					else if ($error != "Straße")
						$error="Ort";
				}	
				$i--;//KOMISCH Muss wieder runter gezählt werden, da Schleife anscheinend einen weiter zählt
				//echo $i;
				
				//******
				if ($found == 1)
				{
					$Koords = "'".$res->{'features'}[$i]->{'geometry'}->{'coordinates'}[0].','.$res->{'features'}[$i]->{'geometry'}->{'coordinates'}[1]."'";
					$sql = 'UPDATE unterkunft 
							SET Name='.$Name.', Telefonnummer='.$TelNum.', MailAdresse='.$EMail.', Internetseite='.$Internet.', Strasse='.$Str.', Hausnummer='.$Hausnummer.', Postleitzahl='.$Plz.', Ort='.$Ort.', Land='.$Land.', Koordinaten='.$Koords.', Kommentar='.$Kommentar.' 
							WHERE ID='.$ID.';';
					
					$result = $conn->query($sql);
					
					//Preis Update
					$preis = "";
					//var_dump($_POST);
					
					for($m=1;$m<=3;$m++) {
						if($m!=1) {$preis .= "\\\\";}
						
						
						
						if(isset($_POST["Jahr".$m])) {
							$cleanJahr = str_replace("\\","",$_POST["Jahr".$m]);
							$cleanJahr = str_replace("|","",$cleanJahr);
							
							$preis .= $cleanJahr;
						}
						
						for($n=1;$n<=5;$n++) {
							$preis .= "|";
							if(isset($_POST["Typ".$m."_".$n]) && !is_null($_POST["Typ".$m."_".$n])) {
								$cleanTyp = str_replace("\\","",$_POST["Typ".$m."_".$n]);
								$cleanTyp = str_replace("|","",$cleanTyp);
								
								$preis .= $cleanTyp;
							}
							$preis .= "|";
							if(isset($_POST["Preis".$m."_".$n]) && !is_null($_POST["Preis".$m."_".$n])) {
								$cleanPreis = str_replace("\\","",$_POST["Preis".$m."_".$n]);
								$cleanPreis = str_replace("|","",$cleanPreis);
								
								$preis .= $cleanPreis;
							}
						}
					}
					
					$sql = 'UPDATE unterkunft SET KommentarPreis="'.$preis.'" WHERE ID='.$ID.';';
					$x = $conn->query($sql);
					
					if($x === false) {$fehler=1; echo $conn->error;}
					
					
					
					
					
					if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
					
					else 
					{
						//delete from DB where ID = Session ID
						
						//delete bool
						$sql = 'DELETE FROM ubesitzt_bool WHERE UID='.$ID.';';
						$result = $conn->query($sql);
						
						if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
						
						
						//delete char and charc
						$sql = 'DELETE FROM ubesitzt_char WHERE UID='.$ID.';';
						$result = $conn->query($sql);
						
						if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
						
						
						//delete int
						$sql = 'DELETE FROM ubesitzt_int WHERE UID='.$ID.';';
						$result = $conn->query($sql);
						
						if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
						
						//---------------------------------------------------------------------
						
						//get all bool attributes
						$sql='SELECT AName FROM attributefuerunterkunft WHERE Typ="bool";';
						$result = $conn->query($sql);
						
						if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
						
						while ($row=$result->fetch_assoc()) {
							$ConvertedAName = str_replace(" ","_",$row["AName"]); //Ist notwendig, da das Formular beim übermitteln Freiezeichen durch Unterstriche ersetzt
							if(isset($_POST[$ConvertedAName])&& $_POST[$ConvertedAName]==1) {
								$sql2 = 'INSERT INTO ubesitzt_bool (UID, AName, Wert) 
										VALUES ('.$ID.', "'.$row["AName"].'", '.$_POST[$ConvertedAName].');';
								$result2 = $conn->query($sql2);
								
								if ($result2 === FALSE) {echo "Error: " . $sql2 . "<br>" . $conn->error;}
							}	
						}
						
						//get all int attributes
						$sql='SELECT AName FROM attributefuerunterkunft WHERE Typ="int";';
						$result = $conn->query($sql);
						
						if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
						
						while ($row=$result->fetch_assoc()) {
							$ConvertedAName = str_replace(" ","_",$row["AName"]);
							if(isset($_POST[$ConvertedAName]) && $_POST[$ConvertedAName]!=NULL) {
								$sql2 = 'INSERT INTO ubesitzt_int (UID, AName, Wert) 
										VALUES ('.$ID.', "'.$row["AName"].'", '.$_POST[$ConvertedAName].');';
								$result2 = $conn->query($sql2);
								
								if ($result2 === FALSE) {echo "Error: " . $sql2 . "<br>" . $conn->error;}
							}	
						}
						
						//get all char and charc attributes
						$sql='SELECT AName FROM attributefuerunterkunft WHERE Typ="char" OR Typ="charc";';
						$result = $conn->query($sql);
						
						if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
						
						$j=0;
						
						while ($row=$result->fetch_assoc()) {
							$charArray = array();
							$ConvertedAName = str_replace(" ","_",$row["AName"]);
							while(isset($_POST[$ConvertedAName.$j])) {
								if(!in_array($_POST[$ConvertedAName.$j], $charArray) && $_POST[$ConvertedAName.$j]!="Keine Angabe" && $_POST[$ConvertedAName.$j]!=""){
									$charArray[] = $_POST[$ConvertedAName.$j];
								}
								$j++;
							}
								
							for($k=0;$k<count($charArray);$k++) {
								$sql2 = 'INSERT INTO ubesitzt_char (UID, AName, Wert) 
										VALUES ('.$ID.', "'.$row["AName"].'", "'.$charArray[$k].'");';
								$result2 = $conn->query($sql2);
								
								if ($result2 === FALSE) {echo "Error: " . $sql2 . "<br>" . $conn->error;}
							}
						}
						
						$updatedDB =true;
						//header('Location: DatenBUnterkunft.php');
					}
				} else {
					echo "Die gewünschte Adresse konnte nicht gefunden werden. Bitte überprüfen Sie ihre Eingabe bei:".$error;
				}
			}
		}
	}
}

//==========================================================================================================================================================

//Eingabe form
echo '
<div>
<form action ="UebersichtUnterkunft.php" method="Post">
<table style="width:100%">
<colgroup>
    <col style="width: 30%" />
    <col style="width: 40%" />
    <col style="width: 30%" />
</colgroup> ' ;

$sql = "SELECT * FROM unterkunft where ID=".$ID.";";
$result = $conn -> query($sql);

if($result === false) {
	echo "FEHLER: ".$conn->error;
} else {
	while($row=$result->fetch_assoc()){
		echo '<tr><th>Name*</th><td> <input type="text" name="Name" value="';
		if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"])){echo $_POST["Name"].'" ';} else {echo $row["Name"].'" ';}
		if($errorPflicht == true){echo "border: 2px solid red;";}
		echo '></td></tr>';
		
		if(!is_null($row["Telefonnummer"])) {echo '<tr><th>Telefonnummer</th><td><input type="text" name="TelNum" value="';
			if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"])  && !$updatedDB) {echo $_POST["TelNum"].'" ';} else {echo $row["Telefonnummer"].'" ';}
			echo '></td></tr>';}
			
		if(!is_null($row["MailAdresse"])) {echo '<tr><th>E-Mail-Adresse</th><td><input type="text" name="EMail" value="';
			if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"]) && !$updatedDB) {echo $_POST["EMail"].'" ';} else {echo $row["MailAdresse"].'" ';}
			echo '></td></tr>';}
			
		if(!is_null($row["Internetseite"])) {echo '<tr><th>Internetseite</th><td><input type="text" name="Internet" value="';
			if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"]) && !$updatedDB) {echo $_POST["Internet"].'" ';} else {echo $row["Internetseite"].'" ';}
			echo '></td></tr>';}
		
		echo '<tr><th>Straße*</th><td><input type="text" name="Str" value="';
		if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"])  && !$updatedDB){echo $_POST["Str"].'" ';} else {echo $row["Strasse"].'" ';}
		if($errorPflicht == true){echo "border: 2px solid red;";}
		echo '></td></tr>';
		
		if(!is_null($row["Hausnummer"])) {echo '<tr><th>Hausnummer</th><td><input type="text" name="Hausnummer" value="';
			if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"]) && !$updatedDB) {echo $_POST["Hausnummer"].'" ';} else {echo $row["Hausnummer"].'" ';}
			echo '></td></tr>';}
			
		if(!is_null($row["Postleitzahl"])) {echo '<tr><th>Postleitzahl</th><td><input type="text" name="Plz" value="';
			if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"]) && !$updatedDB) {echo $_POST["Plz"].'" ';} else {echo $row["Postleitzahl"].'" ';}
			echo '></td></tr>';}
		
		echo '<tr><th>Ort*</th><td><input type="text" name="Ort" value="';
		if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"]) && !$updatedDB){echo $_POST["Ort"].'" ';} else {echo $row["Ort"].'" ';}
		if($errorPflicht == true){echo "border: 2px solid red;";}
		echo '></td></tr>';
		
		if(!is_null($row["Land"])) {echo '<tr><th>Land</th><td><input type="text" name="Land" value="';
			if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"]) && !$updatedDB) {echo $_POST["Land"].'" ';} else {echo $row["Land"].'" ';}
			echo '></td></tr>';}
			
		if(!is_null($row["Kommentar"])) {echo '<tr><th>Kommentar</th><td><textarea rows="4" cols="50" name="Kommentar">';
			if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"]) && !$updatedDB) {echo $_POST["Kommentar"];} else {echo $row["Kommentar"];}
			echo '</textarea></td></tr>';}
	}
}

echo '<tr style="height: 20px"><td></td></tr>';

//bool Attribute
$sql = "SELECT * FROM ubesitzt_bool WHERE UID=".$ID." ORDER BY AName;";
$result = $conn -> query($sql);


if($result === false) {
	echo "FEHLER: ".$conn->error;
} else {
	while($row=$result->fetch_assoc()){
		$ConvertedAName = str_replace(" ","_",$row["AName"]); //Ist notwendig, da das Formular beim übermitteln Freiezeichen durch Unterstriche ersetzt
		if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"]) && !$updatedDB) {
			if(isset($_POST[$ConvertedAName])) {
				echo '<tr><th>'.$row["AName"].'</th><td><input type="checkbox" value=1 name="'.$row["AName"].'" checked></td></tr>';
			} else {
				echo '<tr><th>'.$row["AName"].'</th><td><input type="checkbox" value=1 name="'.$row["AName"].'"> </td></tr>';	
			}
		} else {
			if($row["Wert"]=="1") {
				echo '<tr><th>'.$row["AName"].'</th><td><input type="checkbox" value=1 name="'.$row["AName"].'" checked></td></tr>';
			} else {
				echo '<tr><th>'.$row["AName"].'</th><td><input type="checkbox" value=1 name="'.$row["AName"].'"> </td></tr>';	
			}
		}
	}
}

//char Attribute
$sql = "SELECT * FROM ubesitzt_char NATURAL JOIN attributefuerunterkunft WHERE UID=".$ID." ORDER BY AName;";
$result = $conn -> query($sql);

$m = 0;
$save=0;

if($result === false) {
	echo "FEHLER: ".$conn->error;
} else {
	$lastAttributeAdded = ""; //Wird genutzt, um abzugreifen ob der Kategoriename bereits ausgegeben wurde
	while($row=$result->fetch_assoc()){
		if($row["AName"]!= $save) {$m=0;}
		
		if($row["Typ"] == "char") {
			echo '<tr><th>';
			echo '</th><td><input type="text" name="'.$row["AName"].$m.' value="';
			if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"]) && !$updatedDB) {echo $_POST[$row["AName"].$m].'"';}
			else {echo $row["Wert"].'"';}
			echo '></td></tr>';
			$m++;
		}
		else if ($row["Typ"] == "charc") {
			$sql2 = "SELECT Wert FROM attributefuerunterkunftauswahlwerte WHERE AName='".$row["AName"]."' ORDER BY Wert;";
			$result2 = $conn->query($sql2);
		
			if($result2 === false) {echo 'FEHLER: '.$conn->error;}
			
			echo '<tr><th>';
			if ($lastAttributeAdded == $row["AName"])
				echo " ";
			else
			{
				echo $row["AName"];
				$lastAttributeAdded = $row["AName"];
			}
			echo '</th><td><select name="'.$row["AName"].$m.'">';
			
			echo '<option value="Keine Angabe"';
			if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"]) && !$updatedDB) {if ($_POST[$row["AName"].$m]=="Keine Angabe") {echo ' selected="true"';}}
			else {if ($row["Wert"]=="Keine Angabe") {echo ' selected="true"';}}
			echo '>Keine Angabe</option>';
			
			while($row2=$result2->fetch_assoc()){
				echo '<option value="'.$row2["Wert"].'"';
				if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"]) && !$updatedDB) {if($_POST[$row["AName"].$m]==$row2["Wert"]) {echo 'selected="true"';}}
				else {if($row["Wert"]==$row2["Wert"]) {echo 'selected="true"';}}
				echo '>'.$row2["Wert"].'</option>';
			}
			
			echo '</select></td></tr>';
			
			$m++;
		}
	}
}

//int Attribute
$sql = "SELECT * FROM ubesitzt_int WHERE UID=".$ID.";";
$result = $conn -> query($sql);


if($result === false) {
	echo "FEHLER: ".$conn->error;
} else {
	while($row=$result->fetch_assoc()){
		if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"]) && !$updatedDB) {
			echo '<tr style="height: 10px"><td></td></tr>';
			echo '<tr><th>'.$row["AName"].':</th><td><input type="number" id="number" name="'.$row["AName"].'" value='.$_POST[$row["AName"]].'></td></tr>';
		} else {
			echo '<tr style="height: 10px"><td></td></tr>';
			echo '<tr><th>'.$row["AName"].':</th><td><input type="number" id="number" name="'.$row["AName"].'" value='.$row["Wert"].'></td></tr>';
		}
	}
}

//Preise
$sql = 'SELECT KommentarPreis FROM unterkunft WHERE ID='.$ID.';';
$x = $conn->query($sql);
$row = $x->fetch_assoc();

$preisDB = $row["KommentarPreis"];
if(!is_null($preisDB)) {
	$jahre = explode("\\", $preisDB);

	//echo '<tr><th style="vertical-align:top;"></br></br>Preise</th><td id="elements"><table><tr><th>';

	//gibt an, ob "Preise" schon ausgegegeben wurde
	$Info=false;

	for($i=0;$i<=2;$i++) {
		$counterJahr=$i + 1;
		
		$jahr = explode("|", $jahre[$i]);
		
		//last element
		$last=10;
		while($last>=0 && $jahr[$last]=="") {
			$last--;
		}
		
		if($last<0) {continue;}
		
		if($last%2==0) {}
		else {$last++;}
		
		echo '<tr><th style="vertical-align:top;"></br></br>';
		if($Info==false) {echo 'Preise'; $Info=true;}
		echo '</th><td><table style="width:99%;"><tr><th><input type="text" name="Jahr'.$counterJahr.'" placeholder="Jahreszahl '.$counterJahr.'" ';
		if($jahr[0] != "") {echo 'value="'.$jahr[0].'"';}
		echo '></th><td></br></td></tr>';
		
		$counter = 1;
		for($j=1;$j<=$last;$j=$j+2) {
			echo '<tr><th><input type="text" name="Typ'.$counterJahr.'.'.$counter.'" placeholder="Typ '.$counter.'" ';
			if($jahr[$j] != "") {echo 'value="'.$jahr[$j].'"';}
			echo '></th><td><input type="text" name="Preis'.$counterJahr.'.'.$counter.'" placeholder="Preis '.$counter.'" ';
			if($jahr[$j + 1] != "") {echo 'value="'.$jahr[$j + 1].'"';}
			echo '></td></tr>';
			$counter++;
		}
		
		echo '</table>';
		$counterJahr++;
	}
}

echo '</table>';
echo '<table class="buttons" style="width:90%">
<colgroup>
    <col style="width: 25%" />
    <col style="width: 25%" />
    <col style="width: 25%" />
	<col style="width: 25%" />
</colgroup><tr>
<td style="text-align:center;"><button class="reset" type="reset" value="Reset" style="width:70%;padding:9px;">Änderungen zurücksetzen</button></td>
<!-- weiterleitung zu Details -->
<td><a href="DatenBUnterkunft.php" id="changeAll" >Alle Daten bearbeiten</a></td>
<td><a href="LoeschenUnterkunft.php" id="deleteB">Unterkunft löschen</a></td>
<td><input type = submit value="Eingabe speichern" id="submitB" style="margin-right:0;float:none;width:70%;padding:9px;font-family: Verdana, Helvetica, Arial, sans-serif;"></td></tr>
</table>
</form></br></br>
</div>';

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
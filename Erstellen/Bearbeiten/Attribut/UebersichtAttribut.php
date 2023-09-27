<?php
session_start();
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
  
  #ramen1{
	  margin-left:auto;
	  margin-right:auto;
	  width:90%;
	  background-color:white;
	  <!--border: 1px solid #C0C0C0;-->
  }
  #ramen2{
	  margin-left:30%;
	  margin-right:auto;
	  width:50%;
	  text-align:left;
	  background-color:white;
	  ;
  }
 
  a:link, a:visited{
	width: 40%;
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

<div class="color" id="colorSuche">
<button type="button" onclick="leave_site()" class="HomeB">Home</button></br></br>
<h1 style="text-align:center;">Attribut bearbeiten</h1></br></br>
</div>
<div class="circle"><i style='font-size:50px;margin-top:35%;color:grey;' class='far'>&#xf044;</i></div>

</br></br><div style="border:1px solid black;width:50%;margin-left:auto;margin-right:auto;"></br><i class="fas fa-exclamation-circle" style="font-size:20px;color:black;">&nbsp;&nbsp;</i>Bei Änderung des Eingabetyps werden alle dem Attribut zugehörigen Daten unwiderruflich gelöscht!</br></br></div>

<?php

include('../../../database.php');
$conn = getConnection();

if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST["ID"])) {
	$_SESSION["att"] = $_POST["ID"];
}

//$_SESSION["att"] = "SFußballplatzart";

$object = substr($_SESSION["att"],0 , 1);
$att = substr($_SESSION["att"],1);
$typ;

if($object=="U"){
	$sql = 'SELECT * FROM attributefuerunterkunft WHERE AName="'.$att.'";';
	$result = $conn -> query($sql);
	
	if($result === false) {
		echo "FEHLER: ".$conn->error;
	} else {
		while($row=$result->fetch_assoc()){
			$typ = $row["Typ"];
		}
	}
}

if($object=="S"){
	$sql = 'SELECT * FROM attributefuersportstaette WHERE AName="'.$att.'";';
	$result = $conn -> query($sql);
	
	if($result === false) {
		echo "FEHLER: ".$conn->error;
	} else {
		while($row=$result->fetch_assoc()){
			$typ=$row["Typ"];
		}
	}
}


//DB Verarbeitung
if($_SERVER["REQUEST_METHOD"]=="POST" && !isset($_POST["ID"])) {
	if($_POST["name"]=="" || is_null($_POST["name"])) {
		echo '</br><div style="border:1px solid black;width:50%;margin-left:auto;margin-right:auto;"></br><i class="fas fa-exclamation-circle" style="font-size:20px;color:black;">&nbsp;&nbsp;</i>Sie können einem Attribut keinen leeren Namen geben! <br>
				Wenn Sie das Attribut löschen möchten, wählen Sie die Option "Attribut löschen" unterhalb des Eingabeformulars</br>';
				echo '</br></br></div>';
	}
	else {
		$inDB = false;
		
		if($object == "U") {
			if($att != $_POST["name"]){
				$sql = 'SELECT * FROM attributefuerunterkunft WHERE AName="'.$_POST["name"].'" ;';
				$result = $conn -> query($sql);
				
				while($row=$result->fetch_assoc()){
					$inDB = true;
				}
			}
			
			if($inDB) {
				echo '</br><div style="border:1px solid black;width:50%;margin-left:auto;margin-right:auto;"></br><i class="fas fa-exclamation-circle" style="font-size:20px;color:black;">&nbsp;&nbsp;</i>Für den Namen "'.$_POST["name"].'" existiert bereits ein Attribut für Unterkünfte! <br>
				Wählen Sie für das Attribut einen anderen Namen oder bearbeiten Sie das Attribut "'.$_POST["name"].'"</br>';
				//echo '<div style="width:40%;margin-right:auto;margin-left:auto;margin-top:30px;"><a href="../Bearbeiten/Attribut/UebersichtAttribut.php">Attribut "'.$_POST["name"].'" bearbeiten</a></div>';
				echo '</br></br></div>';
			} else {
				//Update DB
				$changedToCharc = false;
				
				//Update Name
				if($att != $_POST["name"]) {
					$sql = 'UPDATE attributefuerunterkunft SET AName="'.$_POST["name"].'" WHERE AName="'.$att.'";';
					$result = $conn -> query($sql);
					
					if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
					else {$att = $_POST["ID"]; $_SESSION["att"] = $object . $att;}
				}
				
				//Update Typ
				if($typ != $_POST["typ"]) {
					
					//delete current data
					if($typ=="int") {
						$sql = 'DELETE FROM ubesitzt_int WHERE AName="'.$att.'";';
						$result = $conn -> query($sql);
					}
					if($typ=="bool") {
						$sql = 'DELETE FROM ubesitzt_bool WHERE AName="'.$att.'";';
						$result = $conn -> query($sql);
					}
					if($typ=="charc") {
						$sql = 'DELETE FROM ubesitzt_char WHERE AName="'.$att.'";';
						$result = $conn -> query($sql);
						
						$sql = 'DELETE FROM attributefuerunterkunftauswahlwerte WHERE AName="'.$att.'";';
						$result = $conn -> query($sql);
					}
					
					$sql = 'UPDATE attributefuerunterkunft SET Typ="'.$_POST["typ"].'" WHERE Typ="'.$typ.'";';
					$result = $conn -> query($sql);
					
					if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
					
					$typ = $_POST["typ"];
					
					//insert new Auswahlwerte
					if($typ == "charc") {
						$changedToCharc=true;
						$charArray = array();
						if(isset($_POST["aw"]) && $_POST["aw"]!=NULL && $_POST["aw"]!="") {$charArray[] = $_POST["aw"];}
						
						$counter = 0;
						while(isset($_POST["aw".$counter])) {
							if($_POST["aw".$counter] != NULL && !in_array($_POST["aw".$counter], $charArray) && $_POST["aw".$counter] != "") {$charArray[] = $_POST["aw".$counter];}
							$counter++;
						}
						
						for($i=0;$i<count($charArray);$i++) {
							$sql = 'INSERT INTO attributefuerunterkunftauswahlwerte (AName, Wert)
									VALUES ("'.$att.'", "'.$charArray[$i].'");';
							$result = $conn -> query($sql);
							
							if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
						}
					}
				}
				
				//Update Auswahlwerte
				if ($typ=="charc" && !$changedToCharc)
				{
					$sql = 'SELECT * FROM attributefuerunterkunftauswahlwerte WHERE AName="'.$att.'" ORDER BY Wert;';
					$result = $conn -> query($sql);
					
					$charArray = array();
					if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
					else {
						while($row=$result->fetch_assoc()){
							$charArray[] = $row["Wert"];
						}
					}
					
					if(!isset($_POST["aw"]) || is_null($_POST["aw"]) || $_POST["aw"]=="") {
						$sql = 'DELETE FROM ubesitzt_char WHERE AName="'.$att.'" AND Wert="'.$charArray[0].'";';
						$result = $conn -> query($sql);
						
						$sql = 'DELETE FROM attributefuerunterkunftauswahlwerte WHERE AName="'.$att.'" AND Wert="'.$charArray[0].'";';
						$result = $conn -> query($sql);
					} else {
						$sql = 'UPDATE attributefuerunterkunftauswahlwerte SET Wert="'.$_POST["aw"].'" WHERE AName="'.$att.'" AND Wert="'.$charArray[0].'";';
						$result = $conn -> query($sql);
						
						//if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
					}
					
					$counter = 1;
					for($counter = 1;$counter<count($charArray);$counter++) {
						if(!isset($_POST["aw".$counter]) || is_null($_POST["aw".$counter]) || $_POST["aw".$counter]=="") {
							$sql = 'DELETE FROM ubesitzt_char WHERE AName="'.$att.'" AND Wert="'.$charArray[$counter].'";';
							$result = $conn -> query($sql);
							
							$sql = 'DELETE FROM attributefuerunterkunftauswahlwerte WHERE AName="'.$att.'" AND Wert="'.$charArray[$counter].'";';
							$result = $conn -> query($sql);
						} else {
							$sql = 'UPDATE attributefuerunterkunftauswahlwerte SET Wert="'.$_POST["aw".$counter].'" WHERE AName="'.$att.'" AND Wert="'.$charArray[$counter].'";';
							$result = $conn -> query($sql);
							
							//if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
						}
					}
					
					while(isset($_POST["aw".$counter])) {
						if(!is_null($_POST["aw".$counter]) && $_POST["aw".$counter]!="") {
							$sql = 'INSERT INTO attributefuerunterkunftauswahlwerte(AName, Wert)
									VALUES ("'.$att.'","'.$_POST["aw".$counter].'");';
									
							$result = $conn -> query($sql);
							//if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
						}
						$counter++;
					}
					
					
				}
			}
		}
		
		if($object == "S") {
			if($att != $_POST["name"]){
				$sql = 'SELECT * FROM attributefuersportstaette WHERE AName="'.$_POST["name"].'";';
				$result = $conn -> query($sql);
				
				while($row=$result->fetch_assoc()){
					$inDB = true;
				}
			}
			
			if($inDB) {
				echo '</br><div style="border:1px solid black;width:70%;margin-left:auto;margin-right:auto;"></br><i class="fas fa-exclamation-circle" style="font-size:20px;color:black;">&nbsp;&nbsp;</i>Für den Namen "'.$_POST["name"].'" existiert bereits ein Attribut für Unterkünfte! <br>
				Wählen Sie für das Attribut einen anderen Namen oder bearbeiten Sie das Attribut "'.$_POST["name"].'"</br>';
				//echo '<div style="width:22%;margin-right:auto;margin-left:auto;margin-top:30px;"><a href="../Bearbeiten/Attribut/UebersichtAttribut.php">Attribut "'.$_POST["name"].'" bearbeiten</a></div>';
				echo '</br></br></div>';
			} else {
				//Update DB
				$changedToCharc = false;
				
				//Update Name
				if($att != $_POST["name"]) {
					$sql = 'UPDATE attributefuersportstaette SET AName="'.$_POST["name"].'" WHERE AName="'.$att.'";';
					$result = $conn -> query($sql);
					
					if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
					else {$att = $_POST["ID"]; $_SESSION["att"] = $object . $att;}
				}
				
				//Update Typ
				if($typ != $_POST["typ"]) {
					
					//delete current data
					if($typ=="int") {
						$sql = 'DELETE FROM ssbesitzt_int WHERE AName="'.$att.'";';
						$result = $conn -> query($sql);
					}
					if($typ=="bool") {
						$sql = 'DELETE FROM ssbesitzt_bool WHERE AName="'.$att.'";';
						$result = $conn -> query($sql);
					}
					if($typ=="charc") {
						$sql = 'DELETE FROM ssbesitzt_char WHERE AName="'.$att.'";';
						$result = $conn -> query($sql);
						
						$sql = 'DELETE FROM attributefuersportstaetteauswahlwerte WHERE AName="'.$att.'";';
						$result = $conn -> query($sql);
					}
					
					$sql = 'UPDATE attributefuersportstaette SET Typ="'.$_POST["typ"].'" WHERE Typ="'.$typ.'";';
					$result = $conn -> query($sql);
					
					if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
					
					$typ = $_POST["typ"];
					
					//insert new Auswahlwerte
					if($typ == "charc") {
						$changedToCharc = true;
						$charArray = array();
						if(isset($_POST["aw"]) && $_POST["aw"]!=NULL && $_POST["aw"]!="") {$charArray[] = $_POST["aw"];}
						
						$counter = 0;
						while(isset($_POST["aw".$counter])) {
							if($_POST["aw".$counter] != NULL && !in_array($_POST["aw".$counter], $charArray) && $_POST["aw".$counter] != "") {$charArray[] = $_POST["aw".$counter];}
							$counter++;
						}
						
						for($i=0;$i<count($charArray);$i++) {
							$sql = 'INSERT INTO attributefuersportstaetteauswahlwerte (AName, Wert)
									VALUES ("'.$att.'", "'.$charArray[$i].'");';
							$result = $conn -> query($sql);
							
							if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
						}
					}
				}
				
				//Update Auswahlwerte
				if ($typ=="charc" && !$changedToCharc)
				{
					$sql = 'SELECT * FROM attributefuersportstaetteauswahlwerte WHERE AName="'.$att.'" ORDER BY Wert;';
					$result = $conn -> query($sql);
					
					$charArray = array();
					if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
					else {
						while($row=$result->fetch_assoc()){
							$charArray[] = $row["Wert"];
						}
					}
					
					if(!isset($_POST["aw"]) || is_null($_POST["aw"]) || $_POST["aw"]=="") {
						$sql = 'DELETE FROM ssbesitzt_char WHERE AName="'.$att.'" AND Wert="'.$charArray[0].'";';
						$result = $conn -> query($sql);
						
						$sql = 'DELETE FROM attributefuersportstaetteauswahlwerte WHERE AName="'.$att.'" AND Wert="'.$charArray[0].'";';
						$result = $conn -> query($sql);
					} else {
						$sql = 'UPDATE attributefuersportstaetteauswahlwerte SET Wert="'.$_POST["aw"].'" WHERE AName="'.$att.'" AND Wert="'.$charArray[0].'";';
						$result = $conn -> query($sql);
						//if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
					}
					
					$counter = 1;
					for($counter = 1;$counter<count($charArray);$counter++) {
						if(!isset($_POST["aw".$counter]) || is_null($_POST["aw".$counter]) || $_POST["aw".$counter]=="") {
							$sql = 'DELETE FROM ssbesitzt_char WHERE AName="'.$att.'" AND Wert="'.$charArray[$counter].'";';
							$result = $conn -> query($sql);
							
							$sql = 'DELETE FROM attributefuersportstaetteauswahlwerte WHERE AName="'.$att.'" AND Wert="'.$charArray[$counter].'";';
							$result = $conn -> query($sql);
						} else {
							$sql = 'UPDATE attributefuersportstaetteauswahlwerte SET Wert="'.$_POST["aw".$counter].'" WHERE AName="'.$att.'" AND Wert="'.$charArray[$counter].'";';
							$result = $conn -> query($sql);
							//if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
						}
					}
					
					while(isset($_POST["aw".$counter])) {
						if(!is_null($_POST["aw".$counter]) && $_POST["aw".$counter]!="") {
							$sql = 'INSERT INTO attributefuersportstaetteauswahlwerte(AName, Wert)
									VALUES ("'.$att.'","'.$_POST["aw".$counter].'");';
									
							$result = $conn -> query($sql);
							//if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
						}
						$counter++;
					}
					
					
				}
				
				//Update Zuordnung
				$sql = 'SELECT * FROM zuordnungsaa WHERE AName="'.$att.'";';
				$x = $conn -> query($sql);
				$zuord = "Keine Zuordnung";
				
				while($rowx=$x->fetch_assoc()){
					$zuord = $rowx["SAName"];
				}
				
				if($zuord != $_POST["sport"]) {
					if($_POST["sport"] != "Keine Zuordnung") {
						$sql = 'UPDATE zuordnungsaa SET SAName="'.$_POST["sport"].'" WHERE AName="'.$att.'";';
						$result = $conn -> query($sql);
							
						if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
					} else {
						$sql = 'DELETE FROM zuordnungsaa WHERE AName="'.$att.'";';
						$result = $conn -> query($sql);
							
						if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
					}
				}
			}
		}
	}
}

//============================================================================================
//form
echo '
<div>
<form action ="UebersichtAttribut.php" method="Post">
<table style="width:100%">
<colgroup>
    <col style="width: 30%" />
    <col style="width: 40%" />
    <col style="width: 30%" />
</colgroup> ' ;

if($object=="U") {
	$sql = 'SELECT * FROM attributefuerunterkunft WHERE AName="'.$att.'";';
	$result = $conn -> query($sql);
	
	if($result === false) {
		echo "FEHLER: ".$conn->error;
	} else {
		while($row=$result->fetch_assoc()){
			echo '<tr><th>Name</th><td><input type="text" name="name" value="'.$row["AName"].'"></br></td></tr>';
			echo '<tr><th style="vertical-align:top;">Eingabetyp</th><td style="text-align:center;"><div id="ramen1"><div id="ramen2"></br>
							<input type="radio" name="typ" value="bool" id="typB" ';
							if($row["Typ"]=="bool"){echo 'checked';}
							echo '><label for="typB"> Wahrheitswerte</label> </br></br>
							<input type="radio" name="typ" value="int" id="typI" ';
							if($row["Typ"]=="int"){echo 'checked';}
							echo '><label for="typI"> Zahlwerte</label> </br></br>							
							<input type="radio" name="typ" value="charc" id="typC" ';
							if($row["Typ"]=="charc"){echo 'checked';}
							echo '><label for="typC"> Auswahlwerte </label></br></br>
							</div></div></br></td></tr>';
			
			
			
			if($row["Typ"]=="charc") {
				
				echo DisplayAuswahlwerte("attributefuerunterkunftauswahlwerte",$att);

			}
			else {
				echo '<tr id="Auswahlwerte" class="hide" style="opacity:0;transition:0.3s;"><th style="vertical-align:top;">Auswahlwerte</th><td><div id="ramen1"><div id=""><table><tr><td style="text-align:center;"><input name="aw" class="aws" type="text"/></td>
						<td style="width:40%; vertical-align:top;"><button type="button" class="adder" style="margin:2px;">+</button><button type="button" class="miner">-</button></td></tr>

						</table></div></div></td></tr>';
			}
		}
	}
}

if($object=="S") {
	$sql = 'SELECT * FROM attributefuersportstaette WHERE AName="'.$att.'";';
	$result = $conn -> query($sql);
	
	if($result === false) {
		echo "FEHLER: ".$conn->error;
	} else {
		while($row=$result->fetch_assoc()){
			echo '<tr><th>Name</th><td><input type="text" name="name" value="'.$row["AName"].'"></br></td></tr>';
			echo '<tr><th style="vertical-align:top;">Eingabetyp</th><td style="text-align:center;"><div id="ramen1"><div id="ramen2"></br>
							<input type="radio" name="typ" value="bool" id="typB" ';
							if($row["Typ"]=="bool"){echo 'checked';}
							echo '><label for="typB"> Wahrheitswerte</label> </br></br>
							<input type="radio" name="typ" value="int" id="typI" ';
							if($row["Typ"]=="int"){echo 'checked';}
							echo '><label for="typI"> Zahlwerte</label> </br></br>							
							<input type="radio" name="typ" value="charc" id="typC" ';
							if($row["Typ"]=="charc"){echo 'checked';}
							echo '><label for="typC"> Auswahlwerte </label></br></br>
							</div></div></br></td></tr>';
			
			$sql = 'SELECT * FROM zuordnungsaa WHERE AName="'.$att.'";';
			$x = $conn -> query($sql);
			$zuord = "Keine Zuordnung";
			
			while($rowx=$x->fetch_assoc()){
				$zuord = $rowx["SAName"];
			}
			
			
			echo '<tr id="Zuordnungen" class="" style="opacity:1;transition:0.3s;"><th style="vertical-align:top;">Zuordnung</th><td><div id="ramen1"><div id="ramen2">';

			$sql = 'SELECT * FROM sportart ORDER BY Name;';
			$result2 = $conn -> query($sql);

			while($row2=$result2->fetch_assoc()){
				echo '</br><input type="radio" name="sport" value="'.$row2["Name"].'" id="'.$row2["Name"].'" ';
				if($zuord == $row2["Name"]) {echo 'checked';}
				echo '><label for="'.$row2["Name"].'" > '.$row2["Name"].'</label></br></br>';
			}
			echo '</br><input type="radio" name="sport" value="Keine Zuordnung" id="kZ" ';
			if($zuord == "Keine Zuordnung") {echo 'checked';}
			echo '><label for="kZ"> Keine Zuordnung</label> </br></br>';
			echo '</div></div></br></td></tr>';
			
			
			if($row["Typ"]=="charc") {
				
				echo DisplayAuswahlwerte("attributefuersportstaetteauswahlwerte",$att);

			}
			else {
				echo '<tr id="Auswahlwerte" class="hide" style="opacity:0;transition:0.3s;"><th>Auswahlwerte</th><td><div id="ramen1"><div id=""><table><tr><td><input name="aw" class="aws" type="text"/></td>
						<td style="width:40%; vertical-align:top;"><button type="button" class="adder" style="margin:2px;">+</button><button type="button" class="miner">-</button></td></tr>

						</table></div></div></td></tr>';
			}
		}
	}
}



echo '<table class="buttons" style="width:70%">
<colgroup>
    <col style="width: 25%" />
    <col style="width: 50%" />
	<col style="width: 25%" />
</colgroup><tr>
<td style="text-align:center;"><button class="reset" type="reset" value="Reset" style="width:70%;padding:9px;">Änderungen zurücksetzen</button></td>
<td><a href="LoeschenAttribut.php" id="deleteB">Attribut löschen</a></td>
<td><input type = submit value="Eingabe speichern" id="submitB" style="margin-right:0;float:none;width:70%;padding:9px;font-family: Verdana, Helvetica, Arial, sans-serif;"></td></tr>
</table>
</form></br></br>
</div>';

function DisplayAuswahlwerte($typ, $att)
{
	$conn = getConnection();
	$result = "";
	$result .= '<tr id="Auswahlwerte" class="" style="opacity:1;transition:0.3s;"><th>Auswahlwerte</th><td><div id="ramen1"><div id=""><table><tr><td>';
	$counter=0;
	$sqlWerte = 'SELECT * FROM '.$typ.' WHERE AName="'.$att.'" ORDER BY Wert;';
	$resultWerte = $conn -> query($sqlWerte);
		if($resultWerte === false) {
			$result .= "FEHLER: ".$conn->error;
		} else					
		{
			while($rowInner=$resultWerte->fetch_assoc())
			{
				if ($counter==0)
				{
					$result .= '<input name="aw" class="aws" type="text" value="'.$rowInner["Wert"].'" />';
				}
				else
				{
					$result .= '<input name="aw'.$counter.'" class="aws" type="text" value="'.$rowInner["Wert"].'"/>';
				}
				$counter++;
			}
		}
	if ($counter == 0)
	{
		$result .= '<tr><td><input name="aw" class="aws" type="text" />';
	}
	
	$result .= '</td>
					<td style="width:40%; vertical-align:top;"><button type="button" class="adder" style="margin:2px;">+</button>
					<button type="button" class="miner">-</button></td></tr></table></div></div></td></tr>';

	$conn->close();
	return $result;
	
}


$conn->close();
?>





<script>
var typen = document.getElementsByName("typ");
for(var i = 0;i < typen.length;i++)
{
	typen[i].addEventListener("click",function()
	{
		var zuord = document.getElementById("Auswahlwerte");
		if (this.value != "charc")
		{
			//zuord.style.display = "none";
			zuord.style.opacity = "0";	
			setTimeout(function(){document.getElementById("Auswahlwerte").classList.add("hide")},300);	
		}
		else
		{
			//zuord.style.display = "";
			zuord.style.opacity = "1";	
			zuord.classList.remove("hide");	
		}
	});
}

var clicker = document.getElementsByClassName("adder");
for(i = 0; i < clicker.length;i++)
{
	clicker[i].addEventListener('click',function(e){
	e = e || window.event;
	var target = e.target || e.srcElement;
	var locid = target.id;
	//Navigate to source input
	console.log(target);
	var source = target.parentNode.parentNode.firstChild.firstChild;
	console.log(source);
	var j = 0;
	j = target.parentNode.parentNode.firstChild.childNodes.length;
	console.log(target.parentNode.parentNode.firstChild.childNodes.length);
	var newNode = document.createElement("input");
	//newNode.innerHTML = source.innerHTML;
	newNode.name = "aw" + j;
	newNode.classList.add("aws");
	newNode.type = "text";
	target.parentNode.parentNode.firstChild.appendChild(newNode);
	//var panel = target.parentNode.parentNode.parentNode.parentNode.parentNode;
	//panel.style.maxHeight = panel.scrollHeight + "px";
	
	newNode.addEventListener("click", function(){changed = true});
},false);
}
var clicker = document.getElementsByClassName("miner");
for(i = 0; i < clicker.length;i++)
{
	clicker[i].addEventListener('click',function(e){
	e = e || window.event;
	var target = e.target || e.srcElement;
	var locid = target.id;
	//Navigate to source select - letztes Element soll entfernt werden
	var source = target.parentNode.parentNode.firstChild.lastChild;
	
	if((target.parentNode.parentNode.firstChild.childNodes.length-1) > 0)
	{
		target.parentNode.parentNode.firstChild.removeChild(source);
		changed = true;
	}	
},false);
}	

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
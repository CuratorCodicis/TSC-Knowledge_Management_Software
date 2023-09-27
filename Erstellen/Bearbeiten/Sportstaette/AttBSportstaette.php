<?php
session_start();

//error_reporting(E_ERROR | E_PARSE);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Kriterien</title>
  <meta charset="UTF-8">
  <LINK rel="stylesheet" href="\general.css">
  <LINK rel="stylesheet" href="\colors.css">
  <LINK rel="stylesheet" href="../../Anlegenstyle.css">
  <LINK rel="stylesheet" href="\Icons/css/all.css">
 <style>
 .sportart{
	 text-align:center;
	 margin-top: 60px;
 }
 #inhalt, #comment, th, td {
	margin-left: auto;
	margin-right:auto;
    border: none;
    border-collapse: collapse;
}

td {
    text-align: center; 	
}
th {
    text-align: right;
}
#checkboxLine {
    text-align: left;
}

.hiddenchoice {
  background-color: #e5e5e5;
  color: black;
  cursor: pointer;
  padding: 18px;
  width: 70%;
  border: none;
  text-align: left;
  outline: none;
  font-size: 22px;
  transition: 0.4s;
  margin-top: 12px;
  margin-bottom: 6px;
}

.active{
  background-color: #3c3c3c;
  color: #F8F8FF;
}
.hiddenchoice:hover{
	background-color: #848484;
	color: #F8F8FF;
	cursor: pointer;
}

.hiddenchoice:after {
  content: '\002B';
  font-weight: bold;
  float: right;
  margin-left: 5px;
}

.active:after {
  content: "\2212";
}

.panel {
	margin-left: auto;
	margin-right:auto;
	width: 70%;
	background-color: white;
	max-height: 0;
	overflow: hidden;
	transition: max-height 0.2s ease-out;
	border-radius: 2px;
}
.subgroup{
	margin: 15px 10px 5px 20px;
	font-size:16pt;
	float:left;	
}

 </style>
</head>
<body>


<?php

include('../../../database.php');
$conn = getConnection();
?>

<script>
function leave_site() {
	window.location.replace("../../Homebutton.php");
}
</script>

<div class="color" id="colorS2">
<button type="button" onclick="leave_site()" class="HomeB">Home</button></br></br>
<h1>Kriterien</h1></br></br>
</div>
<div class="circle"><i style='font-size:50px;margin-top:35%;color:grey;' class='far'>&#xf044;</i></div>


<?php
$ID = $_SESSION["SID"];

//Daten aus DB auslesen


//Kommentar auslesen
$Kommentar;
$sql = "SELECT Kommentar FROM sportstaette WHERE ID=".$ID;
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
	$Kommentar = $row["Kommentar"];
}


//Allgemeine Attribute auslesen
//mit Attributnamen auf Index zugreifen
//[0] für Attribut Typ
//[1] für Attribut Wert
$AllgArray = array(); 

$sql = "SELECT * FROM attributefuersportstaette WHERE AName NOT IN (SELECT AName FROM zuordnungsaa) ORDER BY AName;";
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
	$AllgArray[$row["AName"]] = array();
	$AllgArray[$row["AName"]][0] = $row["Typ"];
	
	if($row["Typ"]=="charc") {
		$sql2='SELECT * FROM ssbesitzt_char WHERE SSID='.$ID.' AND AName="'.$row['AName'].'";';
		$result2 = $conn->query($sql2);
		
		$AllgArray[$row["AName"]][1] = array();
		$counter = 0;
		while($row2 = $result2->fetch_assoc()) {
			$AllgArray[$row["AName"]][1][$counter] = $row2["Wert"];
			$counter++;
		}
	} else if($row["Typ"]=="char"){
		$sql2='SELECT * FROM ssbesitzt_char WHERE SSID='.$ID.' AND AName="'.$row['AName'].'";';
		$result2 = $conn->query($sql2);
		
		while($row2 = $result2->fetch_assoc()) {
			$AllgArray[$row["AName"]][1] = $row2["Wert"];
		}
	} else if($row["Typ"]=="int"){
		$sql2='SELECT * FROM ssbesitzt_int WHERE SSID='.$ID.' AND AName="'.$row['AName'].'";';
		$result2 = $conn->query($sql2);
		
		while($row2 = $result2->fetch_assoc()) {
			$AllgArray[$row["AName"]][1] = $row2["Wert"];
		}
	} else if($row["Typ"]=="bool") {
		$sql2='SELECT * FROM ssbesitzt_bool WHERE SSID='.$ID.' AND AName="'.$row['AName'].'";';
		$result2 = $conn->query($sql2);
		
		while($row2 = $result2->fetch_assoc()) {
			$AllgArray[$row["AName"]][1] = $row2["Wert"];
		}
	}
}

//Sportarten und deren Attribute auslesen
$sql = "SELECT Name FROM sportart ORDER BY Name";
$result = $conn->query($sql);

//Array für Sportarten und deren Attribute
$SuperArray = array();
/*
1. Arrayebene: Sportart (ist auch SchlÃ¼ssel) - Post von "Sportartname.bool" enthält, ob für Sportartgeeignet
2. Arrayebene: [0]AName Array,[1]ATyp Array, [2]Wert Array
3. Arrayebene: die unterschiedlichen AName Werte, ATyp Werte, usw.
*/
while($row = $result->fetch_assoc()) {	
	$SuperArray[$row["Name"]]=array();
	$SuperArray[$row["Name"]][0]=array();
	$SuperArray[$row["Name"]][1]=array();
	$SuperArray[$row["Name"]][2]=array();
	
	$sql1 = "SELECT * FROM zuordnungsaa WHERE SAName='".$row["Name"]."' ORDER BY AName";
	$result1 = $conn->query($sql1);
	
	$position = 0;
	while($row1 = $result1->fetch_assoc()) {
		
		$sql2 = "SELECT * From attributefuersportstaette WHERE AName ='".$row1["AName"]."'";
		$result2 = $conn->query($sql2);
		
		while($row2 = $result2->fetch_assoc()) {
			$SuperArray[$row["Name"]][0][$position] = $row2["AName"];
			$SuperArray[$row["Name"]][1][$position] = $row2["Typ"];

			if($row2["Typ"]=="int" || $row2["Typ"]=="bool" || $row2["Typ"]=="char"){
				$sql3;
				
				if($row2["Typ"]=="int") {$sql3 = 'SELECT * FROM ssbesitzt_int WHERE SSID='.$ID.' AND AName="'.$row2["AName"].'";';}
				if($row2["Typ"]=="bool") {$sql3 = 'SELECT * FROM ssbesitzt_bool WHERE SSID='.$ID.' AND AName="'.$row2["AName"].'";';}
				if($row2["Typ"]=="char") {$sql3 = 'SELECT * FROM ssbesitzt_char WHERE SSID='.$ID.' AND AName="'.$row2["AName"].'";';}
				$result3 = $conn->query($sql3);
				
				while($row3 = $result3->fetch_assoc()) {
					//Hinzufügen der Werte bei einzelnen
					$SuperArray[$row["Name"]][2][$position] = $row3["Wert"];
				}
			} else {
				$SuperArray[$row["Name"]][2][$position] = array();
				
				$sql3 = 'SELECT * FROM ssbesitzt_char WHERE SSID='.$ID.' AND AName="'.$row2["AName"].'";';
				$result3 = $conn->query($sql3);
				
				while($row3 = $result3->fetch_assoc()) {
					$SuperArray[$row["Name"]][2][$position][] = $row3["Wert"];
				}
				
			}
			$position++;
		}
	}
}


if($_SERVER["REQUEST_METHOD"]=="POST") {
	
	var_dump($_POST); echo "<br>";
	
	
		
	$fehler=0;
	//delete from DB where ID = Session ID
						
	//delete bool
	$sql = 'DELETE FROM ssbesitzt_bool WHERE SSID='.$ID.';';
	$result = $conn->query($sql);
	
	if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
	
	
	//delete char and charc
	$sql = 'DELETE FROM ssbesitzt_char WHERE SSID='.$ID.';';
	$result = $conn->query($sql);
	
	if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
	
	
	//delete int
	$sql = 'DELETE FROM ssbesitzt_int WHERE SSID='.$ID.';';
	$result = $conn->query($sql);
	
	if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
	
	
	//delete eignungsass
	$sql = 'DELETE FROM eignungsass WHERE SSID='.$ID.';';
	$result = $conn->query($sql);
	
	if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
	
	//---------------------------------------------------------------------------------------
	
	//insert into DB
	
	$sql = "SELECT Name FROM sportart";
	$result = $conn->query($sql);

	while($row = $result->fetch_assoc()) {
		$ConvertedName = str_replace(" ","_",$row["Name"]);
		for($h=0;$h<count($SuperArray[$row["Name"]][0]);$h++) {
			if(isset($_POST[$ConvertedName.$h])){
				if($_POST[$ConvertedName.$h]!=NULL){$SuperArray[$row["Name"]][2][$h]=$_POST[$ConvertedName.$h];} else {$SuperArray[$row["Name"]][2][$h]=NULL;}
			}
		}
	}
	
	if($_POST["Kommentar"]!=NULL && $_POST["Kommentar"]!="") {$Kommentar = $_POST["Kommentar"];} else $Kommentar = NULL; //!- Hinteres Stück hinzugefügt, damit die Notics weggehen, dass $Kommentar nicht gesetzt ist
	

	
	
	$sql='SELECT * FROM attributefuersportstaette WHERE AName NOT IN (SELECT AName FROM zuordnungsaa) ORDER BY AName;';
	$result = $conn->query($sql);
	
	while($row = $result->fetch_assoc()) {
		$ConvertedAName = str_replace(" ","_",$row["AName"]);
		if(isset($_POST[$ConvertedAName]) && $_POST[$ConvertedAName]!=NULL) {
			if($row["Typ"] == "int") {
				$sqlneu = "INSERT INTO ssbesitzt_int(SSID, AName, Wert) VALUES (".$ID.",'".$row["AName"]."',".$_POST[$ConvertedAName].")";
				
				$x = $conn->query($sqlneu);
				if($x === false) {$fehler=1; echo $sqlneu." : "; echo $conn->error;}
			}
			
			if($row["Typ"] == "bool" && isset($_POST[$ConvertedAName])) {
				if($_POST[$ConvertedAName] != NULL) {
					$sqlneu = "INSERT INTO ssbesitzt_bool(SSID, AName, Wert) VALUES (".$ID.",'".$row["AName"]."',".$_POST[$ConvertedAName].")";
					
					$x = $conn->query($sqlneu);
					if($x === false) {$fehler=1; echo $sqlneu." : "; echo $conn->error;}
				}
			}
			
			if($row["Typ"] == "char" && $_POST[$row["AName"]] != NULL && $_POST["AName"] != "") {
				$sqlneu = "INSERT INTO ssbesitzt_char(SSID, AName, Wert) VALUES (".$ID.",'".$row["AName"]."',".$_POST[$ConvertedAName].")";
				
				$x = $conn->query($sqlneu);
				if($x === false) {$fehler=1; echo $sqlneu." : "; echo $conn->error;}
			}
			
			if($row["Typ"] == "charc") {
				$charArrayAllg = array();
				
				if($_POST[$ConvertedAName] != "Keine Angabe") {
					$charArrayAllg[] = $_POST[$ConvertedAName];
				}
				
				$counter = 0;
				
				while(isset($_POST[$ConvertedAName.$counter])) {
					if($_POST[$ConvertedAName.$counter]!="Keine Angabe") {
						if(!in_array($_POST[$ConvertedAName.$counter], $charArrayAllg)) {
							$charArrayAllg[] = $_POST[$ConvertedAName.$counter];
						}
					}
				
					$counter++;
				}
				
				for($k=0;$k<count($charArrayAllg);$k++) {
					$sqlneu = "INSERT INTO ssbesitzt_char(SSID, AName, Wert) VALUES (".$ID.",'".$row["AName"]."','".$charArrayAllg[$k]."')";
				
					$x = $conn->query($sqlneu);
					if($x === false) {$fehler=1; echo $sqlneu." : "; echo $conn->error;}
				}
			}
		}
	}
	
	$sql = "SELECT Name FROM sportart";
	$result = $conn->query($sql);//!- Zweite Anfrage hinzugefügt. Nachdem die erste Anfrage ausgelesen ist, ist es nicht möglich sie ein zweites Mal auszulesen
	while($row = $result->fetch_assoc()) {
		$ConvertedName = str_replace(" ","_",$row["Name"]);
		for($j=0;$j<count($SuperArray[$row["Name"]][0]);$j++) {
			
			//echo "<br>".$row["Name"]." ::: ".$SuperArray[$row["Name"]][0][$j]." : ".$SuperArray[$row["Name"]][1][$j]." : ".$SuperArray[$row["Name"]][2][$j];
								
			if($SuperArray[$row["Name"]][1][$j] == "int" && $SuperArray[$row["Name"]][2][$j] != NULL) {
				$sqlneu = "INSERT INTO ssbesitzt_int(SSID, AName, Wert) VALUES (".$ID.",'".$SuperArray[$row["Name"]][0][$j]."',".$SuperArray[$row["Name"]][2][$j].")";
				
				$x = $conn->query($sqlneu);
				if($x === false) {$fehler=1; echo $sqlneu." : ";echo $conn->error;}
			}
			
			if($SuperArray[$row["Name"]][1][$j] == "bool" && isset($SuperArray[$row["Name"]][2][$j])) {
				if($SuperArray[$row["Name"]][2][$j] != NULL){
					$sqlneu = "INSERT INTO ssbesitzt_bool(AName, SSID, Wert) VALUES ('".$SuperArray[$row["Name"]][0][$j]."',".$ID.",".$SuperArray[$row["Name"]][2][$j].")";
					
					$x = $conn->query($sqlneu);
					if($x === false) {$fehler=1; echo $sqlneu." : "; echo $conn->error;}
				}
			}
			
			
			//new
			if($SuperArray[$row["Name"]][1][$j] == "charc" || $SuperArray[$row["Name"]][1][$j] == "char") {
				$charArray = array();
				
				if($SuperArray[$row["Name"]][2][$j]!="Keine Angabe") {
					$charArray[]=$SuperArray[$row["Name"]][2][$j];
				}
				
				$counter = 0;
				while(isset($_POST[$ConvertedName.$j.$counter])) {
					if($_POST[$ConvertedName.$j.$counter]!="Keine Angabe") {
						if(!in_array($_POST[$ConvertedName.$j.$counter], $charArray)) {
							$charArray[] = $_POST[$ConvertedName.$j.$counter];
						}
					}
				
					$counter++;
				}
				
				for($k=0;$k<count($charArray);$k++) {
					$sqlneu = "INSERT INTO ssbesitzt_char(SSID, AName, Wert) VALUES (".$ID.",'".$SuperArray[$row["Name"]][0][$j]."','".$charArray[$k]."')";
				
					$x = $conn->query($sqlneu);
					if($x === false) {$fehler=1; echo $sqlneu." : "; echo $conn->error;}
				}
			}
		}
		//Überprüfe, ob die Checkbox geeignet für gesetzt wurde
		//!- isset hinzugefügt, um Fehlermeldungen abzufangen
		$geeignet = false;
		if (isset($_POST[$ConvertedName."bool"]) && $_POST[$ConvertedName."bool"] != NULL)
		{
			if ($_POST[$ConvertedName."bool"]==1)
				$geeignet = true;			
		}
		
		if($geeignet) {
			$sqlneu = "INSERT INTO eignungsass(SAName, SSID) VALUES ('".$row["Name"]."',".$ID.")";
			
			//!- Query Ausführung hinzugefügt, da diese fehlte
			$x = $conn->query($sqlneu);
			if($x === false) {$fehler=1; echo $sqlneu." : "; echo $conn->error;}
		}
	}
	
	if($Kommentar!=NULL && trim($Kommentar)!="") {
		$sqlneu = "UPDATE sportstaette SET Kommentar='".$Kommentar."' WHERE ID=".$ID;
	}
	else
		$sqlneu = "UPDATE sportstaette SET Kommentar=NULL WHERE ID=".$ID;
		
	$x = $conn->query($sqlneu);
	if($x === false) {$fehler=1; echo $sqlneu." : "; echo $conn->error;}
	
	
	//Preise
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
	
	$sql = 'UPDATE sportstaette SET KommentarPreis="'.$preis.'" WHERE ID='.$ID.';';
	$x = $conn->query($sql);
	
	if($x === false) {$fehler=1; echo $conn->error;}
	
	
	if($fehler==0) {
		header('Location: UebersichtSportstaette.php');
		
	} else {echo "<br>Fehler beim Einf&uumlgen in die Datenbank<br>";}
}

//=========================================================================================================================

//form

echo '<div class="sportart">';
echo '<form action ="AttBSportstaette.php" method="Post">';

$firstL="";

$sql='SELECT * FROM attributefuersportstaette WHERE AName NOT IN (SELECT AName FROM zuordnungsaa) ORDER BY AName;';
$result = $conn->query($sql);

echo '<button type=button class="hiddenchoice">Allgemeine Attribute</button> <div class="panel">';
echo '</br></br>';

while($row = $result->fetch_assoc()) {
	if(substr($row["AName"], 0, 1) != $firstL) // Anfangsbuchstabe
		{
			$firstL = substr($row["AName"], 0, 1);
			echo '</table><p class="subgroup">'.$firstL.'</p><table id="inhalt" style="width:50%"><colgroup><col style="width: 25%" /><col style="width: 75%" /></colgroup>';
		}
	if($row["Typ"] == "int") {
		if(isset($AllgArray[$row["AName"]][1])){echo  '<tr><th>'.$row["AName"].'</th><td><input type="number" value='.$AllgArray[$row["AName"]][1].' name="'.$row["AName"].'"></td><td style="text-align:left;vertical-align:top;min-width: 110px;"><button type="button" class="adder" style="display:none;margin:2px;">+</button><button type="button" class="miner" style="display:none;">-</button></td></tr>';}
		else {echo  '<tr><th>'.$row["AName"].'</th><td><input type="number" name="'.$row["AName"].'"></td><td style="text-align:left;vertical-align:top;min-width: 110px;"><button type="button" class="adder" style="display:none;margin:2px;">+</button><button type="button" class="miner" style="display:none;">-</button></td></tr>';}	
	}
	
	elseif($row["Typ"] == "bool") {
		if(isset($AllgArray[$row["AName"]][1])){if($AllgArray[$row["AName"]][1] == '1') {echo  '<tr><th>'.$row["AName"].'</th><td><input type="checkbox" checked value="1" name="'.$row["AName"].'"></td><td style="text-align:left;vertical-align:top;min-width: 110px;"><button type="button" class="adder" style="display:none;margin:2px;">+</button><button type="button" class="miner" style="display:none;">-</button></td></tr>';}}  //oder:{echo  '<tr><th>'.$valuesAName[$i].'</th><td><input type="radio" value=1 name="vari'.$i.'"> Ja <input type="radio" value=0 name="vari'.$i.'"> Nein <input type="radio" value=2 name="vari'.$i.'" checked> keine Angabe</td></tr>';}
		else {echo  '<tr><th>'.$row["AName"].'</th><td><input type="checkbox" value="1" name="'.$row["AName"].'"></td><td style="text-align:left;vertical-align:top;min-width: 110px;"><button type="button" class="adder" style="display:none;margin:2px;">+</button><button type="button" class="miner" style="display:none;">-</button></td></tr>';}
	}
	elseif($row["Typ"] == "char") {
		if(!isset($AllgArray[$row["AName"]][1]) || $AllgArray[$row["AName"]][1][0]=="") {echo  '<tr><th>'.$row["AName"].'</th><td><input type="text" name="'.$row["AName"].'"></td><td style="text-align:left;vertical-align:top;min-width: 110px;"><button type="button" class="adder" style="display:none;margin:2px;">+</button><button type="button" class="miner" style="display:none;">-</button></td></tr>';}
		else {echo  '<tr><th>'.$row["AName"].'</th><td><input type="text" value="'.$AllgArray[$row["AName"]][1].'" name="'.$row["AName"].'"></td><td style="text-align:left;vertical-align:top;min-width: 110px;"><button type="button" class="adder" style="display:none;margin:2px;">+</button><button type="button" class="miner" style="display:none;">-</button></td></tr>';}
	}
	
	elseif($row["Typ"] =="charc") {
		
		//TODO: form muss mehrere values adden für ein Attribut - values liegen in $AllgArray[Attributname][1][]
		
		
		$sql1 = "SELECT Wert FROM attributefuersportstaetteauswahlwerte WHERE AName='".$row["AName"]."'";
		$result1 = $conn->query($sql1);
		
		if($result1 === false) {echo 'FEHLER: '.$conn->error;}
		
		echo '<tr><th style="vertical-align:top;padding-top: 25px;">'.$row["AName"].'</th>';
		//Keine bereits angegebenen Werte vorhanden
		if (count($AllgArray[$row["AName"]][1]) == 0)
		{
			echo '<td><select name="'.$row["AName"].'">';
			echo '<option value="Keine Angabe">Keine Angabe</option>';
			
			while($row1=$result1->fetch_assoc()){
				
				echo '<option value="'.$row1["Wert"].'">'.$row1["Wert"].'</option>';
			}
			echo '</select></td>';
		}
		$auswahlwerte = array();
		while($row1=$result1->fetch_assoc()){			
			$auswahlwerte[] = $row1["Wert"];
		}		
		echo "<td>";
		for($j=0;$j < count($AllgArray[$row["AName"]][1]);$j++)
		{
			if ($j==0)
				echo '<select name="'.$row["AName"].'">';
			else
				echo '<select name="'.$row["AName"].($j-1).'">';
			echo '<option value="Keine Angabe">Keine Angabe</option>';
			
			for($l=0;$l < count($auswahlwerte);$l++)
			{
				echo '<option value="'.$auswahlwerte[$l].'"';
				if ($auswahlwerte[$l] == $AllgArray[$row["AName"]][1][$j])
					echo 'selected="true"';
				echo '>'.$auswahlwerte[$l].'</option>';
			}
			echo "</select>";
		}		
		
		echo '</td><td style="text-align:left;vertical-align:top;min-width: 110px;"><button type="button" class="adder" style="margin:2px;">+</button><button type="button" class="miner">-</button></td></tr>';
	} else {echo 'FEHLER';}
	
}

echo '</table></br></br></div>';

//Erstellen eines Arrays zum Abgleichen der Eignung der Sportstätten für die Sportarten
$geeigneteSArten = array();
$sql5 = "SELECT * FROM eignungsass WHERE SSID=".$ID.";";
$result = $conn->query($sql5);

while($row = $result->fetch_assoc()) {
	$geeigneteSArten[] = $row["SAName"];
}

$sql = "SELECT Name FROM sportart ORDER BY Name";
$result = $conn->query($sql);


while($row = $result->fetch_assoc()) {
 

  echo '<button type=button class="hiddenchoice">'.$row["Name"].'</button> <div class="panel">';
  
  //Abfangen, ob bereits für die Sportart geeignet
  $found=false;
  for($k=0;$k < count($geeigneteSArten) && !$found;$k++)
  {
	if($geeigneteSArten[$k] == $row["Name"])
	{
		$found=true;
		array_splice($geeigneteSArten, $k, 1);
	}
	
  }
  if($found)
	echo '</br></br><td id="checkboxLine">'."F&uumlr Sportart ".$row["Name"]." geeignet:".'<input type="checkbox" value=1 name="'.$row["Name"].'bool" checked="true"></br></br></td></tr>';
  else
	echo '</br></br><td id="checkboxLine">'."F&uumlr Sportart ".$row["Name"]." geeignet:".'<input type="checkbox" value=1 name="'.$row["Name"].'bool"></br></br></td></tr>';
	
	for($i=0;$i<count($SuperArray[$row["Name"]][0]);$i++) {
		
	 if(substr($SuperArray[$row["Name"]][0][$i], 0, 1) != $firstL) // Anfangsbuchstabe
		{
			$firstL = substr($SuperArray[$row["Name"]][0][$i], 0, 1);
			echo '</table><p class="subgroup">'.$firstL.'</p><table id="inhalt" style="width:50%"><colgroup><col style="width: 25%" /><col style="width: 75%" /></colgroup>';
		}
	if($SuperArray[$row["Name"]][1][$i]== "int") {
		if(isset($SuperArray[$row["Name"]][2][$i])) {echo  '<tr><th>'.$SuperArray[$row["Name"]][0][$i].'</th><td><input type="number" value='.$SuperArray[$row["Name"]][2][$i].' name="'.$row["Name"].$i.'"></td><td style="text-align:left;vertical-align:top;min-width: 110px;"><button type="button" class="adder" style="display:none;margin:2px;">+</button><button type="button" class="miner" style="display:none;">-</button></td></tr>';}
		else {echo  '<tr><th>'.$SuperArray[$row["Name"]][0][$i].'</th><td><input type="number" name="'.$row["Name"].$i.'"></td><td style="text-align:left;vertical-align:top;min-width: 110px;"><button type="button" class="adder" style="display:none;margin:2px;">+</button><button type="button" class="miner" style="display:none;">-</button></td></tr>';}
	}
	elseif($SuperArray[$row["Name"]][1][$i]== "bool") {
		if(isset($SuperArray[$row["Name"]][2][$i])) {if($SuperArray[$row["Name"]][2][$i]=="1"){echo  '<tr><th>'.$SuperArray[$row["Name"]][0][$i].'</th><td><input type="checkbox" value="1" checked name="'.$row["Name"].$i.'"></td><td style="text-align:left;vertical-align:top;min-width: 110px;"><button type="button" class="adder" style="display:none;margin:2px;">+</button><button type="button" class="miner" style="display:none;">-</button></td></tr>';}} //oder:{echo  '<tr><th>'.$valuesAName[$i].'</th><td><input type="radio" value=1 name="vari'.$i.'"> Ja <input type="radio" value=0 name="vari'.$i.'"> Nein <input type="radio" value=2 name="vari'.$i.'" checked> keine Angabe</td></tr>';}
		else {echo  '<tr><th>'.$SuperArray[$row["Name"]][0][$i].'</th><td><input type="checkbox" value="1" name="'.$row["Name"].$i.'"></td><td style="text-align:left;vertical-align:top;min-width: 110px;"><button type="button" class="adder" style="display:none;margin:2px;">+</button><button type="button" class="miner" style="display:none;">-</button></td></tr>';}
	}
	elseif($SuperArray[$row["Name"]][1][$i]== "char") {
		if(isset($SuperArray[$row["Name"]][2][$i])) {echo  '<tr><th>'.$SuperArray[$row["Name"]][0][$i].'</th><td><input type="text" value="'.$SuperArray[$row["Name"]][2][$i].'" name="'.$row["Name"].$i.'"></td><td style="text-align:left;vertical-align:top;min-width: 110px;"><button type="button" class="adder" style="display:none;margin:2px;">+</button><button type="button" class="miner" style="display:none;">-</button></td></tr>';}
		else {echo  '<tr><th>'.$SuperArray[$row["Name"]][0][$i].'</th><td><input type="text" name="'.$row["Name"].$i.'"></td><td style="text-align:left;vertical-align:top;min-width: 110px;"><button type="button" class="adder" style="display:none;margin:2px;">+</button><button type="button" class="miner" style="display:none;">-</button></td></tr>';}
	}
	elseif($SuperArray[$row["Name"]][1][$i]=="charc") {
		
		//TODO: wenn das $SuperArray[$row["Name"]][2][$i][] mehrere Werte hat müssen mehrere choice Felder geaddet werden, die als ausgewählten value die Werte aus $SuperArray[$row["Name"]][2][$i][] nehmen
		
		$sql1 = "SELECT Wert FROM attributefuersportstaetteauswahlwerte WHERE AName='".$SuperArray[$row["Name"]][0][$i]."';";
		$result1 = $conn->query($sql1);
		
		if($result1 === false) {echo 'FEHLER: '.$conn->error;}
		
		echo '<tr><th style="vertical-align:top;padding-top: 25px;">'.$SuperArray[$row["Name"]][0][$i].'</th>';
		//Keine bereits angegebenen Werte vorhanden
		if (count($SuperArray[$row["Name"]][2][0]) == 0)
		{
			echo '<td><select name="'.$row["Name"].$i.'">';
			echo '<option value="Keine Angabe">Keine Angabe</option>';
			
			while($row1=$result1->fetch_assoc()){
				
				echo '<option value="'.$row1["Wert"].'">'.$row1["Wert"].'</option>';
			}
			echo '</select></td>';
		}
		else
			echo "<td>";
		$auswahlwerte = array();
		while($row1=$result1->fetch_assoc()){			
			$auswahlwerte[] = $row1["Wert"];
		}				
		for($j=0;$j < count($SuperArray[$row["Name"]][2][$i]);$j++)
		{
			if ($j==0)
				echo '<select name="'.$row["Name"].$i.'">';
			else
				echo '<select name="'.$row["Name"].$i.($j-1).'">';
			echo '<option value="Keine Angabe">Keine Angabe</option>';		
			for($l=0;$l < count($auswahlwerte);$l++)
			{
				echo '<option value="'.$auswahlwerte[$l].'"';			
				if ($auswahlwerte[$l] == $SuperArray[$row["Name"]][2][$i][$j])
					echo 'selected="true"';
				echo '>'.$auswahlwerte[$l].'</option>';
			}
			
			echo "</select>";
		}	
		if (count($SuperArray[$row["Name"]][2][$i]) == 0)
		{
			echo '<select name="'.$row["Name"].$i.'">';
			echo '<option value="Keine Angabe">Keine Angabe</option>';		
			for($l=0;$l < count($auswahlwerte);$l++)
			{
				echo '<option value="'.$auswahlwerte[$l].'"';			
				echo '>'.$auswahlwerte[$l].'</option>';
			}
			
			echo "</select>";
		}
		
		echo '</td><td style="text-align:left;vertical-align:top;min-width: 110px;"><button type="button" class="adder" style="margin:2px;">+</button><button type="button" class="miner">-</button></td></tr>';
	} else {echo 'FEHLER';}
		
	}
	
	
	echo '</table></br></br></div>';
}
echo '<table id="comment" style="width:70%"><tr><th>Kommentar</th><td><textarea rows="6" cols="50" name="Kommentar" style="width:75%;margin-left:12%;">';
//Falls im Textarea etwas enthalten ist, schreibe es rein
$sql = "SELECT Kommentar FROM sportstaette WHERE ID=".$ID.";";
$result = $conn->query($sql);
	while($row=$result->fetch_assoc()){			
		echo $row["Kommentar"];
	}
echo'</textarea></td></tr></table>';

//Preise
echo '<table id="middle" style="width:80%;">
<colgroup>
    <col style="width: 20%" />
    <col style="width: 60%" />
	<col style="width: 20%" />
  </colgroup>';

$sql = 'SELECT KommentarPreis FROM sportstaette WHERE ID='.$ID.';';
$x = $conn->query($sql);
$row = $x->fetch_assoc();

$preisDB = $row["KommentarPreis"];

$DBnotNull = true;
if(is_null($preisDB)) {$DBnotNull = false;}
$jahre = explode("\\", $preisDB);

//echo '<tr><th style="vertical-align:top;"></br></br>Preise</th><td id="elements"><table><tr><th>';
$sammler = "";
for($i=2;$i>=0;$i--) {
		$counterJahr=$i + 1;
	$sammlerInner = "";
	$jahr;
	if($DBnotNull) {$jahr = explode("|", $jahre[$i]);}
	
	$sammlerInner.= '<tr><th style="vertical-align:top;"></br></br>';
	if($i==0) {$sammlerInner.= 'Preise';}
	$sammlerInner.= '</th><td><table style="margin-top:20px;"><tr><th><input type="text" name="Jahr'.$counterJahr.'" placeholder="Jahreszahl '.$counterJahr.'" ';
	if($DBnotNull && $jahr[0] != "") {$sammlerInner.= 'value="'.$jahr[0].'"';}
	$sammlerInner.= '></th><td></br></td></tr>';
	
	$counter = 5;
	$zwischen = "";
	$rowCounter = 0;
	$hasContent = false;
	for($j=9;$j>=1;$j=$j-2) 
	{
		$inner = "";
		if(($DBnotNull && $jahr[$j] != "")|| $hasContent) 
		{ 
			$inner  = '<tr><th><input type="text" name="Typ'.$counterJahr.'.'.$counter.'" placeholder="Typ '.$counter.'" '.
			'value="'.$jahr[$j].'"></th>';
			$hasContent = true;
			$rowCounter++;
		}
		if(($DBnotNull && $jahr[$j + 1] != "") || $hasContent)
		//if(($DBnotNull) || $hasContent)
		{
			$inner  .=  '<td><input type="text" name="Preis'.$counterJahr.'.'.$counter.'" placeholder="Preis '.$counter.'" '.
			'value="'.$jahr[$j + 1].'"></td>';
			$inner  .='</tr>';			
			$hasContent = true;
		}
		$zwischen = $inner . $zwischen;
		$counter--;
	}
	if ($DBnotNull && $zwischen == "")
	{
		$zwischen = '<tr><th><input type="text" name="Typ'.$counterJahr.'.1" placeholder="Typ 1"></th>'.
		'<td><input type="text" name="Preis'.$counterJahr.'.1" placeholder="Preis 1"></td></table></td>'.
		'<td style="text-align:left;vertical-align:top; width:40%;">
		
		<button type="button" class="adderP" value="1" id="'.$counterJahr.'A" style="margin:2px;margin-top:25px;">+</button><button type="button" class="minerP">-</button></td></tr>';
	}
	else
		$zwischen .= '</table></td><td style="text-align:left;vertical-align:top; width:40%;">
				
				<button type="button" class="adderP" value="'.$rowCounter.'" id="'.$counterJahr.'A" style="margin:2px;margin-top:25px;">+</button><button type="button" class="minerP">-</button></td></tr>';

	$sammlerInner.= $zwischen; 
	//$sammlerInner.= '</table>';
	$sammler = $sammlerInner . $sammler;
	$counterJahr--;
}
if ($sammler != "")
		echo $sammler;
echo '</table>';



echo '</div>';

echo '<br></br></br><input id="submitB" type = submit value="Speichern"> 
<button class="reset" type="reset" value="Reset" style="float:left;margin-left:10%;">Änderungen zurücksetzen</button>
</form>';
 

$conn->close();

?>

<script>
function toggleUpDown_(ele){
	ele.classList.toggle("active");
    var panel = ele.nextElementSibling;
	if (panel != null)
	{
		if (panel.style.maxHeight){
		  panel.style.maxHeight = null;
		} else {
		  panel.style.maxHeight = panel.scrollHeight + "px";
		} 
	}
}

function toggleUpDown(ele)
{
	// Schliesse alle anderen offenen Reiter
	var ele_active = false;
	var closing = document.getElementsByClassName("active");
	if(closing != null){
		for (i = 0; i < closing.length; i++) {
			if(closing[i] == ele){
				ele_active = true;
			}
			toggleUpDown_(closing[i]);
		}
	}
	
	if(!ele_active)
	{
		toggleUpDown_(ele);
	}
}

var acc = document.getElementsByClassName("hiddenchoice");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function(){toggleUpDown(this)});
}

var clicker = document.getElementsByClassName("adder");
for(i = 0; i < clicker.length;i++)
{
	clicker[i].addEventListener('click',function(e){
	console.log(e);
	e = e || window.event;
	console.log(e);
	var target = e.target || e.srcElement;
	console.log(target);
	var locid = target.id;
	//Navigate to source select
	var source = target.parentNode.previousSibling.firstChild;
	console.log(source);
	var j = 0;
	j = target.parentNode.previousSibling.childNodes.length-1;
	console.log(j);
	var newNode = document.createElement("select");
	newNode.innerHTML = source.innerHTML;
	newNode.name = source.name + j;
	target.parentNode.previousSibling.appendChild(newNode);
	var panel = target.parentNode.parentNode.parentNode.parentNode.parentNode;
	panel.style.maxHeight = panel.scrollHeight + "px";
	
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
	var source = target.parentNode.previousSibling.lastChild;
	
	if((target.parentNode.previousSibling.childNodes.length-1) != 0)
	{
		target.parentNode.previousSibling.removeChild(source);
		var panel = target.parentNode.parentNode.parentNode.parentNode.parentNode;
		panel.style.maxHeight = panel.scrollHeight + "px";
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

var clicker = document.getElementsByClassName("adderP");
	for(i = 0; i < clicker.length;i++)
	{
		clicker[i].addEventListener('click',function(e){
		e = e || window.event;
		var target = e.target || e.srcElement;
		var locid = target.id;
		//Navigate to source select
		var source = target.parentNode.parentNode.firstChild.nextSibling.firstChild.firstChild;
		console.log(source);
		var i = 0;
		i = parseInt(target.value);
		if (i < 5)
		{
			i++;
			target.value = i;
			var j= target.id.substring(0,1);
			var newNode = document.createElement("tr");
			newNode.innerHTML = '<tr><th><input type="text" name="Typ'+j+'.'+i+'" placeholder="Typ '+i+'" maxlength=30></th><td><input type="text" name="Preis'+j+'.'+i+'" placeholder="Preis '+i+'" maxlength=8></td>';			
			source.appendChild(newNode);
		}
		//newNode.addEventListener("click", function(){changed = true});
	},false);
	}
	var clicker = document.getElementsByClassName("minerP");
	for(i = 0; i < clicker.length;i++)
	{
		clicker[i].addEventListener('click',function(e){
		e = e || window.event;
		var target = e.target || e.srcElement;
		//var locid = target.id;
		//Navigiere zuerst zum Adder Button, da bei diesem alle Infos abgelegt wurden
		target = target.previousSibling;
		var i = parseInt(target.value);
		var j = target.id.substring(0,1);
		//Überprüfung, ob noch mehr als ein Element vorhanden ist
		if (i > 1)
		{
			var source = target.parentNode.parentNode.firstChild.nextSibling.firstChild.firstChild;
			var removeable = document.getElementsByName("Typ"+j+"."+i)[0].parentNode.parentNode;
			source.removeChild(removeable);			
			target.value = i-1;
		}
		
	},false);
	}	


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

	
	
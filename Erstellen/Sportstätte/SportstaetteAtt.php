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
  <LINK rel="stylesheet" href="../Anlegenstyle.css">
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

include('../../database.php');
$conn = getConnection();
?>

 <script>
function leave_site() {
	window.location.replace("SHomebutton.php");
}
</script>

<div class="color" id="colorS2">
<button type="button" onclick="leave_site()" class="HomeB">Home</button></br></br>
<h1>Kriterien</h1></br></br>
</div>
<div class="circle"><i style='font-size:50px;margin-top:35%;color:grey;' class='fas'>&#xf65e;</i></div>

<?php

//Einfügen in DB

$sql = "SELECT Name FROM sportart ORDER BY Name";
$result = $conn->query($sql);

$comment;

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
	
	while($row1 = $result1->fetch_assoc()) {
		
		$sql2 = "SELECT * From attributefuersportstaette WHERE AName ='".$row1["AName"]."'";
		$result2 = $conn->query($sql2);
		
		while($row2 = $result2->fetch_assoc()) {
			$SuperArray[$row["Name"]][0][] = $row2["AName"];
			$SuperArray[$row["Name"]][1][] = $row2["Typ"];
		}
	}
}


if($_SERVER["REQUEST_METHOD"]=="POST") {
	
	//var_dump($_POST); echo "<br>";
	
	
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
	
	if($_POST["comment"]!=NULL && $_POST["comment"]!="") {$comment = $_POST["comment"];} else $comment = NULL; //!- Hinteres Stück hinzugefügt, damit die Notics weggehen, dass $comment nicht gesetzt ist
	
	$fehler=0;
	//var_dump($SuperArray);
	
	
	$sql='SELECT * FROM attributefuersportstaette WHERE AName NOT IN (SELECT AName FROM zuordnungsaa) ORDER BY AName;';
	$result = $conn->query($sql);
	
	while($row = $result->fetch_assoc()) {
		$ConvertedAName = str_replace(" ","_",$row["AName"]);
		if(isset($_POST[$ConvertedAName])) {
			if($row["Typ"] == "int" && $_POST[$ConvertedAName] != NULL) {
				$sqlneu = "INSERT INTO ssbesitzt_int(SSID, AName, Wert) VALUES (".$_SESSION["SID"].",'".$row["AName"]."',".$_POST[$ConvertedAName].")";
				
				$x = $conn->query($sqlneu);
				if($x === false) {$fehler=1; echo $conn->error;}
			}
			
			if($row["Typ"] == "bool" && isset($_POST[$ConvertedAName])) {
				if($_POST[$ConvertedAName] != NULL){
					$sqlneu = "INSERT INTO ssbesitzt_bool(SSID, AName, Wert) VALUES (".$_SESSION["SID"].",'".$row["AName"]."',".$_POST[$ConvertedAName].")";
					
					$x = $conn->query($sqlneu);
					if($x === false) {$fehler=1; echo $conn->error;}
				}
			}
			
			if($row["Typ"] == "char" && $_POST[$ConvertedAName] != NULL && $_POST[$ConvertedAName] != "") {
				$sqlneu = "INSERT INTO ssbesitzt_char(SSID, AName, Wert) VALUES (".$_SESSION["SID"].",'".$row["AName"]."',".$_POST[$ConvertedAName].")";
				
				$x = $conn->query($sqlneu);
				if($x === false) {$fehler=1; echo $conn->error;}
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
					$sqlneu = "INSERT INTO ssbesitzt_char(SSID, AName, Wert) VALUES (".$_SESSION["SID"].",'".$row["AName"]."','".$charArrayAllg[$k]."')";
				
					$x = $conn->query($sqlneu);
					if($x === false) {$fehler=1; echo $conn->error;}
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
				$sqlneu = "INSERT INTO ssbesitzt_int(SSID, AName, Wert) VALUES (".$_SESSION["SID"].",'".$SuperArray[$row["Name"]][0][$j]."',".$SuperArray[$row["Name"]][2][$j].")";
				
				$x = $conn->query($sqlneu);
				if($x === false) {$fehler=1; echo $conn->error;}
			}
			
			if($SuperArray[$row["Name"]][1][$j] == "bool" && isset($SuperArray[$row["Name"]][2][$j])) {
				if($SuperArray[$row["Name"]][2][$j] != NULL) {
					$sqlneu = "INSERT INTO ssbesitzt_bool(AName, SSID, Wert) VALUES ('".$SuperArray[$row["Name"]][0][$j]."',".$_SESSION["SID"].",".$SuperArray[$row["Name"]][2][$j].")";
					
					$x = $conn->query($sqlneu);
					if($x === false) {$fehler=1; echo $conn->error;}
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
					$sqlneu = "INSERT INTO ssbesitzt_char(SSID, AName, Wert) VALUES (".$_SESSION["SID"].",'".$SuperArray[$row["Name"]][0][$j]."','".$charArray[$k]."')";
				
					$x = $conn->query($sqlneu);
					if($x === false) {$fehler=1; echo $conn->error;}
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
			$sqlneu = "INSERT INTO eignungsass(SAName, SSID) VALUES ('".$row["Name"]."',".$_SESSION["SID"].")";
			
			//!- Query Ausführung hinzugefügt, da diese fehlte
			$x = $conn->query($sqlneu);
			if($x === false) {$fehler=1; echo $conn->error;}
		}
	}
	
	if($comment!=NULL) {
		$sqlneu = "UPDATE sportstaette SET Kommentar='".$comment."' WHERE ID=".$_SESSION["SID"];
		
		$x = $conn->query($sqlneu);
		if($x === false) {$fehler=1; echo $conn->error;}
	}
	
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
	
	$sql = 'UPDATE sportstaette SET KommentarPreis="'.$preis.'" WHERE ID='.$_SESSION["SID"].';';
	$x = $conn->query($sql);
	
	if($x === false) {$fehler=1; echo $conn->error;}
	
	if($fehler==0) {
		header('Location: SportstaetteEnde.php');
		
	} else {echo "<br>Fehler beim Einf&uumlgen in die Datenbank<br>";}
}

//========================================================================================================================
//Attribute form


echo '<div class="sportart">';
echo '<form action ="SportstaetteAtt.php" method="Post">';

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
	if($row["Typ"] == "int") {echo  '<tr><th>'.$row["AName"].'</th><td><input type="number" name="'.$row["AName"].'"></td><td style="text-align:left;vertical-align:top;min-width: 110px;"><button type="button" class="adder" style="display:none;margin:2px;">+</button><button type="button" class="miner" style="display:none;">-</button></td></tr>';}
	elseif($row["Typ"] == "bool") {echo  '<tr><th>'.$row["AName"].'</th><td><input type="checkbox" value="1" name="'.$row["AName"].'"></td><td style="text-align:left;vertical-align:top;min-width: 110px;"><button type="button" class="adder" style="display:none;margin:2px;">+</button><button type="button" class="miner" style="display:none;">-</button></td></tr>';}  //oder:{echo  '<tr><th>'.$valuesAName[$i].'</th><td><input type="radio" value=1 name="vari'.$i.'"> Ja <input type="radio" value=0 name="vari'.$i.'"> Nein <input type="radio" value=2 name="vari'.$i.'" checked> keine Angabe</td></tr>';}
	elseif($row["Typ"] == "char") {echo  '<tr><th>'.$row["AName"].'</th><td><input type="text" name="'.$row["AName"].'"></td><td style="text-align:left;vertical-align:top;min-width: 110px;"><button type="button" class="adder" style="display:none;margin:2px;">+</button><button type="button" class="miner" style="display:none;">-</button></td></tr>';}
	elseif($row["Typ"] =="charc") {
		
		$sql1 = "SELECT Wert FROM attributefuersportstaetteauswahlwerte WHERE AName='".$row["AName"]."'";
		$result1 = $conn->query($sql1);
		
		if($result1 === false) {echo 'FEHLER: '.$conn->error;}
		
		echo '<tr><th style="vertical-align:top;padding-top: 25px;">'.$row["AName"].'</th><td><select name="'.$row["AName"].'">';
		echo '<option value="Keine Angabe">Keine Angabe</option>';
		
		while($row1=$result1->fetch_assoc()){
			
			echo '<option value="'.$row1["Wert"].'">'.$row1["Wert"].'</option>';
		}
		
		echo '</select></td><td style="text-align:left;vertical-align:top;min-width: 110px;"><button type="button" class="adder" style="margin:2px;">+</button><button type="button" class="miner">-</button></td></tr>';
	} else {echo 'FEHLER';}
}

echo '</table></br></br></div>';



$sql = "SELECT Name FROM sportart ORDER BY Name";
$result = $conn->query($sql);


while($row = $result->fetch_assoc()) {
 

  echo '<button type=button class="hiddenchoice">'.$row["Name"].'</button> <div class="panel">';
  
  echo '</br></br><td id="checkboxLine">'."F&uumlr Sportart ".$row["Name"]." geeignet:".'<input type="checkbox" value=1 name="'.$row["Name"].'bool"></br></br></td></tr>';
	
	for($i=0;$i<count($SuperArray[$row["Name"]][0]);$i++) {
		
	 if(substr($SuperArray[$row["Name"]][0][$i], 0, 1) != $firstL) // Anfangsbuchstabe
		{
			$firstL = substr($SuperArray[$row["Name"]][0][$i], 0, 1);
			echo '</table><p class="subgroup">'.$firstL.'</p><table id="inhalt" style="width:50%"><colgroup><col style="width: 25%" /><col style="width: 75%" /></colgroup>';
		}
	if($SuperArray[$row["Name"]][1][$i]== "int") {echo  '<tr><th>'.$SuperArray[$row["Name"]][0][$i].'</th><td><input type="number" name="'.$row["Name"].$i.'"></td><td style="text-align:left;vertical-align:top;min-width: 110px;"><button type="button" class="adder" style="display:none;margin:2px;">+</button><button type="button" class="miner" style="display:none;">-</button></td></tr>';}
	elseif($SuperArray[$row["Name"]][1][$i]== "bool") {echo  '<tr><th>'.$SuperArray[$row["Name"]][0][$i].'</th><td><input type="checkbox" value="1" name="'.$row["Name"].$i.'"></td><td style="text-align:left;vertical-align:top;min-width: 110px;"><button type="button" class="adder" style="display:none;margin:2px;">+</button><button type="button" class="miner" style="display:none;">-</button></td></tr>';}  //oder:{echo  '<tr><th>'.$valuesAName[$i].'</th><td><input type="radio" value=1 name="vari'.$i.'"> Ja <input type="radio" value=0 name="vari'.$i.'"> Nein <input type="radio" value=2 name="vari'.$i.'" checked> keine Angabe</td></tr>';}
	elseif($SuperArray[$row["Name"]][1][$i]== "char") {echo  '<tr><th>'.$SuperArray[$row["Name"]][0][$i].'</th><td><input type="text" name="'.$row["Name"].$i.'"></td><td style="text-align:left;vertical-align:top;min-width: 110px;"><button type="button" class="adder" style="display:none;margin:2px;">+</button><button type="button" class="miner" style="display:none;">-</button></td></tr>';}
	elseif($SuperArray[$row["Name"]][1][$i]=="charc") {
		
		$sql1 = "SELECT Wert FROM attributefuersportstaetteauswahlwerte WHERE AName='".$SuperArray[$row["Name"]][0][$i]."'";
		$result1 = $conn->query($sql1);
		
		if($result1 === false) {echo 'FEHLER: '.$conn->error;}
		
		echo '<tr><th style="vertical-align:top;padding-top: 25px;">'.$SuperArray[$row["Name"]][0][$i].'</th><td><select name="'.$row["Name"].$i.'">';
		echo '<option value="Keine Angabe">Keine Angabe</option>';
		
		while($row1=$result1->fetch_assoc()){
			
			echo '<option value="'.$row1["Wert"].'">'.$row1["Wert"].'</option>';
		}
		
		echo '</select></td><td style="text-align:left;vertical-align:top;min-width: 110px;"><button type="button" class="adder" style="margin:2px;">+</button><button type="button" class="miner">-</button></td></tr>';
		} else {echo 'FEHLER';}
	}
	
	
	echo '</table></br></br></div>';
}
echo '<table id="comment" style="width:70%"><tr><th>Kommentar</th><td><textarea rows="6" cols="50" name="comment" style="width:75%;margin-left:12%;"></textarea></td></tr></table>';



//Preise
echo '<table id="middle" style="width:80%;">
<colgroup>
    <col style="width: 20%" />
    <col style="width: 60%" />
	<col style="width: 20%" />
  </colgroup>';

//Preise

echo '<tr><th style="vertical-align:top;"></br></br>Preise</th><td id="elements"><table style="margin-top:20px;"><tr><th><input type="text" name="Jahr1" placeholder="Jahreszahl 1"></th>
<td></br></td></tr><tr><th><input type="text" name="Typ1.1" placeholder="Typ 1"></th><td><input type="text" name="Preis1.1" placeholder="Preis 1"></td></tr>
</table></td><td style="text-align:left;vertical-align:top;"></br><input type="text" style="visibility:hidden"><button type="button" class="adderP" value=1 id="1A" style="margin:2px;">+</button><button type="button" class="minerP">-</button></td></tr>

<tr><th style="vertical-align:top;"></br></br></th><td><table><tr><th><input type="text" name="Jahr2" placeholder="Jahreszahl 2"></th><td></br></td></tr><tr><th><input type="text" name="Typ2.1" placeholder="Typ 1"></th>
<td><input type="text" name="Preis2.1" placeholder="Preis 1"></td></tr>
</table></td><td style="text-align:left;vertical-align:top;"></br><input type="text" style="visibility:hidden"><button type="button" class="adderP" value=1 id="2A" style="margin:2px;">+</button><button type="button" class="minerP">-</button></td></tr>

<tr><th style="vertical-align:top;"></br></br></th><td><table><tr><th><input type="text" name="Jahr3" placeholder="Jahreszahl 3"></th><td></br></td></tr><tr><th><input type="text" name="Typ3.1" placeholder="Typ 1"></th>
<td><input type="text" name="Preis3.1" placeholder="Preis 1"></td></tr></table></td>
<td style="text-align:left;vertical-align:top;"></br><input type="text" style="visibility:hidden"><button type="button" class="adderP" value=1 id="3A" style="margin:2px;">+</button><button type="button" class="minerP">-</button></td></tr>';

echo '</table>';


echo '</div>';

echo '<br></br></br><input id="submitB" type = submit value="Speichern"> </form>';
 

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
		var locid = target.id;
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
var changed = true;
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
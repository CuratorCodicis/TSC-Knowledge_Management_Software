<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Kriterien</title>
  <meta charset="UTF-8">
  <LINK rel="stylesheet" href="\general.css">
  <LINK rel="stylesheet" href="\colors.css">
  <LINK rel="stylesheet" href="\Icons/css/all.css">
 <style> 
 body{
	 text-align:center;
 }
  table, th, td {
    border: none;
    border-collapse: collapse;
}
#middle{
	margin-left: auto;
	margin-right:auto;
	margin-top: 3%;
}
td {
    text-align: center; 	
}
th {
    text-align: right;
}
#number{
	width: 90%;
}
#adder
{
	text-align: left; 
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

<div class="color" id="colorU1">
<button type="button" onclick="leave_site()" class="HomeB">Home</button></br></br>
<h1>Kriterien</h1></br></br>
</div>
<div class="circle"><i style='font-size:50px;margin-top:35%;color:grey;' class='far'>&#xf044;</i></div>

<?php

$ID = $_SESSION["UID"];

//Ausgangswerte für form auslesen
$Kommentar;

$sql = 'SELECT Kommentar FROM unterkunft WHERE ID='.$ID.';';
$result = $conn->query($sql);

if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}

while ($row=$result->fetch_assoc()) {
	$Kommentar = $row["Kommentar"];
}

if(isset($_POST["Kommentar"])) $Kommentar = $_POST["Kommentar"];

$sql="SELECT * FROM attributefuerunterkunft ORDER BY AName";
$attribute = $conn->query($sql);

if($attribute === false) {echo 'FEHLER: '.$conn->error;}

$valuesAName = array();
$valuesTyp = array();
$values = array();

$j=0;

while($row = $attribute->fetch_assoc()) {
	$valuesAName[$j] = $row["AName"];
	$valuesTyp[$j] = $row["Typ"];
	
	$sql;
	
	if($row["Typ"]=="bool" || $row["Typ"]=="int" || $row["Typ"]=="char") {
		if($row["Typ"]=="bool") {$sql = 'SELECT * FROM ubesitzt_bool WHERE UID='.$ID.' AND AName="'.$row["AName"].'";';}
		if($row["Typ"]=="int") {$sql = 'SELECT * FROM ubesitzt_int WHERE UID='.$ID.' AND AName="'.$row["AName"].'";';}
		if($row["Typ"]=="char") {$sql = 'SELECT * FROM ubesitzt_char WHERE UID='.$ID.' AND AName="'.$row["AName"].'";';}

		
		$result2 = $conn->query($sql);
		if ($result2 === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
		
		$foundOne = false;
		while ($row2=$result2->fetch_assoc()) {
			$values[$j]=$row2["Wert"];
			$foundOne = true;
		}
		if (!$foundOne)
			$values[$j]=null;
		
	} else {
		if($row["Typ"]=="charc") {
			$sql = 'SELECT * FROM ubesitzt_char WHERE UID='.$ID.' AND AName="'.$row["AName"].'";';
			$result2 = $conn->query($sql);
			$values[$j]=array();
			
			while ($row2=$result2->fetch_assoc()) {
				$values[$j][]=$row2["Wert"];
			}
		}
	}
	$j++;
}
//-------------------------------------------------------------------------------------------------------------------------------------

//form

echo'<div>
<form action ="AttBUnterkunft.php" method="Post">
<table id="middle" style="width:60%">
<colgroup>
    <col style="width: 33%" />
    <col style="width: 33%" />
	<col style="width: 33%" />
  </colgroup>';

  $counterValues =0;
for($i=0;$i<count($valuesAName);$i++) {
	
	if($valuesTyp[$i]== "int") {
		if(is_null($values[$i])) {echo  '<tr><th>'.$valuesAName[$i].'</th><td><input type="number" id="number" name="vari'.$i.'"></td><td></td></tr>';}
	else {echo  '<tr><th>'.$valuesAName[$i].'</th><td><input type="number" id="number" value='.$values[$i].' name="vari'.$i.'"></td><td></td></tr>';}
	}
	
	elseif($valuesTyp[$i]== "bool") {
		if($values[$i] != '1') {echo  '<tr><th>'.$valuesAName[$i].'</th><td><input type="checkbox" value=1 name="vari'.$i.'"></td></tr>';}
		else {echo  '<tr><th>'.$valuesAName[$i].'</th><td><input type="checkbox" value=1 checked name="vari'.$i.'"></td></tr>';}
		}  //oder:{echo  '<tr><th>'.$valuesAName[$i].'</th><td><input type="radio" value=1 name="vari'.$i.'"> Ja <input type="radio" value=0 name="vari'.$i.'"> Nein <input type="radio" value=2 name="vari'.$i.'" checked> keine Angabe</td></tr>';}
	
	elseif($valuesTyp[$i]== "char") {
		if (is_null($values[$i])) {echo  '<tr><th>'.$valuesAName[$i].'</th><td><input type="text" name="vari'.$i.'"></td></tr>';}
		else {echo  '<tr><th>'.$valuesAName[$i].'</th><td><input type="text" value="'.$values[$i].'" name="vari'.$i.'"></td></tr>';}
	}
	
	elseif($valuesTyp[$i]=="charc") {
		
		//TODO: wenn das values[][] mehrere Werte hat müssen mehrere choice Felder geaddet werden, die als ausgewählten value die Werte aus values[][] nehmen					
		
		$sql = "SELECT Wert FROM attributefuerunterkunftauswahlwerte WHERE AName='".$valuesAName[$i]."' ORDER BY Wert";
		$result = $conn->query($sql);
		
		if($result === false) {echo 'FEHLER: '.$conn->error;}
		$auswahlwerte = array();
		while($row=$result->fetch_assoc()){
			$auswahlwerte[] = $row["Wert"];
		}
		
		echo '<tr><th style="vertical-align:top;padding-top: 25px;">'.$valuesAName[$i].'</th><td>';
		for($k=0; $k < count($values[$i]);$k++)
		{
			if ($k==0)
				echo '<select name="vari'.$i.'">';
			else
				echo '<select name="vari'.$i.($k-1).'">';
			echo '<option value="Keine Angabe">Keine Angabe</option>';
			for($l=0;$l<count($auswahlwerte);$l++)
			{
				echo '<option value="'.$auswahlwerte[$l].'"';
				if ($auswahlwerte[$l] == $values[$i][$k])
					echo 'selected="true"';
				echo'>'.$auswahlwerte[$l].'</option>';		
			}
			echo '</select>';
		}	
		if (count($values[$i]) ==0)
		{
			echo '<select name="vari'.$i.'">';
			echo '<option value="Keine Angabe">Keine Angabe</option>';
			for($l=0;$l<count($auswahlwerte);$l++)
			{
				echo '<option value="'.$auswahlwerte[$l].'"';
				echo'>'.$auswahlwerte[$l].'</option>';		
			}
			echo '</select>';
		}
		echo '</td><td style="text-align:left;vertical-align:top;"><button type="button" class="adder" style="margin:2px;">+</button><button type="button" class="miner">-</button></td></tr>';
		
		} else {echo 'FEHLER';}
		
	
}
echo '<tr><th></br>Kommentar</th><td></br><textarea rows="4" cols="50" name="Kommentar">';
//Falls im Textarea etwas enthalten ist, schreibe es rein
$sql = "SELECT Kommentar FROM unterkunft WHERE ID=".$ID.";";
$result = $conn->query($sql);
	while($row=$result->fetch_assoc()){			
		echo $row["Kommentar"];
	}
echo '</textarea></td></tr>';

//Preise
$sql = 'SELECT KommentarPreis FROM unterkunft WHERE ID='.$ID.';';
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
if($_SERVER["REQUEST_METHOD"]!="POST")
{	if ($sammler != "")
		echo $sammler;

echo '</table>';
echo '<input type = submit id="submitB" value="Speichern"> 
<button class="reset" type="reset" value="Reset" style="float:left;margin-left:10%;">Änderungen zurücksetzten</button>
</form>
</div>';
}

//==========================================================================================================================================

//DB Verarbeitung

if($_SERVER["REQUEST_METHOD"]=="POST") {
	$fehler=0;
	
	if(is_null($Kommentar) || $Kommentar == "") {
		$sql = 'UPDATE unterkunft SET Kommentar = NULL WHERE ID='.$ID.';';
		$result = $conn->query($sql);
		
		if ($result === FALSE) {$fehler=1; echo "Error: " . $sql . "<br>" . $conn->error;}
	} else {
		$sql = 'UPDATE unterkunft SET Kommentar = "'.$Kommentar.'" WHERE ID='.$ID.';';
		$result = $conn->query($sql);
		
		if ($result === FALSE) {$fehler=1; echo "Error: " . $sql . "<br>" . $conn->error;}
	}
	
	//delete from DB where ID = Session ID
	
	//delete bool
	$sql = 'DELETE FROM ubesitzt_bool WHERE UID='.$ID.';';
	$result = $conn->query($sql);
	//echo $sql."<br>";
	
	if ($result === FALSE) {$fehler=1; echo "Error: " . $sql . "<br>" . $conn->error;}
	
	
	//delete char and charc
	$sql = 'DELETE FROM ubesitzt_char WHERE UID='.$ID.';';
	$result = $conn->query($sql);
	//echo $sql."<br>";
	
	if ($result === FALSE) {$fehler=1; echo "Error: " . $sql . "<br>" . $conn->error;}
	
	
	//delete int
	$sql = 'DELETE FROM ubesitzt_int WHERE UID='.$ID.';';
	$result = $conn->query($sql);
	//echo $sql."<br>Break</br>";
	
	if ($result === FALSE) {$fehler=1; echo "Error: " . $sql . "<br>" . $conn->error;}
	
	//-----------------------------------------------------------------------------------
	//insert into DB
	
	for ($k=0;$k<count($valuesAName);$k++) {
		//insert bool
		if($valuesTyp[$k]=="bool" && isset($_POST["vari".$k]) && $_POST["vari".$k]=="1") {
			$sql = 'INSERT INTO ubesitzt_bool (UID, AName, Wert) VALUES ('.$ID.',"'.$valuesAName[$k].'", 1);';
			$result = $conn->query($sql);
			//echo $sql."<br>bool</br>";
			
			if ($result === FALSE) {$fehler=1; echo "Error: " . $sql . "<br>" . $conn->error; }
		}
		
		//insert int
		if($valuesTyp[$k]=="int" && isset($_POST["vari".$k]) && (trim($_POST["vari".$k]) != "")) {
			$sql = 'INSERT INTO ubesitzt_int (UID, AName, Wert) VALUES ('.$ID.',"'.$valuesAName[$k].'", '.$_POST["vari".$k].');';
			$result = $conn->query($sql);
			//echo $sql."<br>int</br>";
			
			if ($result === FALSE) {$fehler=1; echo "Error: " . $sql . "<br>" . $conn->error;}
		}
		
		//insert char and charc
		//insert int
		if(($valuesTyp[$k]=="char" || $valuesTyp[$k]=="charc") && isset($_POST["vari".$k])) {
			$charArray = array();
			
			if($_POST["vari".$k] != "Keine Angabe" && $_POST["vari".$k] != "") {
				$charArray[] = $_POST["vari".$k];
			}
						
			$counter=0;
			while(isset($_POST["vari".$k.$counter])) {
				if(!in_array($_POST["vari".$k.$counter], $charArray) && $_POST["vari".$k.$counter]!="Keine Angabe" && $_POST["vari".$k.$counter]!=""){
					$charArray[] = $_POST["vari".$k.$counter];
				}
				$counter++;
			}
				
			for($m=0;$m<count($charArray);$m++) {
				$sql2 = 'INSERT INTO ubesitzt_char (UID, AName, Wert) 
						VALUES ('.$ID.', "'.$valuesAName[$k].'", "'.$charArray[$m].'");';
				$result2 = $conn->query($sql2);
				//echo $sql2."<br>charc<br>";
				
				if ($result2 === FALSE) {$fehler=1; echo "Error: " . $sql2 . "<br>" . $conn->error;}
			}
		}
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
	
	$sql = 'UPDATE unterkunft SET KommentarPreis="'.$preis.'" WHERE ID='.$_SESSION["UID"].';';
	$x = $conn->query($sql);
	
	if($x === false) {$fehler=1; echo $conn->error;}
		
	if($fehler==0) {
		header('Location: UebersichtUnterkunft.php');
	} else {echo "<br>Fehler beim EinfÃ¼gen in die Datenbank<br>";}
	
}


$conn->close();
?>


<script type="text/javascript">
var clicker = document.getElementsByClassName("adder");
for(i = 0; i < clicker.length;i++)
{
	clicker[i].addEventListener('click',function(e){
	e = e || window.event;
	var target = e.target || e.srcElement;
	var locid = target.id;
	//Navigate to source select
	var source = target.parentNode.previousSibling.firstChild;
	var i = 0;
	i = target.parentNode.previousSibling.childNodes.length-1;
	var newNode = document.createElement("select");
	newNode.innerHTML = source.innerHTML;
	newNode.name = source.name + i;
	for(var j=0;j < newNode.childNodes.length;j++)
	{
		newNode.childNodes[j].selected = false;
	}
	target.parentNode.previousSibling.appendChild(newNode);
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
	var i = 0;
	i = target.parentNode.previousSibling.childNodes.length-1;
	if(i != 0)
	{
		target.parentNode.previousSibling.removeChild(source);
	}	
},false);
}	

// warne nur, wenn etwas geaendert/geclickt wurde und wenn nicht submittet wird
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
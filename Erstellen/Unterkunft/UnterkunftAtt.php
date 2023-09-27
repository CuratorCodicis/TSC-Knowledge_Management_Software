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
include('../../database.php');
$conn = getConnection();
?>

 <script>
function leave_site() {
	window.location.replace("UHomebutton.php");
}
</script>

<div class="color" id="colorU1">
<button type="button" onclick="leave_site()" class="HomeB">Home</button></br></br>
<h1>Kriterien</h1></br></br>
</div>
<div class="circle2"><i style='font-size:50px;margin-top:35%;color:grey;' class='fas'>&#xf65e;</i></div>

<?php

	$sql="SELECT * FROM attributefuerunterkunft ORDER BY AName";
	$attribute = $conn->query($sql);

	if($attribute === false) {echo 'FEHLER: '.$conn->error;}

	$valuesAName = array();
	$valuesTyp = array();
	$values = array();

	$comment ="";

	while($row = $attribute->fetch_assoc()) {
		$valuesAName[] = $row["AName"];
		$valuesTyp[] = $row["Typ"];
	}
if($_SERVER["REQUEST_METHOD"]!="POST") {
	echo'<div>
	<form action ="UnterkunftAtt.php" method="Post">
	<table id="middle" style="width:80%;">
	<colgroup>
		<col style="width: 33%" />
		<col style="width: 33%" />
		<col style="width: 33%" />
	  </colgroup>';
	for($i=0;$i<count($valuesAName);$i++) {
		
		if($valuesTyp[$i]== "int") {echo  '<tr><th>'.$valuesAName[$i].'</th><td><input type="number" id="number" name="vari'.$i.'"></td><td></td></tr>';}
		elseif($valuesTyp[$i]== "bool") {echo  '<tr><th>'.$valuesAName[$i].'</th><td><input type="checkbox" value=1 name="vari'.$i.'"></td></tr>';}  //oder:{echo  '<tr><th>'.$valuesAName[$i].'</th><td><input type="radio" value=1 name="vari'.$i.'"> Ja <input type="radio" value=0 name="vari'.$i.'"> Nein <input type="radio" value=2 name="vari'.$i.'" checked> keine Angabe</td></tr>';}
		elseif($valuesTyp[$i]== "char") {echo  '<tr><th>'.$valuesAName[$i].'</th><td><input type="text" name="vari'.$i.'"></td></tr>';}
		elseif($valuesTyp[$i]=="charc") {
			
			$sql = "SELECT Wert FROM attributefuerunterkunftauswahlwerte WHERE AName='".$valuesAName[$i]."' ORDER BY Wert";
			$result = $conn->query($sql);
			
			if($result === false) {echo 'FEHLER: '.$conn->error;}
			
			echo '<tr><th style="vertical-align:top;padding-top: 25px;">'.$valuesAName[$i].'</th><td><select name="vari'.$i.'">';
			echo '<option value="Keine Angabe">Keine Angabe</option>';
			
			while($row=$result->fetch_assoc()){
				
				echo '<option value="'.$row["Wert"].'">'.$row["Wert"].'</option>';
			}
			
			echo '</select></td><td style="text-align:left;vertical-align:top;"><button type="button" class="adder" style="margin:2px;">+</button><button type="button" class="miner">-</button></td></tr>';
			
			} else {echo 'FEHLER';}
		
	}
	echo '<tr><th></br>Kommentar</th><td></br><textarea rows="4" cols="50" name="comment"></textarea></td></tr>';

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

	/*
	for($i=0;$i<=2;$i++) {
		$counterJahr=$i + 1;
		
		$jahr = explode("|", $jahre[$i]);
		
		echo '<tr><th style="vertical-align:top;"></br></br>';
		if($i==0) {echo 'Preise';}
		echo '</th><td><table style="margin-top:20px;"><tr><th><input type="text" name="Jahr'.$counterJahr.'" placeholder="Jahreszahl '.$counterJahr.'" ';
		if($jahr[0] != "") {echo 'value="'.$jahr[0].'"';}
		echo '></th><td></br></td></tr>';
		
		$counter = 1;
		for($j=1;$j<=10;$j=$j+2) {
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
	*/

	echo '</table>';
	echo '<input type = submit id="submitB" value="Speichern"> </form>
	</div>';
}
if($_SERVER["REQUEST_METHOD"]=="POST") {
	for($j=0;$j<count($valuesAName);$j++) {
		if (isset($_POST["vari".$j]))
			$values[$j]=$_POST["vari".$j];
		else
			$values[$j]= 0;
	}
	//var_dump($_POST);
	if($_POST["comment"]!=NULL && isset($_POST["comment"])) {$comment = $_POST["comment"];}
	
	$fehler=0;
	
	for($h=0;$h<count($valuesAName);$h++) {
		if($valuesTyp[$h]=="int" && $values[$h]!=NULL) {
			$sqlneu = "INSERT INTO ubesitzt_int(UID, AName, Wert) VALUES("
			.$_SESSION["UID"].",'"
			.$valuesAName[$h]."',"
			.$values[$h].")";
			
			$x = $conn->query($sqlneu);
			
			if($x === false) {$fehler=1; echo $conn->error;}
		}
		
		if($valuesTyp[$h]=="bool" && $values[$h]!=NULL) {
			$sqlneu = "INSERT INTO ubesitzt_bool(UID, AName, Wert) VALUES("
			.$_SESSION["UID"].",'"
			.$valuesAName[$h]."',"
			.$values[$h].")";
			
			$x = $conn->query($sqlneu);
			
			if($x === false) {$fehler=1; echo $conn->error;}
		}
		
		if(($valuesTyp[$h]=="charc"||$valuesTyp[$h]=="char")) {
			
			$charArray = array();
			
			if($values[$h]!="Keine Angabe") {
				$charArray[]=$values[$h];
			}
			
			$counter = 0;
			while(isset($_POST["vari".$h.$counter])) {
				if($_POST["vari".$h.$counter]!="Keine Angabe") {
					if(!in_array($_POST["vari".$h.$counter], $charArray)) {
						$charArray[] = $_POST["vari".$h.$counter];
					}
				}
				
				$counter++;
			}
			
			for($k=0;$k<count($charArray);$k++) {
				$sqlneu = "INSERT INTO ubesitzt_char(UID, AName, Wert) VALUES(".$_SESSION["UID"].",'".$valuesAName[$h]."','".$charArray[$k]."')";
				$x = $conn->query($sqlneu);
			
				if($x === false) {$fehler=1; echo $conn->error;}
			}
			

		}
	}
	
	if($comment!=NULL) {
		$sqlneu = "UPDATE unterkunft SET Kommentar='".$comment."' WHERE ID=".$_SESSION["UID"];
		
		$x = $conn->query($sqlneu);
		
		if($x === false) {$fehler=1; echo $conn->error;}
	}
	
	/* Preise updaten
	for (for each input in #elements){
		$sqlstring = 'UPDATE unterkunft SET KommentarPreis= /"'.$_POST['Jahr1'].'"|"'.$_POST['DZ1'].'"|"'.$_POST['Preis1.1'].'"|"'.$_POST['EZ1'].'"|"'.$_POST['Preis1.2'].'"/"'.$_POST['Jahr2'].'"|"'.$_POST['DZ2'].'"|"'.$_POST['Preis2.1'].'"|"'.$_POST['EZ2'].'"|"'.$_POST['Preis2.2'].'"/"'.$_POST['Jahr3'].'"|"'.$_POST['DZ3'].'"|"'.$_POST['Preis3.1'].'"|"'.$_POST['EZ3'].'"|"'.$_POST['Preis3.2'].'"/" WHERE ID="'.$_SESSION["UID"].'";';
		$x = $conn->query($sqlstring);
		
		if($x === false) {$fehler=1; echo $conn->error;}
	}*/
	
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
		header('Location: UnterkunftEnde.php');
	} else {echo "<br>Fehler beim Einfügen in die Datenbank<br>";}
	
	
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

// falls versucht wird, die Seite zu verlassen, warne vor
window.addEventListener('beforeunload', function(e){sicher(e)});
</script>

</body>
</html> 
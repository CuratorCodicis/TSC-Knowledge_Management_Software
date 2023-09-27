<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Attribut Anlegen</title>
  <meta charset="UTF-8">
  <LINK rel="stylesheet" href="\general.css">
  <LINK rel="stylesheet" href="\colors.css">
  <LINK rel="stylesheet" href="../Anlegenstyle.css">
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
  .hide
  {
	position: absolute;
   top: -9999px;
   left: -9999px;
  }
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
	window.location.replace("../Homebutton.php");
}
</script>

<div class="color" id="colorSuche">
<button type="button" onclick="leave_site()" class="HomeB">Home</button></br></br>
<h1>Neues Attribut anlegen</h1></br></br>
</div>
<div class="circle"><i style='font-size:50px;margin-top:35%;color:grey;' class='fas'>&#xf65e;</i></div>

<?php
include('../../database.php');
$conn = getConnection();

//ist Attribut schon in DB vorhanden
$inDB = false;

//DB Verarbeitung
if($_SERVER["REQUEST_METHOD"]=="POST") {
	$object = $_POST["object"];
	if(is_null($_POST["name"]) || empty($_POST["name"])){
		echo '</br></br></br></br><div style="border:1px solid black;width:50%;margin-left:auto;margin-right:auto;"></br><i class="fas fa-exclamation-circle" style="font-size:20px;color:black;">&nbsp;&nbsp;</i>Sie können einem Attribut keinen leeren Namen geben!
			</br>';
		echo '</br></br></div>';
	}
	else {
	
		if($object == "U") {
			
			$sql = 'SELECT * FROM attributefuerunterkunft WHERE AName="'.$_POST["name"].'";';
			$result = $conn -> query($sql);
			
			while($row=$result->fetch_assoc()){
				$inDB = true;
			}
			
			if($inDB) {
				$_SESSION["att"] = $_POST["object"] . $_POST["name"];
				echo '</br></br></br></br><div style="border:1px solid black;width:50%;margin-left:auto;margin-right:auto;"></br><i class="fas fa-exclamation-circle" style="font-size:20px;color:black;">&nbsp;&nbsp;</i>Das Attribut "'.$_POST["name"].'" existiert bereits für Unterkünfte! <br>
				Erstellen Sie das Attribut mit einem anderen Namen oder bearbeiten Sie das vorhandene Attribut</br>';
				echo '<div style="width:40%;margin-right:auto;margin-left:auto;margin-top:30px;"><a href="../Bearbeiten/Attribut/UebersichtAttribut.php">Attribut "'.$_POST["name"].'" bearbeiten</a></div></br></br></div>';
			} else {
				$sql = 'INSERT INTO attributefuerunterkunft (AName, Typ)
						VALUES ("'.$_POST["name"].'","'.$_POST["typ"].'");';
				$result = $conn -> query($sql);
				
				if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
				
				if ($_POST["typ"] == "charc") {
					$charArray = array();
					if(isset($_POST["aw"]) && $_POST["aw"]!=NULL) {$charArray[] = $_POST["aw"];}
					
					$counter = 0;
					while(isset($_POST["aw".$counter])) {
						if($_POST["aw".$counter] != NULL && !in_array($_POST["aw".$counter], $charArray)) {$charArray[] = $_POST["aw".$counter];}
						$counter++;
					}
					
					for($i=0;$i<count($charArray);$i++) {
						$sql = 'INSERT INTO attributefuerunterkunftauswahlwerte (AName, Wert)
								VALUES ("'.$_POST["name"].'", "'.$charArray[$i].'");';
						$result = $conn -> query($sql);
						
						if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
					}
				}
				$_SESSION["att"] = $_POST["object"] . $_POST["name"];
				header('Location: AttributEnde.php');
			}
		}
		
		if($object == "S") {
			
			$sql = 'SELECT * FROM attributefuersportstaette WHERE AName="'.$_POST["name"].'";';
			$result = $conn -> query($sql);
			
			while($row=$result->fetch_assoc()){
				$inDB = true;
			}
			
			if($inDB) {
				$_SESSION["att"] = $_POST["object"] . $_POST["name"];
				echo '</br></br></br></br><div style="border:1px solid black;width:70%;margin-left:auto;margin-right:auto;"></br><i class="fas fa-exclamation-circle" style="font-size:20px;color:black;">&nbsp;&nbsp;</i>Das Attribut "'.$_POST["name"].'" existiert bereits für Sportstätten! <br>
				Erstellen Sie das Attribut mit einem anderen Namen oder bearbeiten Sie das vorhandene Attribut </br></br>';
				echo '<div style="width:22%;margin-right:auto;margin-left:auto;margin-top:30px;"><a href="../Bearbeiten/Attribut/UebersichtAttribut.php">Attribut "'.$_POST["name"].'" bearbeiten</a></div></br></br></div>';
			} else {
				$sql = 'INSERT INTO attributefuersportstaette (AName, Typ)
						VALUES ("'.$_POST["name"].'","'.$_POST["typ"].'");';
				$result = $conn -> query($sql);
				
				if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
				
				if ($_POST["typ"] == "charc") {
					$charArray = array();
					if(isset($_POST["aw"]) && $_POST["aw"]!=NULL) {$charArray[] = $_POST["aw"];}
					
					$counter = 0;
					while(isset($_POST["aw".$counter])) {
						if($_POST["aw".$counter] != NULL && !in_array($_POST["aw".$counter], $charArray)) {$charArray[] = $_POST["aw".$counter];}
						$counter++;
					}
					
					for($i=0;$i<count($charArray);$i++) {
						$sql = 'INSERT INTO attributefuersportstaetteauswahlwerte (AName, Wert)
								VALUES ("'.$_POST["name"].'", "'.$charArray[$i].'");';
						$result = $conn -> query($sql);
						
						if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
					}
				}
				if($_POST["sport"]!="Keine Zuordnung") {
					$sql = 'INSERT INTO zuordnungsaa (SAName, AName)
							VALUES ("'.$_POST["sport"].'","'.$_POST["name"].'");';
					$result = $conn -> query($sql);
				}
				
				$_SESSION["att"] = $_POST["object"] . $_POST["name"];
				header('Location: AttributEnde.php');
			}
		}
	}
}

//===========================================================================================================
//form
echo '
<div>
<form action ="AttributStart.php" method="Post">
<table style="width:70%;">
<colgroup>
    <col style="width: 30%" />
    <col style="width: 40%" />
    <col style="width: 30%" />
</colgroup> ' ;

echo '<tr><th></th><td><div id="ramen1"></br><input type="radio" name="object" value="U" id="objU" checked><label for="objU">Unterkunft &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
<input type="radio" name="object" value="S" id="objS"><label for="objS">Sportstätte</label></br></br></td></tr></div>';
echo '<tr><th>Name</th><td><input type="text" name="name"></br></td></tr>';
echo '<tr><th style="vertical-align:top;">Eingabetyp</th><td style="text-align:center;"><div id="ramen1"><div id="ramen2"></br>
							<input type="radio" name="typ" value="bool" id="typB" checked><label for="typB"> Wahrheitswerte</label> </br></br>
							<input type="radio" name="typ" value="int" id="typI"><label for="typI"> Zahlwerte</label> </br></br>							
							<input type="radio" name="typ" value="charc" id="typC"><label for="typC"> Auswahlwerte </label></br></br>
	</div></div></br></td></tr>';
	
//Wenn charc, dann Eingabefelder für Auswahlmöglichkeiten

echo '<tr id="Zuordnungen" class="hide" style="opacity:0;transition:0.3s;"><th style="vertical-align:top;">Zuordnung</th><td><div id="ramen1"><div id="ramen2">';

$sql = 'SELECT * FROM sportart ORDER BY Name;';
$result = $conn -> query($sql);

while($row=$result->fetch_assoc()){
	echo '</br><input type="radio" name="sport" value="'.$row["Name"].'" id="'.$row["Name"].'"><label for="'.$row["Name"].'"> '.$row["Name"].'</label></br></br>';
}
echo '</br><input type="radio" name="sport" value="Keine Zuordnung" id="kZ" checked><label for="kZ"> Keine Zuordnung</label> </br></br>';
echo '</div></div></br></td></tr>';

//Hinzufügen der Möglichkeit Auswahlwerte anzugeben
echo '<tr id="Auswahlwerte" class="hide" style="opacity:0;transition:0.3s;"><th>Auswahlwerte</th><td><div id="ramen1"><div id=""><table><tr><td><input name="aw" class="aws" type="text"/></td>
	<td style="width:40%; vertical-align:top;"><button type="button" class="adder" style="margin:2px;">+</button><button type="button" class="miner">-</button></td></tr>
	
 </table></div></div></td></tr>';


echo '</table>';
echo '<table class="buttons" style="width:90%">
<colgroup>
    <col style="width: 25%" />
    <col style="width: 25%" />
    <col style="width: 25%" />
	<col style="width: 25%" />
</colgroup><tr><td></td><td></td><td></td><td><input type = submit value="Eingabe speichern" id="submitB" style="margin-right:0;float:none;width:70%;padding:9px;font-family: Verdana, Helvetica, Arial, sans-serif;"></td></tr>
</table>
</form>';

$conn->close();
?>
<script type="text/javascript">
var objects = document.getElementsByName("object");
for(var i = 0;i < objects.length;i++)
{
	objects[i].addEventListener("click",function()
	{
		var zuord = document.getElementById("Zuordnungen");
		if (this.value == "U")
		{
			//zuord.style.display = "none";
			zuord.style.opacity = "0";	
			setTimeout(function(){document.getElementById("Zuordnungen").classList.add("hide")},300);
			
		}
		else
		{
			//zuord.style.display = "";
			zuord.style.opacity = "1";
			zuord.classList.remove("hide");						
		}
	});
}

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
	j = target.parentNode.parentNode.firstChild.childNodes.length-1;
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
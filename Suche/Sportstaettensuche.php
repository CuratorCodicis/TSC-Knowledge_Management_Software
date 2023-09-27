<!DOCTYPE HTML>
<HTML>
<HEAD>
<TITLE>Suche-Sportstaette</TITLE>
<meta charset="UTF-8">
<LINK rel="stylesheet" href="\general.css">
<LINK rel="stylesheet" href="\colors.css">
<LINK rel="stylesheet" href="styles/Kriterienstyle.css">
<LINK rel="stylesheet" href="\Icons/css/all.css">
<style>
.circle{
	margin-top:0;
	right: 5%;
	top: 5%;
	position: absolute;
	z-index: +1;
	width: 200px;
	height: 200px;
	border-radius: 50%;
	background-color: white;
}

.info{
	margin-left:auto;
	margin-right:auto;
	border:1px solid black;
	border-collapse: collapse;
}

td {
    text-align: left; 	
}
th {
    text-align: right;
}
</style>
</HEAD>
<BODY>

<script>
function leave_site(){
	sessionStorage.clear();
	window.location.replace("../Index.php")
}
</script>
<div class="color" id="colorS2">
<button class="HomeB" onclick="leave_site()">Home</button></br></br>
<h1>Auswahl der Suchkriterien</h1></br></br>
</div>
<div class="circle"><i style='font-size:50px;margin-top:35%;color:grey;' class='fas'>&#xf5a0;</i></div>

<?php
error_reporting(E_ERROR | E_PARSE);
$startort = null;
$maxDistanz = null;
$content = null;
try {
	$startort = $_POST['startort'];
	$maxDistanz = $_POST['maxDist'];
	$SAs = $_POST['SAs'];
	$coords = $_POST['coordinates'];
} catch(Exception $ex)
{
		
}

if ($startort == null || $maxDistanz == null)	
{
	header('Location:../Suche/Suche.php');
	echo"</br><h2 style='color:#FF0000'>Achtung! Nicht ausreichend Daten erhalten!</h2>";
}

//Überprüfe, ob wir über ein zurück hierher gekommen sind
try 
{
	$content = $_POST['queryString'];
} catch(Exception $ex)
{
	
}
if ($content != null)
{
	//TODO:Behandlung eines Rückfalls
	
}
?>

</br>

<div><!-- ausgewählte Attribute -->
<p><!--Ausgewählte Kriterien--></p>

<!--<input type="textbox" readonly value="" id="ausKritAnz"></input>-->
<div id="ausKritAnz"> 
<?php
if ($startort != null && $maxDistanz != null)
	echo "<div class='tagEl' id='sOrtTag'>Startort: ".$startort."</div> <div class='tagEl' id='maxDistTag'>Umkreis: ".$maxDistanz." km</div>"
?>
</div>
</div>

</br>

<div><!-- Auswahl der Attribute -->
<table class="info" style="width:70%">
<colgroup>
    <col style="width: 10%" />
    <col style="width: 90%" />
  </colgroup>
<tr><th style="text-align:center;"><i class='fas fa-exclamation-circle' style='font-size:25px;color:black;'></i></th>
<td><p><?php if($_POST["maxDistZwischen"] != ""){ echo "Wählen Sie die Attribute für die Sportstätte aus";}
	else echo "Wählen Sie die Attribute aus";?></p></td></tr></table>

<?php
include('../database.php');

$conn = getConnection();

//Liefert die zuvor ausgewählten Sportarten. Die erste Position ist zu ignorieren, da sie nur den String "Sportarten" enthält
$sportarten = explode(';',$SAs); 
$atNumber = 0; //Zahl, welche jedem Attribut eine aufzählende ID zuweist

//Stelle zunächst alle möglichen Suchattribute für die angegebenen Sportarten dar
for($i =1; $i<count($sportarten);$i++)
{
	$attrQuery ="SELECT DISTINCT * FROM ZuordnungSAA Natural Join AttributeFuerSportstaette 
	WHERE SAName LIKE '".$sportarten[$i]."' ORDER BY AName";
	$result = $conn->query($attrQuery);
	//Erstelle das neue Element
	echo "<div class='opener'>".$sportarten[$i]."</div><div class='SCcontent'>";
	if ($result != null && $result->num_rows > 0)
	{
		$firstLetter = "";
		while($row = $result->fetch_assoc())
		{
			echo DisplayAttribut($row,$atNumber,$conn,$firstLetter);
			$atNumber++;			
		}
	}
	else
	{
		echo "Entweder ".$sportarten[$i]." in Datenbank nicht gefunden</br> oder es sind keine Attribute mit dieser Sportart in Verbindung gebracht.";
	}
	echo "</div>";
}

//Auflistung aller Attribute, welche nicht mit einer Sportart assoziiert sind
$attrQuery = "SELECT * FROM AttributeFuerSportstaette 
WHERE AName NOT IN (SELECT DISTINCT AName FROM ZuordnungSAA)
ORDER BY AName;";
$result = $conn->query($attrQuery);
if ($result != null && $result->num_rows > 0)
{
	//Erstelle das neue Element
	$firstLetter = "";
	echo "<div class='opener'>Allgemein</div><div class='SCcontent'>";
	if ($result != null && $result->num_rows > 0)
	{
		while($row = $result->fetch_assoc())
		{		
			echo DisplayAttribut($row,$atNumber,$conn,$firstLetter);
			$atNumber++;		
		}
	}
	else
	{
		echo "<p>Keine allgemeinen Attribute vorhanden.<br> Alle Attribute sind einer Sportart zugeordnet. </p>";
	}
	echo "</div>";
}
else
{
	echo "<div class='opener'>Allgemein</div><div class='SCcontent'>";
	echo "Keine allgemeinen Attribute vorhanden.</br> Jedes erfasste Attribut ist mindestens einer Sportart zugeordnet.</div>";
}

//Auflistung aller verfügbaren Attribute abzüglich der in den Sportarten bereits dargestellten
//Größer als 1, da das erste Element in dem Array "Sportarten" ist
if (count($sportarten) > 1)
{
	//Zusammenbauen der SQL Query - Auschluss der Attribute, welche bereits dargestellt wurden
	$SArtenPart = 'WHERE (SAName="'.$sportarten[1].'")';
	for($j =2;$j < count($sportarten);$j++)
	{
		$SArtenPart .= 'OR (SAName="'.$sportarten[$j].'")';
	}

	$attrQuery = "SELECT * FROM AttributeFuerSportstaette WHERE AName NOT IN (SELECT AName FROM
	ZuordnungSAA ".$SArtenPart." ) ORDER BY AName";
}
else 
	$attrQuery = "SELECT * FROM AttributeFuerSportstaette ORDER BY AName";

$result = $conn->query($attrQuery);

if ($result != null && $result->num_rows > 0)
{
	//Erstelle das neue Element
	$firstLetter = "";
	echo "<div class='opener'>Alle Attribute</div><div class='SCcontent'>";
	while($row = $result->fetch_assoc())
	{		
		echo DisplayAttribut($row,$atNumber,$conn,$firstLetter);
		$atNumber++;		
	}
	echo "</div>";
}


$conn->close();

//Wandelt eine gegebene Zeile einer Mysql Anfrage in ein Element um
//Übergabe von firstL firstLetter als Referenzparameter, sodass er geändert werden kann
function DisplayAttribut($row,$atNumber,$conn,&$firstL)
{
	$output = "";
	/*Auslesen der möglichen Attribute
	*Jedes Attribut hat ein umschließendes Elternelement - Dieses nutzen wir zum verschwinden lassen des Attributs beim anklicken
	*Jedes Attribut hat zur Identfikation zusätzlich den Attributnamen als Klasse
	*
	*/
	if(strtoupper(substr($row['AName'], 0, 1)) != $firstL)
		{
			$firstL = strtoupper(substr($row['AName'], 0, 1));
			$output .= '<p class="subgroup">'.$firstL.'</p>';
		}
		switch($row['Typ'])
		{
			case "bool":
				$output .='<p class="attributes (p'.$row['AName'].')">'.$row['AName'].' <button type="button" class="attributes_clicker ('.$row['AName'].')" name="b'.$atNumber.'">&#10004</button></p>';
			break;
			case "int":
				$output .='<p class="attributes (p'.$row['AName'].')">'.$row['AName'].' <button id="b'.$atNumber.'" name="0" class="operator_changer">=</button>'.
				'<input type="number" min=0 id="v'.$atNumber.'" name="'.$row['AName'].'" value=0>'
				.'<button type="button" class="attributes_clicker ('.$row['AName'].')" name="i'.$atNumber.'">&#10004</button></p>';
			break;
			case "charc":
				// Die möglichen Werte für ein Attribut direkt aus der Datenbank auslesen
				$stringQuery = 'SELECT DISTINCT Wert FROM SSbesitzt_char WHERE AName="'.$row['AName'].'"';
				$resultAtributes = $conn->query($stringQuery);
				if ($resultAtributes != null && $resultAtributes->num_rows > 0)
				{
					$output .= '<p class="attributes (p'.$row['AName'].')">'.$row['AName'].' <select id="s'.$atNumber.'" class="(s'.$row['AName'].')">';
					while ($row2 = $resultAtributes->fetch_assoc())
					{
						$output .= '<option class ="('.$row['AName']."_".$row2['Wert'].')" value="'.$row2['Wert'].'">'.$row2['Wert'].'</option>';
					}
					$output .= '</select><button type="button" class="attributes_clicker ('.$row['AName'].')" name="c'.$atNumber.'">&#10004</button></p>';
				}
				else
				{
					//Das Attribut exisitiert zwar, aber es sind keine Werte für dieses vorhanden
					$output .= '<p class="attributes"> Für '.$row['AName'].' sind keine Werte hinterlegt.</p>';
				}
				// todo type def
			break;
			default:
			$output .="Attribut erkannt, aber Typ missmatch:".$row['AName']." Typ ".$row['Typ']."</br>";
		}
	
	return $output;
}
$conn->close();
?>
</div>

<div id="submitDiv"><!-- Weiter geht's -- Abschießen -->
<form name="searchDelegate" action="Suchergebnisse.php" method="post" id="searchDelegate">
<input type="hidden" name="queryString" id="queryString">
<input type="hidden" name="Typ" id="Typ" value="Sportstaette">
<input type="hidden" name="SAs" value="<?php echo $SAs ?>">
<input type="hidden" name="startort" value="<?php echo $startort;?>">
<input type="hidden" name="maxDist" value="<?php echo $maxDistanz;?>">
<input type="hidden" name="coordinates" value="<?php echo $coords;?>">
<input type="hidden" name="maxDistZwischen" value="<?php echo $_POST["maxDistZwischen"];?>">
<?php
if (isset($_POST["finalIDs"]) && $_POST["finalIDs"] != "")
{
	echo '<input type="hidden" name="finalIDs" value='.$_POST["finalIDs"].' />';
	echo '<input type="hidden" name="matrix" value='.$_POST["matrix"].' />';
	echo '<input type="hidden" name="joker" value='.$_POST["joker"].' />';
	echo '<input type="hidden" name="head" value='.$_POST["head"].' />';
	echo "<input type='hidden' name='attrListe' value='".$_POST["attrListe"]."' />";
	
}
?>
</form>
<button id="submitSearch" class="continueB">Suchen</button>
</div>
<?php
//Überprüfung, ob wir durch ein BackDelegate hier her gekommen sind
if ((!isset($_POST["backer"]) || $_POST["backer"]!="true"))	
	echo "<script> sessionStorage.removeItem('content'); </script>";

?>
<SCRIPT type="text/javascript">
function toggleUpDown_(ele){
	ele.classList.toggle("active");
    var panel = ele.nextElementSibling;
	// Ueberprüfen, ob das nächste Element die gewünschte Div Box ist.
	while (panel.className != "SCcontent" && panel != null)
	{
		panel = panel.nextElementSibling;
	}
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

var clickers = document.getElementsByClassName("opener");

for (i = 0; i < clickers.length; i++) {
  clickers[i].addEventListener("click",function(){ toggleUpDown(this)});
}
</SCRIPT>
<!-- Skripteinbindung hier erst, damit Event-Listener richtig gesetzt werden -->
<SCRIPT src="functions/SucheFunctions.js"></SCRIPT>
</BODY>
</HTML>
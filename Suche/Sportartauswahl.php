<!DOCTYPE HTML>
<HTML>
<HEAD>
<TITLE>Suche-Sportartenauswahl</TITLE>
<meta charset="UTF-8">
<LINK rel="stylesheet" href="\general.css">
<LINK rel="stylesheet" href="\colors.css">
<LINK rel="stylesheet" href="styles/SAAstyle.css">
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
</style>
</HEAD>
<BODY>

<script>
function leave_site(){
	window.location.replace("../Index.php")
}
</script>
<div class="color" id="colorS1">
<button class="HomeB" onclick="leave_site()">Home</button></br></br>	
<h1>Auswahl der Sportarten</h1></br></br>
</div>
<div class="circle"><i style='font-size:50px;margin-top:35%;color:grey;' class='fas'>&#xf5a0;</i></div>

<?php
$startort = null;
$maxDistanz = null;
try {
	$startort = $_POST['startort'];
	$maxDistanz = $_POST['maxDist'];
} catch(Exception $ex)
{
		
}

if ($startort == null || $maxDistanz == null)	
	echo"</br><h2 style='color:#FF0000'>Achtung! Nicht ausreichend Daten erhalten!</h2>";
//else
	//echo "<h2>Im Umkreis von ".$maxDistanz."km um ".$startort."</h2>";
?>

</br>

<div><!-- ausgewählte Attribute -->
<p></p>

<div><!-- Auswahl der Sportart -->
<p>Für welche Sportart(en) soll die Sportstätte geeignet sein?</p>
<?php
include('../database.php');

$conn = getConnection();

$SAQuery ="SELECT DISTINCT * FROM Sportart ORDER BY Name";
$result = $conn->query($SAQuery);

if ($result != null && $result->num_rows > 0)
{
	$atNumber = 0; //Zahl, welche jedem Attribut eine aufzählende ID zuweist
	while($row = $result->fetch_assoc())
	{
		$SArt = $row['Name'];
		echo '<p class="SAcheck" id="p'.$atNumber.'"><label><input type="checkbox" id="'.$atNumber.'" class="SAcheck_clicker" name="'.$row['Name'].'">'.$row['Name'].'</label></p>';
		$atNumber++;
	}
}
else
{
	echo "Keine Attribute vorhanden";
}

$conn->close();

?>
</div>

<script type="text/javascript">
function forwardSearch()
{
	var startortJS = "<?php echo $startort; ?>";
	var maxDistJS = "<?php echo $maxDistanz; ?>";
	var inputArea = document.getElementById("startortTarg");
	if (inputArea != null)
	{
		inputArea.value = startortJS;
	}	
	var inputArea = document.getElementById("maxDistTarg");
	if (inputArea != null)
	{
		inputArea.value = maxDistJS;
	}	
	var inputArea = document.getElementById("SAs");
	if (inputArea != null)
	{
		//Füge die ausgewählten Sportarten dem Formular zur Weitergabe hinzu
		var counter = 0;
		var SA = "Sportarten";
		//Iteriere über alle möglichen checkboxen, welche über eine Zahl identifiziert werden
		//Stoppe sobald kein Element mehr gefunden wird, also die Funktion getElementById einen leeren Wert zurück gibt7
		var cb = document.getElementById(counter);
		while(cb != null)
		{
			//Überprüfe, ob die checkbox geklickt wurde
			if(cb.checked == true)
			{
				//Füge alle ausgewählten Sportarten den String hinzu durch ein Semikolon getrennt
				SA = SA+";"+cb.name;
			}
			counter=counter+1;
			cb = document.getElementById(counter);
		}
		inputArea.value = SA; 
		
	}	
	document.fSearch.submit();
}
</script>

<div id="submitDiv"><!-- Weiter geht's -- Abschießen -->
<form name="fSearch" action="Sportstaettensuche.php" method="POST" id="fSearch">
<input type="hidden" name="maxDist" id="maxDistTarg">
<input type="hidden" name="startort" id="startortTarg">
<input type="hidden" name="SAs" id="SAs">
<input type="hidden" name="coordinates" id="coordinates" value="<?php echo $_POST['coordinates']?>">
<?php
if (isset($_POST["finalIDs"]) && $_POST["finalIDs"] != "")
{ 
	echo '<input type="hidden" name="finalIDs" value='.$_POST["finalIDs"].' />';
	echo '<input type="hidden" name="head" value='.$_POST["head"].' />';
	echo '<input type="hidden" name="matrix" value='.$_POST["matrix"].' />';
	echo '<input type="hidden" name="joker" value='.$_POST["joker"].' />';
	echo '<input type="hidden" name="maxDistZwischen" value='.$_POST["maxDistZwischen"].' />';
	echo "<input type='hidden' name='attrListe' value='".$_POST["attrListe"]."' />";
	echo '<script>sessionStorage.clear(); </script>';
}
?>

</form>
<button id="submitSearch" class="continueB" onclick="forwardSearch()">Übernehmen</button>
</div>
</BODY>
</HTML>
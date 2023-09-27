<?php
session_start();
$typ = $_GET["typ"];
?>

<html>
<head> 
  <title>Auswahl Bearbeiten</title>
  <meta charset="UTF-8">
  <LINK rel="stylesheet" href="\general.css">
  <LINK rel="stylesheet" href="\colors.css">
  
 <LINK rel="stylesheet" href="\Icons/css/all.css">
  
 <style>
body{
	text-align:center;
}
  table, th, td {
	margin-left: auto;
	margin-right: auto;
	margin-top:10%;
	margin-bottom:10%;
    border: none;
    border-collapse: collapse;
}
td {
    text-align: left; 	
}
th {
    text-align: right;
	padding-right: 15px;
}
tr{
	padding: 0 10px;
}

  /* Style the tab */
.tab {
  float: left;
  margin-left: 10%;
  border: 1px solid #ccc;
  background-color: #e5e5e5;
  width: 45%;
  height: 65vh;
  overflow-y:scroll;
  
}

/* Style the buttons that are used to open the tab content */
.tab button {
  font: verdana;
  font-size: 100%;
  display: block;
  background-color: inherit;
  color: black;
  padding: 22px 16px;
  width: 100%;
  border: none;
  outline: none;
  text-align: left;
  cursor: pointer;
  transition: 0.3s;
}


/* Change background color of buttons on hover */
/*.tab button:hover {
  background-color: #848484;
}*/	

/* Create an active/current "tab button" class */
.tab button.active {
  background-color: #3c3c3c;
  color: #F8F8FF;
}

.tab button.select {
  background-color: #808080;
}

/* Style the tab content */
.tabcontent {
  word-wrap: normal;
  word-break: break-all;
  white-space: normal;
  display: none;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  position: -webkit-sticky;
  position: sticky;
  position: -moz-sticky;
  top: 0;
  float: left;
  padding: 0px 12px;
  border: 1px solid #ccc;
  width: 30%;
  min-width: 300px;
  border-left: none;
  height: auto;
  z-index:+1;
  </style>
</head>
<body> 

<script>
function leave_site() {
	window.location.replace("../Homebutton.php");
}
</script>

<div class="color" id="<?php if($typ=="U") echo "colorU1"; else if($typ=="S") echo "colorS1";else if($typ=="A") echo "colorSuche"; else echo "colorK1";?>">
<button type="button" onclick="leave_site()" class="HomeB">Home</button></br></br>
<h1><b>Auswahl zum Bearbeiten<b></h1></br></br>
</div>
<div class="circle"><i style='font-size:50px;margin-top:35%;color:grey;' class='far'>&#xf044;</i></div>

<?php
include('../../database.php');
$conn = getConnection();

//Clearen des session Storage um inkonsistenzen zu vermeiden
echo "<script>sessionStorage.clear(); sessionStorage.setItem('KlickID','');</script>";

$setK = $setS = $setU = $setA= true;
if (isset($_GET['typ']))
{
	switch($_GET['typ'])
	{
		case "U":
		$setK = $setS = $setA = false;
		break;
		case "S":
		$setK = $setU = $setA = false;
		break;
		case "K":
		$setS = $setU = $setA = false;
		break;
		case "A":
		$setK = $setS = $setU = false;
		break;
		
	}
}

//Vorbereiten der Elternseite zur Auswahl
if ($setU)
{
	$sqlU = "SELECT * FROM unterkunft ORDER BY Name";
	$resultU = $conn -> query($sqlU);
}
if ($setS)
{
	$sqlS = "SELECT * FROM sportstaette ORDER BY Name";
	$resultS = $conn -> query($sqlS);
}
if ($setK)
{
	$sqlK = "SELECT * FROM Kontaktpersonen ORDER BY Nachname";
	$resultK = $conn -> query($sqlK);
}
if ($setA)
{
	$sqlAU = "SELECT * FROM AttributeFuerUnterkunft ORDER BY AName";
	$resultAU = $conn -> query($sqlAU);
	$sqlAS = "SELECT * FROM AttributeFuerSportstaette ORDER BY AName";
	$resultAS = $conn -> query($sqlAS);
}

echo '<form name="delegate" action="" method="POST">
	<input type="hidden" name="ID" id="ID"></input>
	</br><button type="button" class="continueB" id="submitIt" value="Zum Bearbeiten" style="margin-left:40%;">Zum Bearbeiten</button>
	</form>';


echo '<div class="tab">';
if ($setU)
{
	echo '<br> UNTERKUNFT <br>';
	while($row = $resultU->fetch_assoc()) {
		echo '<button class="tablinks" onclick="clickObject(event, '."'U".$row["ID"]."'".')" onmouseover="openObject(event, '."'U".$row["ID"]."'".')">'.$row["Name"].'</button>';
	}
}
if ($setS)
{
	echo '<br> SPORTST&AumlTTE<br>';
	while($row = $resultS->fetch_assoc()) {
		echo '<button class="tablinks" onclick="clickObject(event, '."'S".$row["ID"]."'".')" onmouseover="openObject(event, '."'S".$row["ID"]."'".')">'.$row["Name"].'</button>';
	}
}
if ($setK)
{
	echo '<br> KONTAKTPERSON<br>';
	while($row = $resultK->fetch_assoc()) {
		echo '<button class="tablinks" onclick="clickObject(event, '."'K".$row["ID"]."'".')" onmouseover="openObject(event, '."'K".$row["ID"]."'".')">'.$row["Nachname"];
		if(!is_null($row["Vorname"])) echo ', '.$row['Vorname'].'</button>';
		else echo '</button>';
	}
}
if ($setA)
{
	echo '<br> Attribute für Unterkünfte<br>';
	while($row = $resultAU->fetch_assoc()) {
		echo '<button class="tablinks" onclick="clickObject(event, '."'AU".$row["AName"]."'".')" onmouseover="openObject(event, '."'AU".$row["AName"]."'".')">'.$row["AName"];
		echo '</button>';
	}
	echo '<br> Attribute für Sportstätten<br>';
	while($row = $resultAS->fetch_assoc()) {
		echo '<button class="tablinks" onclick="clickObject(event, '."'AS".$row["AName"]."'".')" onmouseover="openObject(event, '."'AS".$row["AName"]."'".')">'.$row["AName"];
		echo '</button>';
	}
}
echo '</div>';
if ($setU)
{
	$resultU = $conn -> query($sqlU);
	while($row = $resultU->fetch_assoc()) {
		echo '<div id="U'.$row["ID"].'" class="tabcontent">
		<table style="width:100%">
		<colgroup>
			<col style="width: 40%" />
			<col style="width: 60%" />
		</colgroup>';
		
		echo '<tr><th>Name</th><td>'.$row["Name"].'</td></tr>';
		if(!is_null($row["Telefonnummer"])) {echo '<tr><th>Telefonnummer</th><td> '.$row["Telefonnummer"].'</td></tr>';}
		if(!is_null($row["MailAdresse"])) {echo '<tr><th>E-Mail-Adresse</th><td> '.$row["MailAdresse"].'</td></tr>';}
		if(!is_null($row["Internetseite"])) {echo '<tr><th>Internetseite</th><td> '.$row["Internetseite"].'</td></tr>';}
		echo '<tr><th>Straße</th><td> '.$row["Strasse"].'</td></tr>';
		if(!is_null($row["Hausnummer"])) {echo '<tr><th>Hausnummer</th><td> '.$row["Hausnummer"].'</td></tr>';}
		if(!is_null($row["Postleitzahl"])) {echo '<tr><th>Postleitzahl</th><td> '.$row["Postleitzahl"].'</td></tr>';}
		echo '<tr><th>Ort</th><td> '.$row["Ort"].'</td></tr>';
		echo '<tr><th>Land</th><td> '.$row["Land"].'</td></tr>';
		if(!is_null($row["Kommentar"])) {echo '<tr><th>Kommentar</th><td> '.$row["Kommentar"].'</td></tr>';}
		
		echo '</table></div>';
	}
}
if ($setS)
{
	$resultS = $conn -> query($sqlS);
	while($row = $resultS->fetch_assoc()) {
		echo '<div id="S'.$row["ID"].'" class="tabcontent">
		<table style="width:100%">
		<colgroup>
			<col style="width: 40%" />
			<col style="width: 60%" />
		</colgroup>';
		
		echo '<tr><th>Name</th><td>'.$row["Name"].'</td></tr>';
		if(!is_null($row["Telefonnummer"])) {echo '<tr><th>Telefonnummer</th><td> '.$row["Telefonnummer"].'</td></tr>';}
		if(!is_null($row["MailAdresse"])) {echo '<tr><th>E-Mail-Adresse</th><td> '.$row["MailAdresse"].'</td></tr>';}
		if(!is_null($row["Internetseite"])) {echo '<tr><th>Internetseite</th><td> '.$row["Internetseite"].'</td></tr>';}
		echo '<tr><th>Straße</th><td> '.$row["Strasse"].'</td></tr>';
		if(!is_null($row["Hausnummer"])) {echo '<tr><th>Hausnummer</th><td> '.$row["Hausnummer"].'</td></tr>';}
		if(!is_null($row["Postleitzahl"])) {echo '<tr><th>Postleitzahl</th><td> '.$row["Postleitzahl"].'</td></tr>';}
		echo '<tr><th>Ort</th><td> '.$row["Ort"].'</td></tr>';
		echo '<tr><th>Land</th><td> '.$row["Land"].'</td></tr>';
		if(!is_null($row["Kommentar"])) {echo '<tr><th>Kommentar</th><td> '.$row["Kommentar"].'</td></tr>';}
		
		echo '</table></div>';
	}
}
if ($setK)
{
	$resultK = $conn -> query($sqlK);
	while($row = $resultK->fetch_assoc()) {
		echo '<div id="K'.$row["ID"].'" class="tabcontent">
		<table style="width:100%">
		<colgroup>
			<col style="width: 40%" />
			<col style="width: 60%" />
		</colgroup>';
		
		echo '<tr><th>Name</th><td>'.$row["Nachname"];
		if(!is_null($row["Vorname"])) echo', '.$row['Vorname'].'</td></tr>';
		else echo '</td></tr>';
		if(!is_null($row["Funktion"])) {echo '<tr><th>Funktion</th><td> '.$row["Funktion"].'</td></tr>';}
		if(!is_null($row["Telefonnummer"])) {echo '<tr><th>Telefonnummer</th><td> '.$row["Telefonnummer"].'</td></tr>';}
		if(!is_null($row["Mobilnummer"])) {echo '<tr><th>Mobilnummer</th><td> '.$row["Mobilnummer"].'</td></tr>';}
		if(!is_null($row["MailAdresse"])) {echo '<tr><th>E-Mail-Adresse</th><td> '.$row["MailAdresse"].'</td></tr>';}
		if(!is_null($row["Fax"])) {echo '<tr><th>Fax</th><td> '.$row["Fax"].'</td></tr>';}
		if(!is_null($row["Kommentar"])) {echo '<tr><th>Kommentar</th><td> '.$row["Kommentar"].'</td></tr>';}
		
		echo '</table></div>';
	}
}
if ($setA)
{
	$markers = ["AS","AU"];
	for ($i = 0; $i < 2; $i++)
	{
		if ($i == 0)
			$resultA = $conn -> query("SELECT * FROM AttributeFuerSportstaette");
		else
			$resultA = $conn -> query("SELECT * FROM AttributeFuerUnterkunft");
		while($row = $resultA->fetch_assoc()) {
			echo '<div id="'.$markers[$i].$row["AName"].'" class="tabcontent">
			<table style="width:100%">
			<colgroup>
				<col style="width: 40%" />
				<col style="width: 60%" />
			</colgroup>';
			
			echo '<tr><th>Name</th><td>'.$row["AName"];
			echo '</td></tr>';
			$isCharC = false;
			if(!is_null($row["Typ"])) 
			{
				$ausgabe = "Wahrheitswert";
				switch($row["Typ"])
				{
					case "int":
					$ausgabe = "Zahlwert";
					break;
					case "charc":
					$ausgabe = "Auswahlwert";
					$isCharC = true;
					break;
				}
				echo '<tr><th>Typ</th><td> '.$ausgabe.'</td></tr>';
			}
			if ($isCharC)
			{				
				if ($i==0)
					$resultInner = $conn -> query("SELECT * FROM AttributeFuerSportstaetteAuswahlwerte WHERE AName = '".$row["AName"]."'");
				else
					$resultInner = $conn -> query("SELECT * FROM AttributeFuerUnterkunftAuswahlwerte WHERE AName = '".$row["AName"]."'");
				$start = true;
				while($row = $resultInner->fetch_assoc()) {
					if ($start)
					{
						echo '<tr><th>Mögliche Werte</th><td>'.$row["Wert"].'</td></tr>';
						$start = false;
					}
					else
						echo '<tr><th></th><td> '.$row["Wert"].'</td></tr>';
				}
			}			
			echo '</table></div>';
		}
	}
	
}
$conn->close();
?>

<script>
function openObject(evt, objectName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) { 
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(objectName).style.display = "block";
  if(evt.currentTarget.className.includes(" select")) {}
  else {evt.currentTarget.className += " active";}
  
}
</script>

<script>
function clickObject(evt, objectName) {
	if (evt.currentTarget.className.includes(" select")) 
	{
		//Element war bereits erwählt
		evt.currentTarget.className = evt.currentTarget.className.replace(" select", "");
		//Entfernen der Klasse aus den Ausgewählten
		sessionStorage.setItem("KlickID","");
	}
	else {
		//Entfernen von allen anderen Klassen
		var classes = document.getElementsByClassName("select");
		for (var i = 0; i < classes.length; i++)
		{
			classes[i].className = classes[i].className.replace(" select", "");
		}
		evt.currentTarget.className += " select";
		
		//Füge ID dem session Storage für erwählte IDs hinzu
		sessionStorage.setItem("KlickID",objectName);
	}
	
}

var submitter = document.getElementById("submitIt");
submitter.addEventListener("click",function()
{
	var element = sessionStorage.getItem("KlickID");
	var target = document.getElementById("ID");
	if (target != null && element != null)
	{
		if (element == "")
		{
			//Kein Element wurde geklickt.
			alert("Bitte ein Element erwählen.");
			
		}
		else
		{
			var start = element.substr(0,1);
			var ID = element.substr(1,element.length-1);
			target.value = ID;
			switch(start)
			{
				case "U":
				document.delegate.action = "Unterkunft/UebersichtUnterkunft.php";
				break;
				case "S":
				document.delegate.action ="Sportstaette/UebersichtSportstaette.php";
				break;
				case "K":
				document.delegate.action ="Kontaktperson/UebersichtKontaktperson.php";
				break;
				case "A":
				document.delegate.action ="Attribut/UebersichtAttribut.php";				
				break;
			}
			document.delegate.submit();
		}
	}
	else 
	{
		//TODO:Fehlerbehandlung
	}
});
</script>

</body>
</html> 
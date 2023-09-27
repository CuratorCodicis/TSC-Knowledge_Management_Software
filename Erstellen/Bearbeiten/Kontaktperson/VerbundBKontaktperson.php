<?php
session_start();
?>

<html>
<head> 
  <title>Kontakt</title>
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
  height:500px;
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
.tab button:hover {
  background-color: #848484;
}

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
  border-left: none;
  height: auto;
  z-index:+1;
}
#head{
	color:black;
	font-size:20px;
	text-shadow:none;
}
  </style>
</head>
<body> 

<script>
function leave_site() {
	window.location.replace("../../Homebutton.php");
}
</script>

<div class="color" id="colorK2">
<button type="button" onclick="leave_site()" class="HomeB">Home</button></br></br>
<h1><b>Kontakt<b></h1></br></br>
</div>
<div class="circle"><i style='font-size:50px;margin-top:35%;color:grey;' class='far'>&#xf044;</i></div>

<?php
include('../../../database.php');
$conn = getConnection();

//Eintragen der entstandenen Verbindungen


		
if($_SESSION["KID"]==NULL) {
	header('Location: \Index.php');
}
else
{
	$KPID = $_SESSION["KID"];
}
$unterkunftDabei = false;
$sportstaetteDabei = false;
if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST["IDs"])){
	$decode = json_decode($_POST["IDs"]);
	
	//delete DB Verbund
	$sql = 'DELETE FROM kontakte_unterkunft WHERE KPID='.$KPID.';';
	$result = $conn -> query($sql);
	
	$sql = 'DELETE FROM kontakte_sportstaette WHERE KPID='.$KPID.';';
	$result = $conn -> query($sql);
	
	for($i = 0; $i < count($decode);$i++)
	{
		$typ = substr($decode[$i],0,1);
		$ID = substr($decode[$i],1,strlen($decode[$i])-1);
		$sql = "";
		if ($typ == "U")
		{
			$sql = "INSERT INTO kontakte_unterkunft(KPID,UID) VALUES(".$KPID.",".$ID.");";
			$unterkunftDabei = true;
		}
		if ($typ == "S")
		{
			$sql = "INSERT INTO kontakte_sportstaette(KPID,SSID) VALUES(".$KPID.",".$ID.");";
			$sportstaetteDabei = true;
		}
		try
		{
			$er = $conn -> query($sql);
			if ($er === FALSE)
			{
				echo $conn -> error;
			} else {header("Location:UebersichtKontaktperson.php");}
		}
		catch(Exception $e)
		{
			
		}
	}
}
else
{
	$xID = $_SESSION["KID"];
	$UidArray = array();
	$SidArray = array();
	
	$sql = 'SELECT UID FROM kontakte_unterkunft WHERE KPID='.$xID.';';
	$result = $conn -> query($sql);
	
	while($row = $result->fetch_assoc()) {
		$UidArray[] = $row["UID"];
	}
	
	$sql = 'SELECT SSID FROM kontakte_sportstaette WHERE KPID='.$xID.';';
	$result = $conn -> query($sql);
	
	while($row = $result->fetch_assoc()) {
		$SidArray[] = $row["SSID"];
	}
	
	
	//Clearen des session Storage um inkonsistenzen zu vermeiden
	echo "<script>sessionStorage.clear();</script>";

	//Vorbereiten der Elternseite zur Auswahl
	$sqlU = "SELECT * FROM unterkunft ORDER BY Name";
	$resultU = $conn -> query($sqlU);

	$sqlS = "SELECT * FROM sportstaette ORDER BY Name";
	$resultS = $conn -> query($sqlS);
	
	
	echo '<form action="VerbundBKontaktperson.php" name="delegate" method="POST">
		<input type="hidden" name="IDs" id="IDs"/>
		</br><button type="button" class="continueB" id="submitIt" style="margin-left:40%;">Zuweisen & Speichern</button>
		</form>';
	

	echo '<div class="tab">';
	echo '<br><h1 id="head">UNTERKUNFT</h1><br>';
	while($row = $resultU->fetch_assoc()) {
		echo '<button class="tablinks';
		if(in_array($row["ID"], $UidArray)) {
			echo ' select';
		
		}
		echo '" onclick="clickObject(event, '."'U".$row["ID"]."'".')" onmouseover="openObject(event, '."'U".$row["ID"]."'".')">'.$row["Name"].'</button>';
		if(in_array($row["ID"], $UidArray)) {
				echo '<script type="text/javascript">';
			echo '
					var JIDs = [];
					JIDs = sessionStorage.getItem("JIDs");
					if (JIDs == null)
					{
						JIDs = [];
					}
					else
						JIDs = JSON.parse(JIDs);
					JIDs.push('."'U".$row["ID"]."'".');
					sessionStorage.setItem("JIDs",JSON.stringify(JIDs));';
			echo '</script>';
		}
	}

	echo '<br><h1 id="head">SPORTST&AumlTTE</h1><br>';
	while($row = $resultS->fetch_assoc()) {
		echo '<button class="tablinks';
		if(in_array($row["ID"], $SidArray)) {
			echo ' select';

		}
		echo '" onclick="clickObject(event, '."'S".$row["ID"]."'".')" onmouseover="openObject(event, '."'S".$row["ID"]."'".')">'.$row["Name"].'</button>';
		if(in_array($row["ID"], $SidArray)) {
			echo '<script type="text/javascript">';
			echo '
					var JIDs = [];
					JIDs = sessionStorage.getItem("JIDs");
					if (JIDs == null)
					{
						JIDs = [];
					}
					else
						JIDs = JSON.parse(JIDs);
					JIDs.push('."'S".$row["ID"]."'".');
					sessionStorage.setItem("JIDs",JSON.stringify(JIDs));';
			echo '</script>';
		}
	}
	echo '</div>';


	$resultU = $conn -> query($sqlU);
	while($row = $resultU->fetch_assoc()) {
		echo '<div id="U'.$row["ID"].'" class="tabcontent">
		<table style="width:100%">
		<colgroup>
			<col style="width: 40%" />
			<col style="width: 60%" />
		</colgroup>';
		
		echo '<tr><th>Name:</th><td>'.$row["Name"].'</td></tr>';
		if(!is_null($row["Telefonnummer"])) {echo '<tr><th>Telefonnummer:</th><td> '.$row["Telefonnummer"].'</td></tr>';}
		if(!is_null($row["MailAdresse"])) {echo '<tr><th>E-Mail-Adresse:</th><td> '.$row["MailAdresse"].'</td></tr>';}
		if(!is_null($row["Internetseite"])) {echo '<tr><th>Internetseite:</th><td> '.$row["Internetseite"].'</td></tr>';}
		echo '<tr><th>Straße:</th><td> '.$row["Strasse"].'</td></tr>';
		if(!is_null($row["Hausnummer"])) {echo '<tr><th>Hausnummer:</th><td> '.$row["Hausnummer"].'</td></tr>';}
		echo '<tr><th>Postleitzahl:</th><td> '.$row["Postleitzahl"].'</td></tr>';
		echo '<tr><th>Ort:</th><td> '.$row["Ort"].'</td></tr>';
		echo '<tr><th>Land:</th><td> '.$row["Land"].'</td></tr>';
		if(!is_null($row["Kommentar"])) {echo '<tr><th>Kommentar:</th><td> '.$row["Kommentar"].'</td></tr>';}
		
		echo '</table></div>';
	}

	$resultS = $conn -> query($sqlS);
	while($row = $resultS->fetch_assoc()) {
		echo '<div id="S'.$row["ID"].'" class="tabcontent">
		<table style="width:100%">
		<colgroup>
			<col style="width: 40%" />
			<col style="width: 60%" />
		</colgroup>';
		
		echo '<tr><th>Name:</th><td>'.$row["Name"].'</td></tr>';
		if(!is_null($row["Telefonnummer"])) {echo '<tr><th>Telefonnummer:</th><td> '.$row["Telefonnummer"].'</td></tr>';}
		if(!is_null($row["MailAdresse"])) {echo '<tr><th>E-Mail-Adresse:</th><td> '.$row["MailAdresse"].'</td></tr>';}
		if(!is_null($row["Internetseite"])) {echo '<tr><th>Internetseite:</th><td> '.$row["Internetseite"].'</td></tr>';}
		echo '<tr><th>Straße:</th><td> '.$row["Strasse"].'</td></tr>';
		if(!is_null($row["Hausnummer"])) {echo '<tr><th>Hausnummer:</th><td> '.$row["Hausnummer"].'</td></tr>';}
		echo '<tr><th>Postleitzahl:</th><td> '.$row["Postleitzahl"].'</td></tr>';
		echo '<tr><th>Ort:</th><td> '.$row["Ort"].'</td></tr>';
		echo '<tr><th>Land:</th><td> '.$row["Land"].'</td></tr>';
		if(!is_null($row["Kommentar"])) {echo '<tr><th>Kommentar:</th><td> '.$row["Kommentar"].'</td></tr>';}
		
		echo '</table></div>';
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
//tracke Aenderungen
var changed = false;

function clickObject(evt, objectName) {
	changed = true;
	
	if (evt.currentTarget.className.includes(" select")) 
	{
		//Element war bereits erwählt
		evt.currentTarget.className = evt.currentTarget.className.replace(" select", "");
		//Entfernen der Klasse aus den Ausgewählten
		var JIDs = sessionStorage.getItem("JIDs");
		JIDs = JSON.parse(JIDs);
		var index = JIDs.indexOf(objectName);
		JIDs = JIDs.slice(0,index).concat(JIDs.slice(index+1) );
		sessionStorage.setItem("JIDs",JSON.stringify(JIDs));
	}
	else {
		evt.currentTarget.className += " select";
		
		//Füge ID dem session Storage für erwählte IDs hinzu
		var JIDs = [];
		JIDs = sessionStorage.getItem("JIDs");
		if (JIDs == null)
		{
			JIDs = [];
		}
		else
			JIDs = JSON.parse(JIDs);
		JIDs.push(objectName);
		sessionStorage.setItem("JIDs",JSON.stringify(JIDs));
	}
	
}

var submitter = document.getElementById("submitIt");
submitter.addEventListener("click",function()
{
	window.btn_clicked = true;
	
	var elements = sessionStorage.getItem("JIDs");
	var target = document.getElementById("IDs");
	if (target != null && elements != null)
	{
		var strElements = JSON.parse(elements);
		try{ 
		if (strElements.length == 0)
		{
			//Keine Zuweisung
		}
		else
		{
			target.value=elements;
			document.delegate.submit();
		}
		}
		catch(ex)
		{
			
		}
		

	}
	else 
	{
		//TODO:Fehlerbehandlung
	}
});
</script>

<script>
// warne nur, wenn etwas geaendert/geclickt wurde und wenn nicht submittet wird
function sicher(e) {
	if(!window.btn_clicked && changed){
        e.preventDefault();
    }
}

// falls versucht wird, die Seite zu verlassen, warne vor
window.addEventListener('beforeunload', function(e){sicher(e)});
</script>

</body>
</html> 
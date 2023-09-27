<?php
require('../../database.php');
$conn = getConnection();
?>

<!DOCTYPE HTML>
<HTML>
<HEAD>
<TITLE>Direktergebnis</TITLE>
<meta charset="UTF-8">
<LINK rel="stylesheet" href="\general.css">
<LINK rel="stylesheet" href="\colors.css">
<LINK rel="stylesheet" href="\Icons/css/all.css">
<style>
.circle2{
	right: 6%;
	top: 5%;
	position: absolute;
	z-index: +1;
	width: 200px;
	height: 200px;
	border-radius: 50%;
	background-color: white;
}
  table, th, td {
    border: none;
    border-collapse: collapse;
	margin-left: auto;
	margin-right:auto;
	margin-top:2%;
	margin-bottom:2%;
}
td {
    text-align: left; 	
	margin-left: 5px;
}
th {
    text-align: right;
	padding-right: 15px;
}
p{
	width:97%;
	padding:10px;
	float:left;
	background-color: #e3e3e3;
	font-weight: normal;
	border: 1px solid grey;
	border-radius: 4px;
	color: #7c6d6d;
	text-align:left;
	box-shadow: inset 0 1px 3px #ddd;
	word-wrap: normal;
	overflow-wrap: break-word;
	white-space: normal;
	-webkit-line-clamp: 1;
	-webkit-box-orient: vertical;
	min-width: 350px;
}

.Unterkunft, .Sportstätte, .Kontaktperson{
	margin-right:auto;
	margin-left:auto;
	margin-bottom: 1%;
	padding: 0px 12px;
	border: 1px solid #ccc;
	width: 55%;
	height: auto;
	background-color:white;
	word-wrap: normal;
	word-break: break-all;
	white-space: normal;
	-webkit-line-clamp: 1;
	-webkit-box-orient: vertical;
	min-width: 450px;
}

.UnterkunftUnter, .SportstätteUnter{
	margin: 1%;
	padding: 0px 0px;
	border: 1px solid #ccc;
	width: 90%;
	height: auto;
	background-color:white;
}

.Anzahl{
	margin-left:auto;
	margin-right:auto;
	padding: 0px 0px;
	width: 55%;
	font-weight: bold;
}
.Unterkunft:hover, .Sportstätte:hover, .UnterkunftUnter:hover, .SportstätteUnter:hover
{
	background-color: #848484;
	color: #F8F8FF;
	cursor: pointer;
}

#beendenBox, #zuruckBox
{
	text-align:right;
	width:71.75%;
	float:right;
	margin-right: 14.125%;
}


</style>
</HEAD>
<body>

<script>
function leave_site() {
	window.location.replace("../../Index.php");
}
</script>

<div class="color" id="colorSuche">
<button type="button" onclick="leave_site()" class="HomeB">Home</button></br></br>
<h1><b></br></b></h1></br></br>
</div>
<div class="circle"><i style='font-size:50px;margin-top:35%;margin-left:37%;color:grey;' class='fas'>&#xf5eb;</i></div>


</br></br>
<!-- Stelle Gesuchtes dar --->
<table id="Search" style="width:60%">
<colgroup>
    <col style="width: 80%" />
	<col style="width: 20%" />
  </colgroup>
<tr>
  <th><p><?php if (isset($_POST['search2'])) echo $_POST['search2'].'<tab style="padding-left: 4em;">in: '.$_POST['wähle'];?> </p></th>
  <td><button type="button" id="zuruck" onclick="Go_Back()">Suche anpassen</button></td>
</tr></table>

</br>
<div id="content" style="width:80%;margin-left:10%;">
<?php
/* Direktsuche mit Auswahl "Unterkunft" */
	if(isset($_POST['submit'])&& ($_POST['wähle'] == "Unterkunft" || $_POST['wähle'] == "Alle")){
		$search = mysqli_real_escape_string($conn,$_POST['search2']);
		$sql = 'SELECT * FROM unterkunft WHERE Name LIKE "%'.$search.'%" OR Telefonnummer LIKE "%'.$search.'%" OR MailAdresse LIKE "%'.$search.'%" OR Strasse LIKE "%'.$search.'%" OR Ort LIKE "%'.$search.'%" OR Internetseite LIKE "%'.$search.'%" OR Postleitzahl LIKE "%'.$search.'%";';
		$result =$conn->query($sql);
		$queryResults = mysqli_num_rows($result);
		
		if ($_POST['wähle'] == "Alle")
		{
			/*
			if ($queryResults == 1) {echo '</br><div class="Anzahl">Es wurde 1 Ergebnis innerhalb der Unterkünfte zur Eingabe "'.$search.'" gefunden!</div></br>';}
			else {echo '</br><div class="Anzahl">Es wurden '.$queryResults.' Ergebnisse innerhalb der Unterkünfte zur Eingabe "'.$search.'" gefunden!</div></br>';}
			*/
			
			if ($queryResults == 1) {echo '</br><div class="Anzahl"> Unterkünfte: 1 Ergebnis</div></br>';}
			else {echo '</br><div class="Anzahl">Unterkünfte: '.$queryResults.' Ergebnisse</div></br>';}
		}
		else
		{
			/*
			if ($queryResults == 1) {echo '</br><div class="Anzahl">Es wurde '.$queryResults.' Ergebnis gefunden zur Eingabe "'.$search.'"!</div></br>';}
			else {echo '</br><div class="Anzahl">Es wurden '.$queryResults.' Ergebnisse gefunden zur Eingabe "'.$search.'"!</div></br>';}
			*/
			
			if ($queryResults == 1) {echo '</br><div class="Anzahl">1 Ergebnis</div></br>';}
			else {echo '</br><div class="Anzahl">'.$queryResults.' Ergebnisse</div></br>';}
		}
		
		if ($queryResults > 0) { /*Sofern es Ergebnisse gibt*/
		while($row = $result->fetch_assoc()) {
			echo '<div class="Unterkunft" onClick=delegate("u'.$row['ID'].'&dist_km=0&dist_h=0")>
			<i style=font-size:24px;float:right;margin-top:10px;" class="fas">&#xf05a;</i>
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
			if(!is_null($row["Kommentar"])) {echo '<tr><th style="verical-align:top;">Kommentar</th><td> '.$row["Kommentar"].'</td></tr>';}
		
			echo '</table></div>';
			}
		} else {
			//echo "Es wurden keine Ergebnisse zu ihrer Suche gefunden!";
		}
	}
	/* Direktsuche mit Auswahl "Sportstätte" */
	if(isset($_POST['submit'])&& ($_POST['wähle'] == "Sportstätte" || $_POST['wähle'] == "Alle")){
		$search = mysqli_real_escape_string($conn, $_POST['search2']);
		$sql = 'SELECT * FROM sportstaette WHERE Name LIKE "%'.$search.'%" OR Telefonnummer LIKE "%'.$search.'%" OR MailAdresse LIKE "%'.$search.'%" OR Strasse LIKE "%'.$search.'%" OR Ort LIKE "%'.$search.'%" OR Internetseite LIKE "%'.$search.'%" OR Postleitzahl LIKE "%'.$search.'%";';
		$result =$conn->query($sql);
		$queryResults = mysqli_num_rows($result);
		
		if ($_POST['wähle'] == "Alle")
		{
			/*
			if ($queryResults == 1) {echo '</br><div class="Anzahl">Es wurde 1 Ergebnis innerhalb der Sportstätten zur Eingabe "'.$search.'" gefunden!</div></br>';}
			else {echo '</br><div class="Anzahl">Es wurden '.$queryResults.' Ergebnisse innerhalb der Sportstätten zur Eingabe "'.$search.'" gefunden!</div></br>';}
			*/
			
			if ($queryResults == 1) {echo '</br><div class="Anzahl"> Sportstätten: 1 Ergebnis</div></br>';}
			else {echo '</br><div class="Anzahl">Sportstätten: '.$queryResults.' Ergebnisse</div></br>';}
		}
		else
		{
			/*
			if ($queryResults == 1) {echo '</br><div class="Anzahl">Es wurde '.$queryResults.' Ergebnis gefunden zur Eingabe "'.$search.'"!</div></br>';}
			else {echo '</br><div class="Anzahl">Es wurden '.$queryResults.' Ergebnisse gefundenzur Eingabe "'.$search.'"!</div></br>';}
			*/
			
			if ($queryResults == 1) {echo '</br><div class="Anzahl">1 Ergebnis</div></br>';}
			else {echo '</br><div class="Anzahl">'.$queryResults.' Ergebnisse</div></br>';}
		}
		
		if ($queryResults > 0) {
		while($row = $result->fetch_assoc()) {
			echo '<div class="Sportstätte" onClick=delegate("s'.$row['ID'].'&dist_km=0&dist_h=0")>
			<i style=font-size:24px;float:right;margin-top:10px;" class="fas">&#xf05a;</i>
				<table style="width:100%">
			<colgroup>
				<col style="width: 40%" />
				<col style="width: 60%" />
			</colgroup>';
		
			//echo '<tr><th>Eingabe</th><td>"'.$search.'"</td></tr>';
			echo '<tr><th>Name</th><td>'.$row["Name"].'</td></tr>';
			if(!is_null($row["Telefonnummer"])) {echo '<tr><th>Telefonnummer</th><td> '.$row["Telefonnummer"].'</td></tr>';}
			if(!is_null($row["MailAdresse"])) {echo '<tr><th>E-Mail-Adresse</th><td> '.$row["MailAdresse"].'</td></tr>';}
			if(!is_null($row["Internetseite"])) {echo '<tr><th>Internetseite</th><td> '.$row["Internetseite"].'</td></tr>';}
			echo '<tr><th>Straße</th><td> '.$row["Strasse"].'</td></tr>';
			if(!is_null($row["Hausnummer"])) {echo '<tr><th>Hausnummer</th><td> '.$row["Hausnummer"].'</td></tr>';}
			if(!is_null($row["Postleitzahl"])) {echo '<tr><th>Postleitzahl</th><td> '.$row["Postleitzahl"].'</td></tr>';}
			echo '<tr><th>Ort</th><td> '.$row["Ort"].'</td></tr>';
			echo '<tr><th>Land</th><td> '.$row["Land"].'</td></tr>';
			if(!is_null($row["Kommentar"])) {echo '<tr><th style="verical-align:top;">Kommentar</th><td> '.$row["Kommentar"].'</td></tr>';}
		
			echo '</table></div>';
			}
		} else {
			//echo "Es wurden keine Ergebnisse zu ihrer Suche gefunden!";
		}
	}
	/* Direktsuche mit Auswahl "Kontaktperson" */
	if(isset($_POST['submit'])&& ($_POST['wähle'] == "Kontaktperson" || $_POST['wähle'] == "Alle")){
		$search = mysqli_real_escape_string($conn, $_POST['search2']);
		$sql = 'SELECT * FROM kontaktpersonen WHERE Vorname LIKE "%'.$search.'%" OR Nachname LIKE "%'.$search.'%" OR Telefonnummer LIKE "%'.$search.'%" OR MailAdresse LIKE "%'.$search.'%" OR Mobilnummer LIKE "%'.$search.'%";';
		$result =$conn->query($sql);
		$queryResults = mysqli_num_rows($result);
		
		if ($_POST['wähle'] == "Alle")
		{
			/*
			if ($queryResults == 1) {echo '</br><div class="Anzahl">Es wurde 1 Ergebnis innerhalb der Kontaktpersonen zur Eingabe "'.$search.'" gefunden!</div></br>';}
			else {echo '</br><div class="Anzahl">Es wurden '.$queryResults.' Ergebnisse innerhalb der Kontaktpersonen zur Eingabe "'.$search.'" gefunden!</div></br>';}
			*/
			
			if ($queryResults == 1) {echo '</br><div class="Anzahl"> Kontaktpersonen: 1 Ergebnis</div></br>';}
			else {echo '</br><div class="Anzahl">Kontaktpersonen: '.$queryResults.' Ergebnisse</div></br>';}
		}
		else
		{		
			/*if ($queryResults == 1) {echo '</br><div class="Anzahl">Es wurde '.$queryResults.' Ergebnis gefunden!</div></br>';}
			else {echo '</br><div class="Anzahl">Es wurden '.$queryResults.' Ergebnisse gefunden!</div></br>';}
			*/
			
			if ($queryResults == 1) {echo '</br><div class="Anzahl">1 Ergebnis</div></br>';}
			else {echo '</br><div class="Anzahl">'.$queryResults.' Ergebnisse</div></br>';}
		}
		
		if ($queryResults > 0) {
		while($row = $result->fetch_assoc()) {
			echo '<div class="Kontaktperson">
				<table style="width:100%">
			<colgroup>
				<col style="width: 40%" />
				<col style="width: 60%" />
			</colgroup>';
		
			if(!is_null($row["Vorname"])){echo '<tr><th>Vorname</th><td>'.$row["Vorname"].'</td></tr>';}
			echo '<tr><th>Nachname</th><td>'.$row["Nachname"].'</td></tr>';
			if(!is_null($row["Telefonnummer"])) {echo '<tr><th>Telefonnummer</th><td> '.$row["Telefonnummer"].'</td></tr>';}
			if(!is_null($row["MailAdresse"])) {echo '<tr><th>E-Mail-Adresse</th><td> '.$row["MailAdresse"].'</td></tr>';}
			if(!is_null($row["Mobilnummer"])) {echo '<tr><th>Mobilnummer</th><td> '.$row["Mobilnummer"].'</td></tr>';}
			if(!is_null($row["Fax"])) {echo '<tr><th>Fax</th><td> '.$row["Fax"].'</td></tr>';}
			if(!is_null($row["Funktion"])) {echo '<tr><th>Funktion </th><td> '.$row["Funktion"].'</td></tr>';}
			if(!is_null($row["Kommentar"])) {echo '<tr><th style="verical-align:top;">Kommentar</th><td> '.$row["Kommentar"].'</td></tr>';}
			$ID = $row['ID'];
			//Einfügen der zugewiesenen Objekte
			$sqlVerk = "SELECT S.Name,S.ID FROM kontakte_sportstaette as K JOIN sportstaette as S on K.SSID=S.ID WHERE k.KPID=".$ID;
			$queryResult = $conn ->query($sqlVerk);
			if (mysqli_num_rows($queryResult) > 0) {
				echo'<tr><th>zugeordnete Sportstätte</th><td><table style=width:100%;>';
				while($row = $queryResult->fetch_assoc()) {
					echo'<tr><td><div class="SportstätteUnter" onClick=delegate("s'.$row['ID'].'&dist_km=0&dist_h=0")><div style="margin:3%;">'.$row['Name'].'<i style=top:2%;left:4%;float:right;" class="fas">&#xf05a;</i></div></div></td></tr>';
				}
				echo '</table></td></tr>';
			}
			$sqlVerk = "SELECT S.Name,S.ID FROM kontakte_unterkunft as K JOIN unterkunft as S on K.UID=S.ID WHERE k.KPID=".$ID;
			$queryResult = $conn ->query($sqlVerk);
			if (mysqli_num_rows($queryResult) > 0) {
				echo'<tr><th>zugeordnete Unterkunft</th><td><table style=width:100%;>';
				while($row = $queryResult->fetch_assoc()) {
					echo'<tr><td><div class="UnterkunftUnter" onClick=delegate("u'.$row['ID'].'&dist_km=0&dist_h=0")><div style="margin:3%;">'.$row['Name'].'<i style=float:right;top:2%;left:4%;" class="fas">&#xf05a;</i></div></div></td></tr>';
				}
				echo '</table></td></tr>';
			}
			
			echo '</table></div>';
			}
		} else {
			//echo "Es wurden keine Ergebnisse zu ihrer Suche gefunden!";
		}
	}
echo "</div>";
	
$conn->close();
?>

<div id="zuruckBox">
<div id="beendenBox"><button class="continueB" type="button"id ="beenden" onclick="leave_site2()" style="float:right;">Suche beenden</button></div>
</div>
<form name="backer" action="Direktsuche.php" method="POST">
<input type="hidden" name="wahl" value="<?php echo $_POST['wähle']?>"/>
<input type="hidden" name="searchInput" value="<?php echo $_POST['search2']?>" />
</form>
<script>
function delegate(url)
{
	//window.location = "Detailseite.php?submitID="+url;
	window.open("../Detailseite.php?submitID="+url, "_blank");
}
function Go_Back()
{
	document.backer.submit();
}
function leave_site2(){
	window.location.replace("Direktsuche.php")
}
</script>
</body>
</HTML>
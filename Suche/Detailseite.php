<HTML>
<HEAD>
<?php
//Holen der benoetigten Informationen von window.opener
$oID = $_GET["submitID"];
$dist_km = $_GET["dist_km"];
$dist_h = $_GET["dist_h"];

$typ = substr($oID, 0, 1);
$id = substr($oID, 1);

//Name auf dem Titelbild
include('../database.php');
$conn = getConnection();

$query = "SELECT * FROM ";
if($typ == "u"){
	$query = $query."unterkunft";
}else{
	$query = $query."sportstaette";
}
$query = $query." WHERE ID = ".$id;

$result = $conn->query($query);
$row = $result->fetch_assoc();

if ($result != null && $result->num_rows > 0)
{
	$name = $row["Name"];
}
?>
<TITLE><?php echo $name; ?></TITLE>
<meta charset="UTF-8">
<LINK rel="stylesheet" href="\general.css">
<LINK rel="stylesheet" href="\colors.css">
<LINK rel="stylesheet" href="styles/DetailStyle.css">
<LINK rel="stylesheet" href="\Icons/css/all.css">
</HEAD>
<BODY>

<?php
//Banner je nach Objektart
if($typ == "u"){
	echo '<div class="color" id="colorU1">';
}else{
	echo '<div class="color" id="colorS2">';
}
echo '</br></br>';
echo '<h1>'.$name.'</h1>';
?>
</br></br>
</div>
</br>
<div class="circle"><i style='font-size:50px;margin-top:35%;color:grey;' class='fas'>&#xf5a0;</i></div>
<?php
// Berechnung von Entfernung in km und Zeit fuer Umgebungssuche
$time = explode('_', $dist_h);
if (count($time) == 2)
{
	$hours = $time[0];
	$min = $time[1];
}
else
{
	$hours = null;
	$min = null;
}

if($hours != null && $min != null){
	// Ausgabe der Zeit und Entfernung
	echo '<div class="info entf"><table style="width: 90% !important; margin-left: 5%;"><tr><td style="width: 25% !important;"><h4>Entfernung			</h4></td><td style="width: 25% !important;">'.$dist_km.' km</td>';
	if($hours == 0){
		echo '<td style="width: 25% !important;"><h4>Fahrzeit			</h4></td><td style="width: 25% !important;">'.$min.' min</td></tr></table>';
	}else
	{
		echo '<td style="width: 25% !important;"><h4>Fahrzeit			</h4></td><td style="width: 25% !important;">'.$hours.' h '.$min.' min</td></tr></table>';
	}
	echo "</div>";
}

if(isset($_GET["attrListe".strtoupper($typ)])){
	//Auswertung der überlieferten Attribute fuer Umgebungssuche
	$attrListe = json_decode($_GET["attrListe".strtoupper($typ)]);
	$attrEntries = json_decode($_GET["attrEntries".strtoupper($typ)]);
	
	// Ausgabe der nicht zutreffenden Attribute
	if(sizeof($attrListe) != 0){
		$not_satisfied = 0;
		
		for ($i = 0; $i < sizeof($attrListe); $i++)
		{
			if($attrEntries[$i] == 0){
				// verhindert, dass eine div erstellt wird, die nicht gebruacht wird
				if($not_satisfied == 0){
					echo '</br></br><div class="info entf">';
					echo '<div class="unsatisfied_box"><h4 style="text-align: left;">Nicht erfüllte Suchkriterien</h4></br>';
				}
				
				echo "<div class='unsatisfied'>".$attrListe[$i]."</div>";
				/*
				// Entferne ni cht mehr benoetigtest Element, um Aufwand fuer erfuellte Attribute zu verkleinern
				\array_splice($attrListe, $i);
				\array_splice($attrEntries, $i);
				
				var_dump($attrListe);
				var_dump($attrEntries);
				*/
				$not_satisfied++;
			} 
		}
		
		// schließe div nur, wenn sie erstellt wird
		if($not_satisfied != 0) echo "</div></div>";
	}
}
else
	$attrListe = [];
?>

</br></br>
<div class="ramen">
<div class="left">
<div class="info">
<?php
// Angabe Adresse
echo '<h3>Adresse</h3>';

echo '<table>';
echo '<tr><td> '.$row["Strasse"];
if(!is_null($row["Hausnummer"])) {echo ' '.$row["Hausnummer"];}
echo '</td></tr>';
echo '<tr><td> '.$row["Postleitzahl"]." ";
echo $row["Ort"].'</td></tr>';
if(!is_null($row["Land"])) {echo '<tr><td> '.$row["Land"].'</td></tr>';}
echo '</table>';

?>
</div>
</br></br>
<div class="info">
<?php
// Angabe Preis
//Button zur Preisänderung
	echo '<div><h3 id="priceEdit">Preise</h3><button type="button" id="editprices" value="0" onclick=EditPrices("'.trim($typ).'","'.$id.'")><i class="fas fa-edit"></i></button></div>';
	echo '<table id="tablePrice">';

$preis = $row["KommentarPreis"];
$keineAusgabeCounter = 0;
if($preis != null && trim($preis) != ""){
	//Dekodierung Preis-String
	//Split in Jahre - backslash muss durch backslash escaped werden, sodass da eigentlich nur einer steht
	$jahre = explode("\\", $preis);
	for($i = 0; $i < sizeof($jahre); $i++)
	{
		$preisInf = explode("|", $jahre[$i]);
		$keinJahr = false;
		// Ausgabe Jahr
		if (trim($preisInf[0]) == "")
		{
			$keinJahr = true;
			echo '<tr><th><h4 style="text-align: left;font-size: initial;"></h4></th><td></td></tr>'.
				'<tr><th></th><td></td></tr>';
		}
		else
			echo '<tr><th><h4 style="text-align: left;font-size: initial;">'.$preisInf[0].'</h4></th><td></td></tr>';
		// Ausgabe Gruppen + Preis
		$ausgabe = false;
		$inner = "";
		for($j = sizeof($preisInf)-2; $j >= 0; $j=$j-2)
		{
			if (trim($preisInf[$j]) != "" || $ausgabe)
			{
				$inner = '<tr><th>'.$preisInf[$j].'</th><td>'.$preisInf[$j+1].'</td></tr>' . $inner;
				$ausgabe = true;
			}
		}
		if(!$ausgabe)
		{			
			if (!$keinJahr)
				echo '<tr><th colspan="2" style="font-weight: normal;">Keine Preisinformationen für dieses Jahr angegeben.</th></tr>';
			else
				$keineAusgabeCounter +=1;
		}
		else
			echo $inner;
		echo '<tr style="height: 20px !important;"></tr>';
		
	}
	
}else{
	echo '<p class="empty" id="noPrices">Es sind keine Preisinformationen vorhanden.</p>';
}
if ($keineAusgabeCounter == 3)
	echo '<p class="empty" id="noPrices">Es sind keine Preisinformationen vorhanden.</p>';

echo '</table>';
?>
</div>
</br></br>
<div class="info">
<?php
// Angabe Details
echo '<h3>Details</h3>';
echo '<table>';

$detailFull = false;

//bool Attribute
if($typ == "u")
{
	$sql = "SELECT * FROM ubesitzt_bool WHERE UID=".$id;
}else{
	$sql = "SELECT * FROM ssbesitzt_bool WHERE SSID=".$id;
}
$resultD = $conn -> query($sql);

//var_dump($attrListe);
//var_dump($attrEntries);
if($result === false) {
	echo "FEHLER: ".$conn->error;
} else {
	while($rowD=$resultD->fetch_assoc()){
		if($rowD["Wert"]==1) {
			$index = array_search($rowD["AName"], $attrListe);
			//var_dump($index != false);
			//var_dump($attrEntries[$index] == 1);
			if(!is_bool($index) && $attrEntries[$index] == 1)	
				echo '<tr><th><div class="satisfied_th">'.$rowD["AName"].'</div></th><td> Ja </td></tr>';
			else
				echo '<tr><th>'.$rowD["AName"].'</th><td> Ja </td></tr>';
		} else {
			echo '<tr><th>'.$rowD["AName"].'</th><td> Nein </td></tr>';	//wenn wir auch negative bool Aussagen haben - wird zuzeit nicht benoetigt
		}
		$detailFull = true;
	}
}

//charc Attribute
if($typ == "u")
{
	$sql = "SELECT * FROM ubesitzt_char WHERE UID=".$id;
}else{
	$sql = "SELECT * FROM ssbesitzt_char WHERE SSID=".$id;
}
$resultD = $conn -> query($sql);

if($resultD === false) {
	echo "FEHLER: ".$conn->error;
} else {
	while($rowD=$resultD->fetch_assoc()){
		$index = array_search($rowD["AName"], $attrListe);
		if(!is_bool($index) && $attrEntries[$index] == 1)
			echo '<tr><th><div class="satisfied_th">'.$rowD["AName"].'</div></th><td> '.$rowD["Wert"].'</td></tr>';
		else
			echo '<tr><th>'.$rowD["AName"].'</th><td> '.$rowD["Wert"].'</td></tr>';
		$detailFull = true;
	}
}

//int Attribute
if($typ == "u")
{
	$sql = "SELECT * FROM ubesitzt_int WHERE UID=".$id;
}else{
	$sql = "SELECT * FROM ssbesitzt_int WHERE SSID=".$id;
}
$resultD = $conn -> query($sql);

if($resultD === false) {
	echo "FEHLER: ".$conn->error;
} else {
	while($rowD=$resultD->fetch_assoc()){
		$index = array_search($rowD["AName"], $attrListe);
		if(!is_bool($index) && $attrEntries[$index] == 1)
			echo '<tr><th><div class="satisfied_th">'.$rowD["AName"].'</div></th><td> '.$rowD["Wert"].'</td></tr>';
		else
			echo '<tr><th>'.$rowD["AName"].'</th><td> '.$rowD["Wert"].'</td></tr>';
		$detailFull = true;
	}
	
}
	
	
//Kommentar mit vorangehender Leerzeile
if(!is_null($row["Kommentar"])) 
{
	echo '<tr style="height: 20px !important;"></tr><tr><th style="vertical-align: top;">Kommentar
	<div style="text-align:center"><button type="button" id="editCommentObject" value="'.$typ.'" onclick="OnEditObject('.$id.')"><i class="fas fa-edit"></i></button></div></th>
	<td id="commentObject"> '.$row["Kommentar"].'</td></tr>'; 
	$detailFull = true;
	
	
}

if(!$detailFull)
{
	echo '<p class="empty">Es sind keine Details vorhanden.</p>';
}

echo '</table>';
?>
</div>
</div>

<div class="right">
<div class="info">
<?php
// Angabe Kontakt
echo '<h3>Kontakt</h3>';

$kontaktFull = false;

echo '<table>';
if(!is_null($row["Telefonnummer"])) {echo '<tr><th>Telefonnummer</th><td> '.$row["Telefonnummer"].'</td></tr>'; $kontaktFull = true;}
if(!is_null($row["MailAdresse"])) {echo '<tr><th>E-Mail-Adresse</th><td> '.$row["MailAdresse"].'</td></tr>'; $kontaktFull = true;}
if(!is_null($row["Internetseite"])) 
{
	$splittsWebsite = explode('/',$row["Internetseite"]);
	if ($splittsWebsite != null && $splittsWebsite[0] != "https:" && $splittsWebsite[0] != "http:") // Format: www.abc.de oder www.abc.de/ oder www.abc.de/def/
		$web = $splittsWebsite[0];
	else if ($splittsWebsite != null && ($splittsWebsite[0] == "https:" || $splittsWebsite[0] == "http:") && count($splittsWebsite) >= 3)
		$web = $splittsWebsite[2];
	
	if($splittsWebsite[0] == "https:" || $splittsWebsite[0] == "http:")
		echo '<tr><th>Internetseite</th><td><a href="'.$row["Internetseite"].'" target="_blank">'.$web.'</a></td></tr>';
	else{
		echo '<tr><th>Internetseite</th><td><a href="https://'.$row["Internetseite"].'" target="_blank">'.$web.'</a></td></tr>';
	}
	$kontaktFull = true;
}
echo '</table>';


//Hole Kontaktpersonen
if($typ == "u")
{
	$sql = "SELECT * FROM kontakte_unterkunft WHERE UID=".$id;
}else{
	$sql = "SELECT * FROM kontakte_sportstaette WHERE SSID=".$id;
}
$resultK = $conn -> query($sql);

//Stelle einzelne Kontaktpersonen dar
while($rowK=$resultK->fetch_assoc()){
	echo '<table><tr style="height: 20px !important;"></tr></table>';
	$kontaktFull = true;
	
	$sqlK = "SELECT * FROM kontaktpersonen WHERE ID=".$rowK["KPID"];
	$resultKP = $conn->query($sqlK);
	
	while($rowKP=$resultKP->fetch_assoc())
	{
		if(!is_null($rowKP["Vorname"])){
			echo '<table><tr><th><h4 style="text-align: left;font-size: initial;">'.$rowKP["Vorname"]." ".$rowKP["Nachname"].'</h4></th></tr></table>';
		}else{
			echo '<table><tr><th><h4 style="text-align: left;font-size: initial;">'.$rowKP["Nachname"].'</h4></th></tr></table>';
		}
		
		echo "<table>";
		$KPFull = false;
		//Stelle Attribute der Kontaktperson dar
		if(!is_null($rowKP["Funktion"])){echo '<tr><th>Funktion</th><td>'.$rowKP["Funktion"].'</td><tr style="height: 4px !important;"></tr></tr>'; $KPFull = true;}
		if(!is_null($rowKP["MailAdresse"])){echo '<tr><th>E-Mail-Adresse</th><td>'.$rowKP["MailAdresse"].'</td></tr>'; $KPFull = true;}
		if(!is_null($rowKP["Telefonnummer"])){echo '<tr><th>Telefonnummer</th><td>'.$rowKP["Telefonnummer"].'</td></tr>'; $KPFull = true;}
		if(!is_null($rowKP["Mobilnummer"])){echo '<tr><th>Mobilnummer</th><td>'.$rowKP["Mobilnummer"].'</td></tr>'; $KPFull = true;}
		if(!is_null($rowKP["Fax"])){echo '<tr><th>Fax</th><td>'.$rowKP["Fax"].'</td></tr>'; $KPFull = true;}
		if(!is_null($rowKP["Kommentar"]))
		{
			echo '<tr><th>Kommentar <div style="text-align:center">
			<button type="button" id="editCommentKP'.$rowKP["ID"].'" value="0" onclick="OnEditKP('.$rowKP["ID"].')"><i class="fas fa-edit"></i></button>
			</div></th><td id="commentKP'.$rowKP["ID"].'">'.$rowKP["Kommentar"].'</td></tr>'; 
			$KPFull = true;
		}
		echo "</table>";
		
		if(!$KPFull)
		{
			echo '<tr style="height: 2px !important;"></tr>';
			echo '<tr class="empty"><td>Keine weiteren Angaben vorhanden.</td></tr>';
		}
	}
}

if(!$kontaktFull)
{
	echo '<p class="empty" style="margin-top: 0;">Es sind keine Kontaktinformationen vorhanden.</p>';
	
}

$conn->close();
?>
</div>
</div>
<div style="margin: auto; width: 100%; position: relative; float: left;"></br></br></div>
</div>
<!-- Abstand zum Seitenende -->
<form id="changeDelegate" action=<?php if($typ == "u"){echo '../Erstellen/Bearbeiten/Unterkunft/UebersichtUnterkunft.php';}else{echo '../Erstellen/Bearbeiten/Sportstaette/UebersichtSportstaette.php';}?> method="POST">
  <input id="ID" name="ID" type="hidden" value="default">
</form>

<div id="edit"> <!-- TODO ID zu String-->
<button type="button" id="change" onclick="changeDelegate('<?php echo $id;?>')"><?php if($typ == "u"){echo 'Unterkunft';}else{echo 'Sportstätte';}?> bearbeiten</button>
</div>
<script>
function changeDelegate(ID) {
 document.getElementById("ID").value = ID;
  
  var hiddenID = document.getElementById("changeDelegate");
  if(hiddenID != null)
  {
	  hiddenID.submit();
  }
}
var save = false;
function OnEditObject(ID)
{
	var button = document.getElementById("editCommentObject");
	var commentTd = document.getElementById("commentObject");
	if (save == false)
	{		
		//Ändern in die Eingabe
		var text = commentTd.innerHTML;
		commentTd.innerHTML = '<textarea id="inputCommentObject">'+text+'</textarea>';
		button.innerHTML = '<i class="fas fa-save"></i>';
		save = true;
	}
	else
	{
		//Speichern und ändern in die Ausgabe
		var text = commentTd.firstChild.value;
		//Sende eine Request an die Datei dynamischeAenderungen, um die Änderungen durchzuführen
		typ = button.value;
		var request = new XMLHttpRequest();
		request.onreadystatechange=function() 
		{
			if (this.readyState==4 && this.status==200) 
			{
				if (this.responseText == "TRUE")
				{
					commentTd.innerHTML = text;
					button.innerHTML = '<i class="fas fa-edit"></i>';
					save = false;
				}
				else 
					console.log(this.responseText);
			}
		}
		request.open('POST','dynamischeAenderungen.php');
		request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		request.send('typ='+typ+'&text='+text+'&ID='+ID+'&Art=Kommentar');
		button.innerHTML = '<i class="fas fa-spinner"></i>';
	}
	
}

function OnEditKP(ID)
{
	var button = document.getElementById("editCommentKP"+ID);
	var commentTd = document.getElementById("commentKP"+ID);
	if (button.value == "0")
	{		
		//Ändern in die Eingabe
		var text = commentTd.innerHTML;
		commentTd.innerHTML = '<textarea id="inputCommentObject">'+text+'</textarea>';
		button.innerHTML = '<i class="fas fa-save"></i>';
		button.value = "1";
	}
	else if (button.value == "1")
	{
		//Speichern und ändern in die Ausgabe
		var text = commentTd.firstChild.value;
		//Sende eine Request an die Datei dynamischeAenderungen, um die Änderungen durchzuführen
		typ = button.value;
		var request = new XMLHttpRequest();
		request.onreadystatechange=function() 
		{
			if (this.readyState==4 && this.status==200) 
			{
				if (this.responseText == "TRUE")
				{
					commentTd.innerHTML = text;
					button.innerHTML = '<i class="fas fa-edit"></i>';
					button.value = "0";
				}
				else 
					console.log(this.responseText);
			}
		}
		request.open('POST','dynamischeAenderungen.php');
		request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		request.send('typ=k&text='+text+'&ID='+ID+'&Art=Kommentar');
		button.innerHTML = '<i class="fas fa-spinner"></i>';
	}
	
}

function EditPrices(typ, id)
{
	var button = document.getElementById("editprices");
	//1 für Speicherung 0 für Umwandlung
	if (button != null && button.value == 1)
	{
		//Abspeichern der Änderungen 
		var request = new XMLHttpRequest();
		request.onreadystatechange=function() 
		{
			if (this.readyState==4 && this.status==200) 
			{
				if (this.responseText == "TRUE")
				{				
					//Darstellen der neuen Tabelle
					//Senden der neuen Anfrage an Nachladen um die Änderungen direkt aus der Datenbank abzurufen
					var requestContent = new XMLHttpRequest();
					requestContent.onreadystatechange=function() 
					{
						if (this.readyState==4 && this.status==200) 
						{
							var table = document.getElementById("tablePrice");
							table.innerHTML = this.responseText;
							
							//Darstellen des Buttons zum editieren -- > Änderung Speichern ist abgeschlossen
							button.innerHTML = '<i class="fas fa-edit"></i>';
							button.value = "0";
						}
					}										
					requestContent.open('POST','dynamischeAenderungen.php');
					requestContent.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					requestContent.send('typ='+typ+'&text='+" "+'&ID='+id+'&Art=AbfragePreis');
				}
				else 
					console.log(this.responseText);
			}
		}
		//Zusammenbauen der Anfrage an Nachladen um die Änderungen einzupflegen
		var senderText = " ";
		for(var i = 1; i < 4; i++) //i - Jahresiterator
		{
			var jahr = "";
			if (document.getElementsByName('J'+i)[0] != null && document.getElementsByName('J'+i)[0].value != "" && document.getElementsByName('J'+i)[0].value != " ")
			{
				jahr = document.getElementsByName('J'+i)[0].value.replace("\\","").replace("|","");
			}
				//Hinzufügen des Jahres
				if (i != 1)
					senderText += "\\";			
				senderText += jahr;			
				var rI = 1; // Zeileniterator
				var curObj = document.getElementsByName("B"+i.toString()+rI.toString());					
				//while(curObj != null && curObj.length > 0 && curObj[0].value.length >0 )
				while(curObj != null && curObj.length > 0 )
				{
					senderText += "|";
					//Hinzufügen des Bezeichners
					senderText+= curObj[0].value.replace("\\","").replace("|","")+"|";
					curObj = document.getElementsByName("C"+i.toString()+rI.toString())[0];
					//Hinzufügen des Inhalts
					senderText+= curObj.value.replace("\\","").replace("|","");
					//Hochzählen des Zeileniterator und feststellen des nächsten Elements
					rI++;
					curObj = document.getElementsByName("B"+i.toString()+rI.toString());
				}
				while (rI <= 5)
				{
					senderText += "||";
					rI++;
				}
			
		}
		request.open('POST','dynamischeAenderungen.php');
		request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		request.send('typ='+typ+'&text='+senderText+'&ID='+id+'&Art=Preis');
		console.log(senderText);
		button.innerHTML = '<i class="fas fa-spinner"></i>';
				
	}
	else if (button != null)
	{
		//Darstellen des Buttons zum Speichern
		button.innerHTML = '<i class="fas fa-save"></i>';
		button.value = 1;
		//Umwandlung einer eventuell vorhandenen Tabelle in Edit Felder
		//Falls keine Tabelle vorhanden ist füge drei leere Felder eine
		var table = document.getElementById("tablePrice");
		if (table != null)
		{			
			if (document.getElementById("noPrices") != null && document.getElementById("noPrices").style.display != "none")
			{
				//Es sind bisher keine Preisinformationen vorhanden
				//Erstellen der anfänglichen Tabelle mit 3 Jahren und je einer Zeile
				var inner = '<tr><th><input type="text" name="J1" placeholder="Jahr 1" maxlength=4></th><td></td><td></td></tr>'+
				'<tr><td><input type="text" name="B11" placeholder="Typ 1" maxlength=30></td><td><input type="text" name="C11" placeholder="Preis 1" maxlength=8></td>'+
				'<td style="width:30%;"><button type="button" value="1" id="1A" class="adder" style="margin:2px;">+</button><button type="button" class="miner">-</button></td></tr>'+
				'<tr style="height: 20px !important;"></tr>'+
				'<tr><th><input type="text" name="J2" placeholder="Jahr 2" maxlength=4></th><td></td></tr>'+
				'<tr><td><input type="text" name="B21" placeholder="Typ 1" maxlength=30></td><td><input type="text" name="C21" placeholder="Preis 1" maxlength=8></td>'+
				'<td style="width:30%;"><button type="button" value="1" id="2A" class="adder" style="margin:2px;">+</button><button type="button" class="miner">-</button></td></tr>'+
				'<tr style="height: 20px !important;"></tr>'+
				'<tr><th><input type="text" name="J3" placeholder="Jahr 3" maxlength=4></th><td></td></tr>'+
				'<tr><td><input type="text" name="B31" placeholder="Typ 1" maxlength=30></td><td><input type="text" name="C31" placeholder="Preis 1" maxlength=8></td>'+
				'<td style="width:30%;"><button type="button" value="1" id="3A" class="adder" style="margin:2px;">+</button><button type="button" class="miner">-</button></td></tr>';
				table.innerHTML = inner;
				document.getElementById("noPrices").style.display = "none";
				UpdateAdderAndMiner();
			}
			else
			{
				//Es sind Preisinformationen vorhanden
				//Konvertierung der Preisinformationen in Input Felder
				table = table.firstChild;
				var rI = 0; // rowIterator, der für das jeweilige Jahr die Anzahl der Spalten angibt
				var iter = 0; // Iterator für das jeweilige Jahr
				for (var i=0; i < table.childNodes.length;i++)
				{
					//Abfragen nach der Jahreszahl oder Inhalt
					if (table.childNodes[i].firstChild != null && table.childNodes[i].firstChild.firstChild != null && table.childNodes[i].firstChild.firstChild.nodeName == "H4")
					{
						iter +=1;
						rI = 0;
						var value = table.childNodes[i].firstChild.firstChild.innerHTML;
						var newNode = document.createElement("input");
						newNode.name = "J"+iter;
						newNode.type = "text";
						newNode.placeholder = "Jahr "+iter;						
						newNode.value = value.replace(" ","");
						newNode.maxLength = 4;
						table.childNodes[i].firstChild.replaceChild(newNode,table.childNodes[i].firstChild.firstChild);
					}else
					if (table.childNodes[i].childNodes.length == 2)
					{
						rI+=1;
						//Ersetzen des Bezeichners
						var value = table.childNodes[i].firstChild.innerHTML;
						table.childNodes[i].firstChild.innerHTML = "";
						var newNode = document.createElement("input");
						newNode.name = "B"+iter+rI;
						newNode.type = "text";
						newNode.value = value.replace(" ","");
						newNode.placeholder = "Typ "+rI;
						newNode.maxLength = 30;
						table.childNodes[i].firstChild.appendChild(newNode);
						newNode = null;
						//Ersetzen des Inhalts 
						value = table.childNodes[i].firstChild.nextSibling.innerHTML;
						table.childNodes[i].firstChild.nextSibling.innerHTML = "";
						newNode = document.createElement("input");
						newNode.name = "C"+iter+rI;
						newNode.type = "text";
						newNode.placeholder = "Preis "+rI;
						newNode.maxLength = 8;
						newNode.value = value.replace(" ","");
						table.childNodes[i].firstChild.nextSibling.appendChild(newNode);	
						//Falls gerade das erste Element für das Jahr iteriert wird, füge die Buttons hinzu
						if (rI == 1)
						{
							var buttonsNode = document.createElement("td");
							buttonsNode.innerHTML = '<button type="button" value="1" id="'+iter+'A" class="adder" style="margin:2px;">+</button><button type="button" class="miner">-</button>';
							buttonsNode.style = "width:30%;";
							table.childNodes[i].appendChild(buttonsNode);
						}
						else
						{
							//Setze den Value des Buttons um einen nach oben
							var button = document.getElementById(iter+"A");
							button.value = rI;
						}
							
					}else
					if (table.childNodes[i].firstChild != null && table.childNodes[i].childNodes.length == 1)
					{
						//Falls für eine Jahreszahl noch keine Preise angegeben wurden
						//Ersetzen durch das erste Eingabe Element
						var upperNode = document.createElement("td");
						var newNode = document.createElement("input");
						newNode.name = "B"+iter+rI;
						newNode.type = "text";
						newNode.maxlength = 30;
						newNode.placeholder = "Typ 1";
						upperNode.appendChild(newNode);
						table.childNodes[i].replaceChild(upperNode,table.childNodes[i].firstChild);
						
						//Füge den möglichen EingabeWert hinzu
						var upperNode2 = document.createElement("td");
						newNode = document.createElement("input");
						newNode.name = "C"+iter+rI;
						newNode.type = "text";
						newNode.placeholder = "Preis 1";
						newNode.maxlength = 8;
						upperNode2.appendChild(newNode);
						table.childNodes[i].appendChild(upperNode2);
						
						//Füge die Buttons hinzu
						var buttonsNode = document.createElement("td");
						buttonsNode.innerHTML = '<button type="button" value="1" id="'+iter+'A" class="adder" style="margin:2px;">+</button><button type="button" class="miner">-</button>';
						buttonsNode.style = "width:30%;";
						table.childNodes[i].appendChild(buttonsNode);
					}
				}
				//Falls nur 1 oder 2 Jahre angezeigt wurden, zeige noch die Eingabemglichkeiten für die kommenden Jahre an
				while (iter < 3)
				{
					iter += 1;
					//Einfügen des Jahres
					var newRow = document.createElement("tr");
					newRow.style = "height: 20px !important;";
					table.appendChild(newRow);
					var newRow2 = document.createElement("tr");
					newRow2.innerHTML = '<tr><th><input type="text" name="J'+iter+'" placeholder="Jahr '+iter+'" maxlength=4></th><td></td></tr>';
					table.appendChild(newRow2);
					newRow = document.createElement("tr");
					newRow.innerHTML = '<td><input type="text" name="B'+iter+'1" placeholder="Typ 1" maxlength=30></td><td><input type="text" name="C'+iter+'1" placeholder="Preis 1" maxlength=8></td>'+
					'<td style="width:30%;"><button type="button" value="1" id="'+iter+'A" class="adder" style="margin:2px;">+</button><button type="button" class="miner">-</button></td>';
					table.appendChild(newRow);
				}
				//Buttons mit der Klick Funktion ausstatten
				UpdateAdderAndMiner();
			}
			
		}
		else
			console.log("Preis Tabelle wurde nicht gefunden.");
	}
}
function UpdateAdderAndMiner()
{
	var clicker = document.getElementsByClassName("adder");
	for(i = 0; i < clicker.length;i++)
	{
		clicker[i].addEventListener('click',function(e){
		e = e || window.event;
		var target = e.target || e.srcElement;
		var locid = target.id;
		//Navigate to source select
		var source = target.parentNode.parentNode;
		var i = 0;
		i = parseInt(target.value);
		if (i < 5)
		{
			i++;
			target.value = i;
			var j= target.id.substring(0,1);
			var newNode = document.createElement("tr");
			newNode.innerHTML = '<tr><td><input type="text" name="B'+j+i+'" placeholder="Typ '+i+'" maxlength=30></td><td><input type="text" name="C'+j+i+'" placeholder="Preis '+i+'" maxlength=8></td>';
			var beforeInsertNode = target.parentNode.parentNode;
			for (var k= 0; k < i-1; k++)
				beforeInsertNode = beforeInsertNode.nextSibling;
			
			target.parentNode.parentNode.parentNode.insertBefore(newNode, beforeInsertNode);
		}
		//newNode.addEventListener("click", function(){changed = true});
	},false);
	}
	var clicker = document.getElementsByClassName("miner");
	for(i = 0; i < clicker.length;i++)
	{
		clicker[i].addEventListener('click',function(e){
		e = e || window.event;
		var target = e.target || e.srcElement;
		var locid = target.id;
		//Navigiere zuerst zum Adder Button, da bei diesem alle Infos abgelegt wurden
		target = target.previousSibling;
		var i = parseInt(target.value);
		//Überprüfung, ob noch mehr als ein Element vorhanden ist
		if (i > 1)
		{
			var source = target.parentNode.parentNode;
			for (var k= 0; k < i-1; k++)
				source = source.nextSibling;
			target.parentNode.parentNode.parentNode.removeChild(source);
			target.value = i-1;
		}
		
	},false);
	}	
}
</script>
</BODY>
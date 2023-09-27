<!DOCTYPE HTML>
<HTML>
<HEAD>
<TITLE>Suchen</TITLE>
<meta charset="UTF-8">
<LINK rel="stylesheet" href="\general.css">
<LINK rel="stylesheet" href="\colors.css">
<LINK rel="stylesheet" href="styles/Suchstyle.css">
<LINK rel="stylesheet" href="\Icons/css/all.css">
</HEAD>
<BODY>
<script>
function leave_site(){
	window.location.replace("../Index.php")
}
</script>
<div class="color" id="colorSuche">
<button class="HomeB" onclick="leave_site()">Home</button></br></br>
<h1> Suche </h1></br></br>
</div>
<div class="circle"><i style='font-size:50px;margin-top:35%;color:grey;' class='fas'>&#xf5a0;</i></div>

</br></br></br></br></br></br>

<div id="distanceParameters">
<table>
<tr>
<td>Startort</td>
<td id="SOrtTD"><input type="text" name="startort" id="startort" placeholder = "Stadt"></input>
</tr>
<tr>
<td>Maximale Distanz</td>
<td><input type="text" name ="maxDistO" id="maxDistO" min=0 placeholder="100"></td><td></input>km</td>
</tr>
</table>

</br></br></br>

</div>
<div id="errorArea"></div>
<div id="SButtons">
	<button id="startSearchU" type="button">Unterkunft suchen</button>
	<button id="startSearchS" type="button">Sportstätte suchen</button>
	<button id="startSearchB" type="button">Beides suchen</button>

</div>
<form name="searchDelegate" action="Unterkunftssuche.php" method="post" id="searchDelegate">
<input type="hidden" name="maxDist" id="maxDist">
<input type="hidden" name="startort" id="startortTarg">
<input type="hidden" name="coordinates" id="coordinates">
<input type="hidden" name="maxDistZwischen" id="maxDistZwischen" value="">
</form>
<script type="text/javascript">

document.getElementById("startSearchU").addEventListener('click',function(e){
	submitForm("Unterkunftssuche.php", "u");	
},false);
document.getElementById("startSearchS").addEventListener('click',function(e){
	submitForm("Sportartauswahl.php", "s");	
},false);
document.getElementById("startSearchB").addEventListener('click',function(e){
	submitForm("Unterkunftssuche.php", "b");	
},false);

//Schreibt die Koordinaten der Stadt in das Formular und ergänzt die Stadt um die Region
function getCoordinates(text)
{	
	//Formuliere die XML Anfrage an openrouteservice
	var request = new XMLHttpRequest();
	//Die API, der Schlüssel, welche die Benutzung des Services erlaubt.
	//Die API ist privat und auf mein Konto registriert
	const API = "<?php require("../APIKey.php"); echo GetAPIKey();?>";
	//Zusammenbauen der Anfrage
	var requestText = "https://api.openrouteservice.org/geocode/search?";
	//Hinzufügen des API-Codes; Muss immer geschehen, damit die Anfrage überhaupt bearbeitet wird
	requestText +='api_key='+API;
	//Hinzufügen der Eingabe des Suchfelds und die Beschränkung auf Deutschland
	//requestText += '&text='+text+'&boundary.country=DE';
	requestText += '&text='+text+'';
	requestText += '&layers=locality';
	request.open('GET', requestText);
	//Abfangen des Ergebnisses der Anfrage
	request.onreadystatechange = function () {
	if (this.readyState === 4) {
		console.log('Status:', this.status);
		console.log('Headers:', this.getAllResponseHeaders());
		if (this.status == 200)
		{
			//Umwandeln in ein Objekt
			let obj = JSON.parse(this.responseText);
			console.log(obj);
			//Wenn wir mehr als ein Ergebnis, sowie überhaupt eins haben, fahren wir fort
			var coord = document.getElementById("coordinates");		
			if (obj.features.length > 0 && this.status == 200)
			{
				//Überprüfe, ob es mehrere mögliche Treffer für die Eingabe existieren
				if ( obj.features.length > 1)
				{
					var tdEle = document.getElementById("SOrtTD");
					if (tdEle != null)
					{
						//Erstelle eine DropDown Auswahl 
						var inner = "<select name='startort' id='startort'>";
						var displayed = [];
						for (var i = 0; i < obj.features.length ;i++)
						{
							//Überprüfen auf Doppelungen
							var doppelt = 0;
							for(var j = 0; j< displayed.length;j++)
							{
								if (displayed[j] == String(obj.features[i].properties.name)+", "+String(obj.features[i].properties.region)+", "
									+String(obj.features[i].properties.country))
									{
										doppelt = 1;
										console.log(obj.features[j]);
									}
							}
							if (String(obj.features[i].geometry.coordinates).length > 15 && doppelt == 0)
							{
								inner+="<option value='"+String(obj.features[i].properties.name)+", "+String(obj.features[i].properties.region)
								+";"+String(obj.features[i].geometry.coordinates)
								+"' >"+ String(obj.features[i].properties.name)+", "+String(obj.features[i].properties.region)+", "
								+String(obj.features[i].properties.country)+"</option>";
								//Hinzufügen zu den bereits ausgegeben. Wird für die Überprüdung auf Doppelungen genutzt
								displayed.push(String(obj.features[i].properties.name)+", "+String(obj.features[i].properties.region)+", "
								+String(obj.features[i].properties.country));
							}
						}
						inner += "</select>";
						tdEle.innerHTML = inner;
						//Falls nur ein Element ausgegeben wurde und der Rest Doppelungen war, submitte das Formular
						if (displayed.length == 1)
						{
							submitForm(document.searchDelegate.action);
						}
						//Gib eine Fehlermeldung aus, dass mehrere Startorte gefunden wurden
						tdEle.firstChild.style = "border: 2px solid red;";
						var errorDisplay = document.getElementById("errorArea");
						errorDisplay.innerHTML = "<span class='error'> Es wurden mehrere Startorte zur Eingabe gefunden.</br> Bitte wählen Sie den passenden aus.						</span>";
						
					}
					else
						console.log("Fehler im Aufbau der Seite: SOrtTD nicht gefunden.");
				}
				else
				{
					//Ergänzen des Ortes um die Region
					var target = document.getElementById("startortTarg");
					if (target != null)
					{
						target.value = String(obj.features[0].properties.name);
						target.value +=", "+String(obj.features[0].properties.region);
					}
					//Hinzufügen der Koordinaten
					if (coord != null)
					{
						coord.value = String(obj.features[0].geometry.coordinates);
					}
					//Bestätige die Weiterleitung des Formulars
					document.searchDelegate.submit();
				}

			}
			else
			{
				console.log("Eingabe konnte nicht gefunden werden");
				var tdEle = document.getElementById("SOrtTD");
				tdEle.firstChild.style = "border: 2px solid red;";
				var errorDisplay = document.getElementById("errorArea");
				errorDisplay.innerHTML = "<span class='error'>Zu der Eingabe konnte keine passende Startstadt gefunden werden. </br> Bitte überprüfen Sie die Eingabe.</span>";

			}
		}
		else
		{
			alert("Fehler: Das minütige Anfragenlimit scheint erreicht oder es existiert keine Internetverbindung.<br> Bitte versuchen Sie es gleich noch einmal.");
		}
	  }
	};
	request.send();
}

function submitForm(url, typ)
{
	//Zurücksetzen von Fehlerumrandungen
	document.getElementById("maxDistO").style = "";
	document.getElementById("startort").style = "";
	
	//Anfragen auf Beides Suchen
	if (typ == "b")
	{
		document.getElementById("maxDistZwischen").value = "5";
	}
	else
	{
		document.getElementById("maxDistZwischen").value = "";
	}
	
	//Üerprüfung auf richtig ausgefüllte Felder
	document.searchDelegate.action = url;
	var target = document.getElementById("maxDist");
	var source = document.getElementById("maxDistO");
	cont = 1;
	if (source != null && target != null)
	{
		if (isNaN(source.value)  || parseFloat(source.value) <= 0 || source.value == "")
		{
			var errorDisplay = document.getElementById("errorArea");
			errorDisplay.innerHTML = "<span class='error'>Es wurde keine lesbare Distanz angegeben. </br> Bitte geben Sie die Entfernung als Ganzzahl (bspw. 100) an.</span>";
			document.getElementById("maxDistO").style = "border: 2px solid red;";			
			cont = 0;
			return;
		}
		target.value = source.value;
	}
	else
		alert("error in copying into empty script Distanz");
	target = null;
	source = null;
	target = document.getElementById("startortTarg");
	source = document.getElementById("startort");		
		if (source != null && target != null)
		{			
			//Überprüfung auf DropDown Menü
			console.log(source.nodeName);
			if (source.nodeName == "SELECT")
			{
				var val = source.value.split(";");
				console.log(val);
				if (val.length == 2)
				{
					target.value = val[0];
					document.getElementById("coordinates").value = val[1];
					//Bestätige die Weiterleitung des Formulars
					document.searchDelegate.submit();
				}
				
			}
			else
			{
				//Überprüfung ob überhaupt eine Eingabe im Startort ist
				if (source.value == "" || source.value.length <=1)
				{
					var errorDisplay = document.getElementById("errorArea");
					errorDisplay.innerHTML = "<span class='error'>Es wurde keine Stadt angegeben. </br> Bitte geben Sie eine Stadt zum Starten der Umkreissuche an.</span>";
					document.getElementById("startort").style = "border: 2px solid red;";	
				}
				else
				{
					target.value = source.value;
					//Hinzufügen der Geocodierung des Standorts	
					//Bestätigung des Formulars geschieht innerhalb der getCoordinates Funktion
					getCoordinates(source.value);	
				}
			}								
		}
	else
		alert("error in copying into empty script");
	
	//if (cont == 1)
	//document.searchDelegate.submit();
	//-- Geschieht über die Funktion getCoordinates
}


</script>
</BODY>
</HTML>
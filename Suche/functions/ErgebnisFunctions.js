var zuruckButton = document.getElementById("zuruck");

if (zuruckButton != null)
{
	zuruckButton.addEventListener("click",function()
	{
		var formular = document.getElementById("backDelegate");
		if (formular != null)
		{
			formular.submit();
		}
	});
}

//Globale Definition der Objekte für die Liste
/*
* Die Unterkünfte in die einzelnen Kategorien geordnet
* Element des Arrays ist ein Objekt aus Inhalt, coords, id, zeit und categorie
*/
var catsU =  [[],[],[],[],[]];
var catsS = [[],[],[],[],[]];
var catsUS = new Array();
var catsSU = new Array();

/*
* Stellt die Ergebnisse der Suche in der Liste dar
* groupedByU stellt bei einer Suche nach beidem da, ob die Ergebnisse nach der Unterkunft gruppiert sein sollen
*/
function displayListElements(groupedByU)
{
	var notEmptyU = false;
	var notEmptyS = false;
	var liste = document.getElementById("listContent");
	for(var i = 0; i < catsU.length && !(notEmptyS && notEmptyU);i++)
	{
		if (catsS[i].length > 0)
			notEmptyS = true
		if (catsU[i].length > 0)
			notEmptyU = true;		
	}
	
	if (notEmptyS && notEmptyU)
	{
		//Leeren der Anzeige
		liste.innerHTML = "";
		//Wir haben in beiden Arrays etwas enthalten, also wir haben nach beidem gesucht
		if(groupedByU)
		{
			//Ergebnisse sind nach Unterkunft gruppiert
			//Gehe jedes Element aus catsU durch um die Ordnung zu erhalten
			for(var i=0; i < catsU.length; i++)
			{
				for(var j = 0; j < catsU[i].length;j++)
				{
					//Suche die ID der Unterkunft innerhalb des catsUS Array
					var found = false;
					var k=0;
					for(k=0; k < catsUS.length && !found;k++)
					{
						if (catsUS[k][5] == catsU[i][j].id)
							found = true;							
					}
					k=k-1;
					//Gib die Unterkunft und alle dazugehörigen Sportstaetten aus
					liste.innerHTML += catsU[i][j].inhalt;
					var content = "<div style='display: table; width:100%;margin: 0;'>";
					var lastL =-1;
					var lastM = -1;
					for(var l=0;l < catsUS[k].length-1;l++)
					{						
						//Gib die Sportstätten aus							
						for(var m=0; m < catsUS[k][l].length;m++)
						{
							if ((lastM != m || lastL != l) && (lastM != -1 && lastL != -1))
								content +="<div style='display: table-row'><div style='width:5%; display: table-cell; background-image:url(../Marker/gruppiererWeiter.png); background-size: 100% 100%'></div>"+catsUS[k][lastL][lastM].inhalt+"</div>";
							lastM = m;
							lastL = l;
						}							
					}
					content +="<div style='display: table-row'><div style='width:5%; display: table-cell; background-image:url(../Marker/gruppiererEnde.png); background-size: 100% 100%'></div>"+catsUS[k][lastL][lastM].inhalt+"</div>";
					content +="</div>";
					liste.innerHTML += content;
				}
			}
		}
		else
		{
			//Überprüfen, ob ZuordnungSU bereits erstellt wurde. Falls sie das nicht wurde, wird sie erstellt
			if (catsSU.length == 0)
			{
				//Sprichwörtliches Tauschen von US und SU
				//Gehe jede Unterkunft durch
				for(var i=0; i < catsUS.length;i++)
				{
					for (var n= 0; n < catsUS[i].length-1; n++)
					{
						//Gehe jede Sportstätte durch
						for(var j=0; j < catsUS[i][n].length;j++)
						{
							//Suche, ob die aktuelle Sportstätte schon vorhanden ist
							var found = false;
							for(var k=0; k < catsSU.length && !found; k++)
							{
								// Bereits vorhanden
								if (catsUS[i][n][j].id == catsSU[k][5].id)
								{
									found = true;
									//Ordne die Unterkunft der Sportstaette zu
									//Suche die Unterkunft im catsU array 
									var founder2 = false;
									for (var l = 0; l < catsU.length && !founder2;l++)
									{
										for (var m = 0; m < catsU[l].length;m++)
										{
											if (catsU[l][m].id == catsUS[i][5])
											{
												founder2 = true;
												catsSU[k][catsU[l][m].cat].push(JSON.parse(JSON.stringify(catsU[l][m])));
												//Ändere die Zeiten
												catsSU[k][catsU[l][m].cat][catsSU[k][catsU[l][m].cat].length-1].zeit = catsUS[i][n][j].zeit;
												catsSU[k][catsU[l][m].cat][catsSU[k][catsU[l][m].cat].length-1].inhalt = SwapTimeAndDist(catsUS[i][n][j].inhalt,catsSU[k][catsU[l][m].cat][catsSU[k][catsU[l][m].cat].length-1].inhalt);
											}
										}
									}
								}
							}
							//Sportstaette ist noch nicht vorhanden und muss hinzugefügt werden
							if (!found)
							{
								var adder = [[],[],[],[],[],JSON.parse(JSON.stringify(catsUS[i][n][j]))];								
								//Ordne die Unterkunft der Sportstaette zu
								//Suche die Unterkunft im catsU array 
								var founder2 = false;
								for (var l = 0; l < catsU.length && !founder2;l++)
								{
									for (var m = 0; m < catsU[l].length;m++)
									{
										if (catsU[l][m].id == catsUS[i][5])
										{
											founder2 = true;
											adder[catsU[l][m].cat].push(JSON.parse(JSON.stringify(catsU[l][m])));
											//Ändere die Zeiten
											var zwischenZeit = catsU[l][m].zeit;
											var zwischenInhalt = catsU[l][m].inhalt;
											//console.log(catsU[l][m]);
											adder[catsU[l][m].cat][adder[catsU[l][m].cat].length-1].zeit = catsUS[i][n][j].zeit;
											adder[catsU[l][m].cat][adder[catsU[l][m].cat].length-1].inhalt = SwapTimeAndDist(catsUS[i][n][j].inhalt,adder[catsU[l][m].cat][adder[catsU[l][m].cat].length-1].inhalt);
											//Ordne die Zeit der Unterkunft der neuen Sportstaette zu
											adder[5].zeit = zwischenZeit;
											adder[5].inhalt = SwapTimeAndDist(zwischenInhalt, adder[5].inhalt);
											//console.log("Ordne "+adder[5].id+" folgende Zeit zu "+zwischenZeit);
										}
									}
								}
								//console.log("adder");
								//console.log(adder);
								catsSU.push(adder);
							}
						}
					}					
				}									
			}
			
			//Ergebnisse sind nach Unterkunft gruppiert
			//Gehe jedes Element aus catsU durch um die Ordnung zu erhalten
			var alreadyAdded = new Array();
			for(var i=0; i < catsS.length; i++)
			{
				for(var j = 0; j < catsS[i].length;j++)
				{
					if (alreadyAdded.indexOf(catsS[i][j].id) == -1)
					{
						//Suche die ID der Unterkunft innerhalb des catsUS Array
						var found = false;
						var k=0;
						for(k=0; k < catsSU.length && !found;k++)
						{
							if (catsSU[k][5].id == catsS[i][j].id)
								found = true;							
						}
						k=k-1;
						//Gib die Unterkunft und alle dazugehörigen Sportstaetten aus
						liste.innerHTML += catsSU[k][5].inhalt;
						alreadyAdded.push(catsS[i][j].id);
						var content = "<div style='display: table; width:100%;margin: 0;'>";
						var lastL =-1;
						var lastM = -1;
						for(var l=0;l < catsSU[k].length-1;l++)
						{						
							//Gib die Sportstätten aus
							for(var m=0; m < catsSU[k][l].length;m++)
							{
								if ((lastM != m || lastL != l) && (lastM != -1 && lastL != -1))
									content +="<div style='display: table-row'><div style='width:5%; display: table-cell; background-image:url(../Marker/gruppiererWeiter.png); background-size: 100% 100%'></div>"+catsSU[k][lastL][lastM].inhalt+"</div>";
								lastM = m;
								lastL = l;
							}								
						}	
						content +="<div style='display: table-row'><div style='width:5%; display: table-cell; background-image:url(../Marker/gruppiererEnde.png); background-size: 100% 100%'></div>"+catsSU[k][lastL][lastM].inhalt+"</div>";						
						content +="</div>";
						liste.innerHTML += content;
					}
				}
			}
			
		}
	}
	else if (notEmptyS || notEmptyU)
	{
		//Nach einem von beidem wurde gesucht
		var shower = catsU;
		if (notEmptyS)
			shower = catsS;
		for(var i=0; i < shower.length;i++)
		{
			for(var j=0; j < shower[i].length;j++)				
				list.innerHTML += shower[i][j].inhalt;
		}
	}
	
	changeIcons();
}

//Funktion für das Klicken auf einen Marker
function markerClicked(e)
{
	//console.log(e);
	//Suche den Marker 
	var found = -1;
	//console.log(markers);
	for (var i = 0; i < markers.length && found==-1;i++)
	{
		if (String(markers[i]._latlng) == String(e.latlng))
		{
			found = i;			
		}
	}
	//Der Marker wurde wiedergefunden
	if (found != -1)
	{
		var ergebnisse = document.getElementsByClassName("Ergebnis");
		for (var i = 0; i < ergebnisse.length; i++)
		{
			ergebnisse[i].style = "";
		}
		var target = document.getElementById(results[found].id);
		window.location.hash = results[found].id;
		target.style = "background-color: #3c3c3c;color: #F8F8FF;";
	}
}

//Laden der Distanzinformationen und dynamisches Nachladen der Seite
var errorReported = false;
var ids = JSON.parse(document.getElementById("IIDs").value);
var results = [];
var markers = [];
var resultsBoth = []; //Wird für die Behandlung von der Suche nach beidem benötigt
var beides = false;
if (document.getElementById("finalIDsFirst") != null)
{
	beides = true;
}
if (ids == null || document.getElementById("IIDs").value == "[]" || document.getElementById("IIDs").value == "[[],[],[],[],[]]")
{
	console.log("error: No Element IIDs found!");
	var loader = document.getElementById("loader");
	loader.style.display = "none";
	var cont = document.getElementById("listContent");
	cont.innerHTML ="<p>Leider konnte zu den eingegebenen Attributen kein passendes Objekt gefunden werden.</p>";
}
else
{
	var loader = document.getElementById("loader");
	var pb = document.getElementById("progressbar");
	var map_list = document.getElementById("map_list");
	var ZuordnungUzuSElement = document.getElementById("ZuordnungUzuS");
	var finalIDsFirstElement = document.getElementById("finalIDsFirst");
	//Abfangen und gesonderte Behandlung, wenn nach beidem gesucht wird
	if (ZuordnungUzuSElement != null && finalIDsFirstElement != null)
	{
		//Es wurde nach beidem gesucht
		var ZuordUS = JSON.parse(ZuordnungUzuSElement.value);
		var idsFirst = JSON.parse(finalIDsFirstElement.value);
		var counterAll = Object.keys(ZuordUS).length;
		var counterUnter = Object.keys(ZuordUS).length;
		var maxCounter = Object.keys(ZuordUS).length;
		var lastSent = false;
		var tempResultsBoth = []; //Wird gebraucht um die Ergebnisse zwischen zu lagern Schlüssel => Array ; Ähnlich zu ZuordnungUzuS
		//Durchlaufen aller Unterkünfte und den zugehörigen Sportstätten
		//console.log(counterAll);
		for(var index in ZuordUS)
		{			
			value = ZuordUS[index];
			//Zunächst formulieren einer Anfrage für die Unterkunft
			var request = new XMLHttpRequest();
			request.onreadystatechange=function() 
			{
				var cont=false; //Einfügen, um Verschachtelung geringer zu halten
				if (this.readyState==4 && this.status==200) 
				{
					//Anfrage nach der Unterkunft ist zurück gekommen
					counterAll--;
					counterUnter--;
					//Anpassen des Ladebalkens
					var fortschritt = 100- (100/(maxCounter+1) * (counterUnter))-1;
					pb.style.width = fortschritt+"%";
					pb.innerHTML = parseFloat(fortschritt).toFixed(2)+"%";
					//console.log("-1 bei Anfrage Unterkunft");
					cont=true;
					//Überprüfe, ob Antwort valid war
					if (this.responseText == "0" || this.responseText == "c")
					{
						console.log("Antwort auf Unterkunft: Zu weit entfernt");
						console.log(this.responseText);
						cont=false;
						//Abziehen der Anzahl der Sportarten vom Gesamtcounter
						//console.log(sportstaetten.length+" sollte hinzugefügt werden zu"+counterAll);
						//TODO? Falls Distanz nicht stimmt. Zwischenspeichern?
					}
				}
				if (cont)
				{
					//Unterkunft passt, also überprüfen der umliegenden Sportstätten
					//Sportstätten wurden über den responseText als Eigenschaft cat übermittelt
					var result = JSON.parse(this.responseText);
					//Hinzufügen der Unterkunft in das tempResultsBoth Array
					tempResultsBoth.push({Unterkunft:result,Sportstaetten: []});
					//console.log(result);
					var sportstaetten = JSON.parse(result.FID);
					//Hinzufügen zum Counter
					counterAll += sportstaetten.length;
					//console.log("+"+sportstaetten.length+" starten der Anfragen für die Sportstaetten");
					for(var k=0;k<sportstaetten.length;k++)
					{
						var requestInner = new XMLHttpRequest();
						requestInner.onreadystatechange=function() 
						{							
							//Ergebnis der Sportstaette liegt vor
							if (this.readyState==4 && this.status==200) 
							{
								//Verringern des Counters, da eine Anfrage eingegangen ist
								counterAll--;
								//console.log("-1 bei Anfrage Sportstaette");
								if (this.responseText == "0" || this.responseText == "c")
								{
									//Ergebnis wurde nicht angenommen
								}
								else
								{
									//Ergebnis wurde angenommen
									//Suche die Unterkunft, der es hinzugefügt werden soll
									var tries = 3;
									var iterator =0;
									try {
										var sportstaetteLocal = JSON.parse(this.responseText);
										
										//console.log(tempResultsBoth[iterator].Unterkunft.id +" zu "+ sportstaetteLocal.cat);
										//console.log(sportstaetteLocal);
										while(tempResultsBoth[iterator].Unterkunft.id != sportstaetteLocal.FID && tries>=0)
										{
											iterator++;
											if (iterator >= tempResultsBoth.length)
											{
												iterator=0;
												tries--;
											}
											
										}
										if (tries >= 0)
										{
											tempResultsBoth[iterator].Sportstaetten.push(sportstaetteLocal);
										}	
									}	
									catch(err)
									{
										if (!errorReported)
										{
											errorReported = true;
											alert("Abragelimit schein erreicht. Bitte einmal kurz warten.");
										}
										
										console.log(this.responseText);
									}
								}
								//console.log(tempResultsBoth);
								//console.log(counterAll);
								//Überprüfe ob alle Anfragen behandelt wurden
								if (counterAll == 0)
								{
									/*
									* Wir haben im Ergebnis nun ein Array (tempResultsBoth) vorliegen in welchem die Unterkünfte mit ihrere Response
									* und den dazugehörigen Sportstaetten mit jeweils ihrere Response vorliegen
									*/
									
									//Aussortieren der Unterkünfte mit keiner Sportstaette in Laufdistanz --> Nur behandeln der Unterkünfte mit Sportstaetten
									var cont = document.getElementById("listContent");
									for(var l=0;l< tempResultsBoth.length;l++)
									{
										if(tempResultsBoth[l].Sportstaetten.length > 0)
										{
											//Ausgabe derer in der Liste
											//Ausgabe der Unterkunft in der Liste
											//cont.innerHTML+= tempResultsBoth[l].Unterkunft.inhalt;
											//Zwischenspeichern im Array für spätere geordnete Ausgabe
											catsU[tempResultsBoth[l].Unterkunft.cat].push(JSON.parse(JSON.stringify(tempResultsBoth[l].Unterkunft)));
											catsUS.push([[],[],[],[],[],JSON.parse(JSON.stringify(tempResultsBoth[l].Unterkunft.id))]);
											//Ausgabe der Sportstaetten in der Liste
											for(var m=0;m<tempResultsBoth[l].Sportstaetten.length;m++)
											{
												//cont.innerHTML+= tempResultsBoth[l].Sportstaetten[m].inhalt;
												catsS[tempResultsBoth[l].Sportstaetten[m].cat].push(JSON.parse(JSON.stringify(tempResultsBoth[l].Sportstaetten[m])));
												catsUS[catsUS.length-1][tempResultsBoth[l].Sportstaetten[m].cat].push(JSON.parse(JSON.stringify(tempResultsBoth[l].Sportstaetten[m])));
												
											}
											resultsBoth.push(tempResultsBoth[l]);
										}
									}	
									displayListElements(true);
									var radios = document.getElementsByName("group");
									radios[0].checked = "checked";
									
									//Darstellen auf der Karte
									for(var l=0; l< resultsBoth.length;l++)
									{
										//Zuerst die Unterkunft
										var splitter = resultsBoth[l].Unterkunft.coords.split(',');
										if (splitter.length != 2)
											console.log("error"+splitter);
										var categorie = 0;
										categorie = resultsBoth[l].Unterkunft.cat;
										var marker = L.marker([splitter[1],splitter[0]],{icon: GetIcon(categorie,"U")}).addTo(map);
										results.push(resultsBoth[l].Unterkunft);
										markers.push(marker);
										markers[markers.length - 1].on('click',markerClicked);
										//Einfügen der Sportstaetten
										for(var o=0; o < resultsBoth[l].Sportstaetten.length;o++)
										{
											var splitter = resultsBoth[l].Sportstaetten[o].coords.split(',');
											if (splitter.length != 2)
												console.log("error"+splitter);
											categorie = resultsBoth[l].Sportstaetten[o].cat;
											var marker = L.marker([splitter[1],splitter[0]],{icon: GetIcon(categorie,"S")}).addTo(map);
											results.push(resultsBoth[l].Sportstaetten[o]);
											markers.push(marker);
											markers[markers.length - 1].on('click',markerClicked);
										}
									}									
									//Ändern der Sichtbarkeit des Ladebalkens
									loader.style.display = "none";
								}								
							}
						}
						//Suche die Unterkunft im ids Array
						var foundIt = false;
						var categorie=0;
						for(categorie=0; categorie <ids.length && !foundIt;categorie++)
						{
							for(n=0;n < ids[categorie].length && !foundIt;n++)
							{					
								if (ids[categorie][n] == index)
								{
									foundIt = true;
								}
							}
						}
						var requestText = 'id='+sportstaetten[k]+
							'&maxDist='+document.getElementsByName("maxDistZwischen")[0].value+
							'&typ=Sportstaette'+
							'&coords='+result.coords+
							'&FID='+result.id+ //überträgt die ID der Unterkunft
							'&cat='+(categorie-1)+ 
							'&overwriteDriving=true';//Überschreiben der Art der Berechnung --> wird dann intern in Laufen übersetzt
						requestInner.open('GET','SuchergebnisseNachladen.php?'+requestText);
						requestInner.send();
					}					
				}				
			}
			//Suche die Unterkunft im idsFirst Array
			var foundIt = false;
			var categorie=0;
			for(categorie=0; categorie <idsFirst.length && !foundIt;categorie++)
			{
				for(n=0;n < idsFirst[categorie].length && !foundIt;n++)
				{					
					if (idsFirst[categorie][n] == index)
					{
						foundIt = true;
					}
				}
			}
			var requestText = 'id='+index+
				'&maxDist='+document.getElementsByName("maxDist")[0].value+ //Einsetzen der Distanz, die zwischen den Objekten liegen darf
				'&typ=Unterkunft'+
				'&coords='+document.getElementsByName("coordinates")[0].value+ //Einsetzen der Koordinaten der Unterkunft
				'&FID='+JSON.stringify(value)+ //soll die passenden Sportstätten transportieren	
				'&cat='+(categorie-1); 		
			request.open('GET','SuchergebnisseNachladen.php?'+requestText);
			request.send();
			counter++;
			if (counterUnter==0) lastSent=1;
		}
		
		if (!beides && results.length == 0)
		{
			var cont = document.getElementById("listContent");
			cont.innerHTML = "<p>Leider konnten zu den angegebenen Daten keine Übereinstimmung gefunden werden.</p>";
			//Ändern der Sichtbarkeit des Ladebalkens
			loader.style.display = "none";
		}
		else
			console.log("Wrong");
	}
	else
	{
		//Sende für jede ID eine Anfrage mit der Distanz an die externe Datei
		var counter = 0; //Zählt die ausgehenden und wieder eingegangenen Anfragen
		var counterAll =ids[0].length; //Wird subtrahiert auf 0, sodass alle Anfragen geschickt werden
		if (ids.length > 1)
			counterAll +=ids[1].length+ids[2].length+ids[3].length+ids[4].length;
		var maxCounter = counterAll; //Wird als Referenz für den Ladebalken benötigt
		var lastSent = 0; //Wahrheitswert, der sagt, ob die letzte Anfrage geschickt wurde
		//console.log(ids);
		for(var k=0;k <ids.length;k++ )
		{
			for(var j=0; j <ids[k].length;j++)
			{
				var id = ids[k][j];
				var request = new XMLHttpRequest();
				request.onreadystatechange=function() 
				{
					if (this.readyState==4 && this.status==200) 
					{
						//Überprüfe, ob Antwort valid war
						if (this.responseText == "0" || this.responseText == "c")
						{
							console.log(this.responseText);
							//TODO? Falls Distanz nicht stimmt. Zwischenspeichern?
							counter--;
						}
						else
						{
							//Antwort war richtig und die Infos werden als JSON übermittelt
							
							try 
							{
								results.push(JSON.parse(this.responseText));							
							}
							catch( Exception)
							{
								console.log(Exception);
								console.log(this);
							}
							counter--;
						}
					}
					//Update die Progressbar
					var fortschritt = 100- (100/maxCounter * (counterAll));
					pb.style.width = fortschritt+"%";
					pb.innerHTML = parseFloat(fortschritt).toFixed(2)+"%";
					if (counter==0 && lastSent ==1)
					{
						//Das Ende ist nah
						//TODO??: Ordnen
						//Ändern der Sichtbarkeit der einzelnen Teile
						loader.style.display = "none";
						
						var cont = document.getElementById("listContent");	//fuer Liste geaendert
						for (var t=0; t < results.length;t++)
						{
							var typ = "U";
							if (document.getElementById("typ").value == "Sportstaette") typ ="S";
							//Darstellen des Inhalts in der Liste
							//cont.innerHTML = cont.innerHTML+results[t].inhalt;
							//Zwischenspeichern im Array für späteres geordnetes Ausgeben
							if (typ =="U")
								catsU[results[t].cat].push(results[t]);
							else
								catsS[results[t].cat].push(results[t]);
							//Darstellen der Marker auf der Karte
							var splitter = results[t].coords.split(',');
							if (splitter.length != 2)
								console.log("error"+splitter);
							else
							{								
								var icon = GetIcon(results[t].cat,typ);
								var marker = L.marker([splitter[1],splitter[0]],{icon: icon}).addTo(map);
								markers.push(marker);
								markers[markers.length - 1].on('click',markerClicked);						
								//console.log(markers);
							}							
						}
						//Darstellen der Elemente in einer geordneten Liste
							displayListElements(false);
						if (results.length == 0)
						{
							cont.innerHTML = "<p>Leider konnten zu den angegebenen Daten keine Übereinstimmung gefunden werden.</p>";
						}
					}
				}
				var requestText = 'id='+id+
				'&maxDist='+document.getElementsByName("maxDist")[0].value+
				'&typ='+document.getElementById("typ").value+
				'&coords='+document.getElementsByName("coordinates")[0].value+
				'&cat='+k; //cat soll die jeweilige Abstufung wieder zurück liefern, damit eine Einordnung geschehene kann
				//console.log(requestText);
				request.open('GET','SuchergebnisseNachladen.php?'+requestText);
				request.send();
				counter++;
				counterAll--;
				if (counterAll==0) lastSent=1;
			}
		}
	}
}

/* 
* passe am Anfang die Farben der icons an die der Marker an
*/
function changeIcons(){
	var UnterColors = ["#e9977b", "#eca78f", "#f1bcaa", "#f3c6b7", "#f6dad1"];
	var SportColors = ["#5e9fa1", "#6fa9ab", "#90bcbd", "#a8cacb", "#c8ddde"];

	var listErgebnisse = document.getElementsByClassName("Ergebnis");
	console.log(listErgebnisse);
	for(var i=0; i < listErgebnisse.length; i++)
	{
		// Hole Objekt und Id fuer Art
		var curr = listErgebnisse[i];
		var art = curr.id.charAt(0);
		var farbe = null;
		
		//Suche Objekt in catsS/catsU, um Art und Farbe zu erfahren
		if(art == "u"){
			for(var j=0; j < catsU.length; j++){
				for(var k=0; k < catsU[j].length; k++){
					if(catsU[j][k].id == curr.id){
						farbe = j;
						break;
					}
				}
				if(farbe != null){
					break;
				}
			}
		}else{
			for(var j=0; j < catsS.length; j++){
				for(var k=0; k < catsS[j].length; k++){
					if(catsS[j][k].id == curr.id){
						farbe = j;
						break;
					}
				}
				if(farbe != null){
					break;
				}
			}
		}	
		
		//Hole Icon und aendere dessen Farbe
		if(art == "u")
			curr.firstChild.style.color = UnterColors[farbe];
		else	
			curr.firstChild.style.color = SportColors[farbe];
	}
}

var tempMarker = null;
function TurnMarkerOn(coords)
{	
	var splitter = coords.split(',');
	if (splitter.length == 2)
	{
		//Erzeuge einen temporären Marker an der Position mit einer größeren Größe
		for (var i = 0; i < markers.length; i++)
		{
			markers[i].setOpacity(0.3);	
			
			if (markers[i].getLatLng().equals(L.latLng(splitter[1],splitter[0])))
			{
				markers[i].setOpacity(1);
			}
		}
	}

}
function TurnMarkerOff(coords)
{
	for (var i = 0; i < markers.length; i++)
	{
		markers[i].setOpacity(1);		
	}
}

function GetIcon(categorie, typ)
{
	return L.icon({
	iconUrl: '../Marker/marker'+typ+categorie+'.png',
	shadowUrl: 'leaflet/images/marker-shadow.png',
	iconSize:     [25, 35], // size of the icon
	shadowSize:   [41, 41], // size of the shadow
	iconAnchor:   [12, 41] // point of the icon which will correspond to marker's location	
	});

}

function SwapTimeAndDist(source, target)
{														
	var entfernungNeu = "";
	var zeitNeu ="";
	var stunden = "0";
	var minuten = "0";
	//Hole dir die getauschte Entfernung 
	//console.log(catsUS[k][i][l].inhalt.split("<")[10]);
	zeitNeu = source.split("<")[10].split(">")[1];
	var minutensplitter = zeitNeu.split("h");
	if (minutensplitter.length > 1)
	{
		stunden = minutensplitter[0].trim();
		minuten = minutensplitter[1].split("min")[0].trim();
	}
	else
		minuten = zeitNeu.split("min")[0].trim();
	entfernungNeu = source.split("<")[8].split(">")[1].trim();
	//console.log("Minuten "+minuten);
	//console.log("stunden "+stunden);
	//console.log("entfern "+entfernungNeu.split("km")[0]);
	var neuerInhalt = "<";
	var funktionenSplitter = target.split("<")[1].split("(");
	//console.log(funktionenSplitter);
	neuerInhalt += funktionenSplitter[0].split("=")[0]+"="+funktionenSplitter[0].split("=")[1]+"="+funktionenSplitter[0].split("=")[2]+"="+funktionenSplitter[0].split("=")[3]+"(";
	neuerInhalt += funktionenSplitter[1].split(")")[0].split(",")[0];
	neuerInhalt += ",'"+entfernungNeu.split("km")[0].trim()+"','"+stunden+"_"+minuten+"')";
	//console.log(neuerInhalt);
	neuerInhalt += funktionenSplitter[1].split(")")[1];
	for (var o=2; o < funktionenSplitter.length;o++)		
		neuerInhalt += "("+funktionenSplitter[o];
	neuerInhalt += "<";
	for (var o=2; o < 8;o++)
	{
		neuerInhalt+=target.split("<")[o]+"<";
	}										
	neuerInhalt+=
	target.split("<")[8].split(">")[0]+">";
	//console.log(neuerInhalt);
	neuerInhalt += entfernungNeu;
	//console.log(neuerInhalt);
	neuerInhalt+="</div><div class='ErgebnisZeit'>"+zeitNeu+"</div></div> ";

	return neuerInhalt;
}
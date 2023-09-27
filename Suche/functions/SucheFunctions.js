/**-----------------Funktionen für die Objektbehandlung----------------------*/
/*
* Generelle Idee:
* - Stelle die einzelnen Suchelemente als ein Array von Objekten dar
* - die einzelnen Objekte beinhalten: Typ, Attributname, ein Array der ausgewählten Werte
* - die Identifikation der einzelnen Attribute und der Kommunikation von checkbox mit der Auswahlbox soll über den Attributnamen geschehen
* - der SessionStorage "content" wird zur globalen Speicherung genutzt
* - das Objekt wird mit JSON geparsed
*/

//Auszuführen beim Laden des Skriptes
RefreshSelectedArea();
HideAllStorageElements();

/**Constructor*/
function Attribute(typ,name,val)
{
	this.typ = typ;
	this.name = name;
	this.val = [val];
	
}

/**Fügt ein neues Element der globalen Variable Content im SessionStorage hinzu
* Falls das Element bereits vorhanden ist, wird der Value ergänzt
*/
function AddAttribute(name, typ, value)
{
	//Lese den bisherigen Storage aus
	var readContent = sessionStorage.getItem("content");
	var content = null;
	
	if (readContent == null)
	{
		//bisher ist noch kein Element im Storage vorhanden
		content = [new Attribute(typ,name,value)];
	}
	else
	{
		//Es existieren bereits Elemente - Lese diese aus und wandle sie in ein Objekt um
		content = JSON.parse(readContent);
		//Suche das Element im aktuellen content | mehrere Werte können nur bei charcoice auftreten
		if (typ == "charc")
		{
			var found = 0;
			for(var i = 0; i < content.length;i++)
			{
				if(content[i].name == name)
				{
					//Füge den Wert dem Array hinzu
					content[i].val.push(value);
					found = 1;
				}
			}
			if (found == 0)
				content.push(new Attribute(typ,name,value));
		}
		else
			content.push(new Attribute(typ,name,value));
	}
	//Transformiere das geschriebene Objekt wieder in das String Format und lege es im sessionStorage ab
	sessionStorage.setItem("content",JSON.stringify(content));
	//Aktualisiere die Anzeige
	RefreshSelectedArea();
	
}

/**Entfernt ein Element aus dem globalen sessionStorage content
* Falls mehrere Werte existieren, wird der angegebene Wert gelöscht
*/
function RemoveAttribute(name,value)
{
	//Versuche die Elemente aus dem sessionStorage zu lesen
	var readContent = sessionStorage.getItem("content");
	if (readContent != null)
	{
		var content = JSON.parse(readContent);
		
		//Suche das angegebene Element
		for(var i = 0; i< content.length;i++)
		{
			if (content[i].name == name)
			{
				if (content[i].typ == "charc")
				{
					//Bestehen mehr als zwei Werte? - Also eine Oder Verknüpfung?
					if(content[i].val.length > 1)
					{
						//Entferne den Wert
						for(var j=0; j < content[i].val.length;j++)
						{
							//Das zu löschende Element wurde gefunden
							if(content[i].val[j] == value)
							{
								//Entfernt das Element an der i-ten Position und shifted den Rest
								content[i].val.splice(j,1);	
								j = content[i].val.length+1;
							}
						}
					}
					else
					{
						//Löschen des gesamten Elements aus dem Array
						content.splice(i,1);
					}
				}
				else
				{
					//Löschen des gesamten Elements aus dem Array
					content.splice(i,1);
				}
				//Abbruch der for Schleife
				i = content.length+1;
			}
		}
		
		//Schreibe den veränderten Content zurück in den globalen Speicher
		sessionStorage.setItem("content",JSON.stringify(content));
		//Aktualisiere die Anzeige
		RefreshSelectedArea();
	}	
}

/**Stellt ein Abbild aus dem aktuellen sessionStorage content in der Auswahlbox dar
*/
function RefreshSelectedArea()
{
	//Hole den content und das Feld
	var readContent = sessionStorage.getItem("content");
	var target = document.getElementById("ausKritAnz");
	if (readContent != null && target != null)
	{
		var content = JSON.parse(readContent);
		//Da Startort und Distanz gesondert behandelt werden, müssen diese zunächst auch gesondert gespeichert werden
		var out = sessionStorage.getItem("ortDist");
		//Falls die Variable im sessionStorage noch nicht gesetzt ist, so wurde noch kein Element bereits hinzugefügt.
		//Damit kann einfach der erzeugte Quellcode für SOrt und DIstanz gespeichert werden
		if (out == null)
		{
			out = target.innerHTML;
			sessionStorage.setItem("ortDist",out);
		}
		
		//Nun wird der content in die einzelnen Variablen überführt
		for(var i = 0; i < content.length;i++)
		{
			switch(content[i].typ)
			{
				case "charc":
				//Charchoice kann mehrere Werte haben
				if (content[i].val.length == 1)
				out += "<div class='tagEl' id='tagDiv"+content[i].name+"'> "+content[i].name+" : "+content[i].val[0]
					+"<button class='deleteTag' id='dT"+content[i].name+"_"+content[i].val[0]+"'>&#10006</button></div>";
				else
				{
					//Mehrere Werte
					//Die Identifikation geschieht durch eine zusammengesetzte ID mit name und Wert durch _ getrennt
					out +="<div class='orGroup'>";
					for(var j = 0; j < content[i].val.length;j++)
					{						
						out += "<div class='tagEl' id='tagDiv"+content[i].name+"_"+content[i].val[j]+"'> "+content[i].name+" : "+content[i].val[j]
							+"<button class='deleteTag' id='dT"+content[i].name+"_"+content[i].val[j]+"'>&#10006</button></div>";						
					}
					out +="</div>";
				}
				break;
				case "int":
				out +="<div class='tagEl' id='tagDiv"+content[i].name+"'> "+content[i].name+" "+content[i].val[0]
					+"<button class='deleteTag' id='dT"+content[i].name+"'>&#10006</button></div>";
				break;
				case "bool":
				out +="<div class='tagEl' id='tagDiv"+content[i].name+"'> "+content[i].name
					+"<button class='deleteTag' id='dT"+content[i].name+"'>&#10006</button></div>";
				break;
			}
		}
		
		target.innerHTML = out;
		UpdateClickerDeleteTag();
	}
}

/** Schliesst leere offene Reiter
*/
function CloseOpenTabs()
{
	
	var openTab = document.getElementsByClassName("opener active");
	console.log(openTab);
	
	for(var i=0; i < openTab.length; i++)
	{
		var kids = openTab[i].nextSibling.childNodes;
		var empty = true;
		console.log(kids);
		
		for(var j=0; j < kids.length; j++)
		{
			if(kids[j].className == "subgroup")
			{
				continue;
			}
			if(kids[j].style.display == "")
			{
				empty = false;
			}
		}
	
		if(empty)
		{
			openTab[i].nextSibling.style.maxHeight = null;
			openTab[i].className = "opener";
		}
	}
	
}

// Verstecke subgroup, falls kein Element mehr drin steht
function HideSubgroup(name){
	// console.log(name);
	var elems = document.getElementsByClassName(name);
	for(var i = 0; i < elems.length;i++){	
		var elem = elems[i];
		var subgroup = elem.previousSibling;
		
		// Finde Supgroup-Element
		// Brich ab, falls keins vorhanden (bei Unterkunftssuche)
		while(!subgroup.classList.contains("subgroup")){
			subgroup = subgroup.previousSibling;
			
			if(subgroup == null){	// subgroup existiert nicht
				break;
			}
		}
		
		// Verstecke, falls existiert und kein weiteres Element in der subgroup
		if(subgroup != null){
			var otherElement = false;
			var iterator = elem.previousSibling;
			
			while(iterator != null && !iterator.classList.contains("subgroup"))
			{				
				if(iterator.style.display != "none"){
					otherElement = true;
					break;
				}
				
				iterator = iterator.previousSibling;
			}
			
			if(!otherElement)
			{
				iterator = elem.nextSibling;
				
				while(iterator != null && !iterator.classList.contains("subgroup"))
				{
					if(iterator.style.display != "none"){
						otherElement = true;
						break;
					}
					
					iterator = iterator.nextSibling;
				}
			}
			
			if(!otherElement)
				subgroup.style.display = "none";
		}else{
			console.log("subgroup not found");
		}
	}
}

function DisplaySubgroup(name){
	console.log(name);
	var elems = document.getElementsByClassName(name);
	for(var i = 0; i < elems.length;i++){	
		var elem = elems[i];
		var subgroup = elem.previousSibling;
		
		// Finde Supgroup-Element
		// Brich ab, falls keins vorhanden (bei Unterkunftssuche)
		while(!subgroup.classList.contains("subgroup")){
			subgroup = subgroup.previousSibling;
			
			if(subgroup == null){	// subgroup existiert nicht
				break;
			}
		}
		
		console.log(subgroup);
		
		// Displaye, falls existiert und subgroup nicht displayed
		if(subgroup != null){
			if(subgroup.style.display == "none"){
				subgroup.style.display = "";
				console.log("I tried");
			}
		}
	}
}

/*--------------------------------Interactive Functions--------------------------------*/
//Clickers
function UpdateClickerDeleteTag()
{
	var clicker = document.getElementsByClassName("deleteTag");
	for(i = 0; i < clicker.length;i++)
	{
		clicker[i].addEventListener('click',function(e){
		e = e || window.event;
		var target = e.target || e.srcElement;
		locid = target.id;
		//Auf den Klick passe das Document an
		ProcessDeleteTagKlick(locid);
	},false);
	}	
}

var operator_changer = document.getElementsByClassName("operator_changer");
var clicker = document.getElementsByClassName("attributes_clicker");
var buttonSubmit = document.getElementById("submitSearch");

if (buttonSubmit != null)
{
	buttonSubmit.addEventListener('click',function(e){
	e = e || window.event;
    var target = e.target || e.srcElement;
	locid = target.id;
	searchDelegate();
	
},false);
}
else console.log("Wrong submit ");

/*Operator changer - Ändert das Operationszeichen bei der Auswahl von Integer Werten*/
for(i = 0; i < operator_changer.length;i++)
{
	operator_changer[i].addEventListener('click',function(e){
	e = e || window.event;
    var target = e.target || e.srcElement;
	locid = target.id;
	ChangeOperator(locid);
	
},false);
}
/*Wird aufgerufen, sobald die Übernahme in das Auswahlfeld mit dem Klick auf die Checkbox bestätigt wurde*/
for(i = 0; i < clicker.length;i++)
{
	clicker[i].addEventListener('click',function(e){
	e = e || window.event;
    var target = e.target || e.srcElement;
	processCheckboxKlick(target);
	this.checked = false;
},false);
}

function ChangeOperator(id)
{
	var myElement = document.getElementById(id);
	if (myElement != null)
	{
		switch(myElement.name)
		{
			case '0':
			myElement.innerHTML="&gt;";
			myElement.name="1";
			break;
			case '1':
			myElement.innerHTML="&lt;";
			myElement.name="2";
			break;
			case "2":
			myElement.innerHTML="&geq;";
			myElement.name="3";
			break;
			case "3":
			myElement.innerHTML="&leq;";
			myElement.name="4";
			break;
			case "4":
			myElement.innerHTML="=";
			myElement.name="0";
			break;
			default:
			myElement.innerHTML="!";
			break;
		}
		
	}
}
function searchDelegate()
{
	var targetBox = document.getElementById("ausKritAnz");
	if (targetBox == null)
	{
		// Fehler im Aufbau der Website
		return;
	}
	//sessionStorage.setItem("queryString",targetBox.innerHTML); -- SessionStorage wird durch die Übermittlung via formular nicht benötigt
	var inputArea = document.getElementById("queryString");
	if (inputArea != null)
	{
		inputArea.value = sessionStorage.getItem("content");
		document.searchDelegate.submit();
	}
	//Zurücksetzen der Zwischenspeicherung des Orts und der Distanz
	sessionStorage.removeItem("ortDist");
	
}

function processCheckboxKlick(elemen)
{
	//In der Checkbox steht im ClassName der Wert des Attributes welches wir brauche
	//Ebenso stehen andere Klassen, die keine Relevanz haben - Unsere Klasse ist immer die Zweite
	//Bei Attributen durch leerzeichen getrennt ist unsere Klasse immer die zweite und folgenden
	var splitter = elemen.className.split(" ");
	var attributName = splitter[1];
	if (splitter.length > 2)
	{
		for(var i =2;i < splitter.length;i++)
		{
			attributName+=" "+splitter[i];
		}
	}
	//Wieder entfernen der Klammern
	attributName = attributName.slice(1,attributName.length-1);
	console.log("attributName: "+attributName);
	//Der Name erzählt uns um welchen Typ es sich handelt
	//b - boolean, i - integer, c - charchoice
	//Ebenso wird die atNumber verraten
	//Dabei beginnt der eigentliche Name ab der zweiten Position, da die erste durch die Klammer belegt ist
	var atNumber = elemen.name.slice(1,elemen.name.length);
	var type = elemen.name.slice(0,1);
	
	switch(type)
	{
		case "b":
			//Füge den Wert zunächst dem Storage hinzu
			AddAttribute(attributName,"bool","true");
			//Lasse alle anderen möglichen Auswahlen verschwinden
			var auswahlen = document.getElementsByClassName("(p"+attributName+")");
			for(var i = 0; i < auswahlen.length;i++)
			{
				auswahlen[i].style.display = "none";
			}
			
			// Verstecke subgroup, falls letztes Element
			HideSubgroup("(p"+attributName+")");
			
		break;
		case "i":
			//Sammel die nötigen Informationen
			var operator = document.getElementById("b"+atNumber);
			var wert = document.getElementById("v"+atNumber);
			if (operator != null && wert != null)
			{
				//Füge das Element hinzu
				AddAttribute(attributName,"int",operator.innerHTML+" "+wert.value);
			}
			else
				console.log("Fehler: Wert oder Operator für "+attributName+" konnte nicht gefunden werden");
			//Lasse alle anderen Auswahlfenster verschwinden
			var auswahlen = document.getElementsByClassName("(p"+attributName+")");
			for(var i = 0; i < auswahlen.length;i++)
			{
				auswahlen[i].style.display = "none";
			}
			
			// Verstecke subgroup, falls letztes Element
			HideSubgroup("(p"+attributName+")");			
		break;
		case "c":
			//Sammel die Infos			
			var selector = document.getElementById("s"+atNumber);
			if (selector != null)
			{
				//Füge den Wert hinzu
				AddAttribute(attributName,"charc",selector.value);
				console.log("Füge als Name hinzu:"+attributName);
				//Blende alle optionen aus, die bereits gewählt wurden
				var allOptions = document.getElementsByClassName("("+attributName+'_'+selector.value+")");
				for(var i = 0;i < allOptions.length;i++)
				{
					//Disable die einzelne Option
					allOptions[i].disabled = true;
					//Wähle die nächste verfügbare Option aus oder lasse das Objekt verschwinden, wenn keine Option mehr gewählt werden kann
					var sib = allOptions[i].nextSibling;
					if (sib == null)
						sib = allOptions[i].previousSibling;
					if (sib == null)
					{
						//Verstecke alle selektoren
						console.log("Verstecke die Selektoren");
						var allSelectors = document.getElementsByClassName("(p"+attributName+")");
						for(var j=0;j<allSelectors.length;j++)
						{
							allSelectors[j].style.display = "none";
						}
						// Verstecke subgroup, falls letztes Element
							HideSubgroup("(p"+attributName+")");
					}
					else
					{
						while(sib.disabled == true && sib.nodeName == "OPTION" && sib.nextSibling != null)
						{
							sib = sib.nextSibling;
						}
						//Gehe auch die andere Richtung falls ein Element am Ende ausgewählt wurde
						while(sib.disabled == true && sib.nodeName == "OPTION" && sib.previousSibling != null)
						{
							sib = sib.previousSibling;
						}
						//Unterscheide ob noch Auswahlelemente verfügbar sind
						if (sib.nodeName == "OPTION" && sib.disabled == false)
						{
							//Wähle das nächste Element aus 
							selector.value = sib.value;
						}
						else
						{
							//Verstecke alle selektoren
							console.log("Verstecke die Selektoren");
							var allSelectors = document.getElementsByClassName("(p"+attributName+")");
							for(var j=0;j<allSelectors.length;j++)
							{
								allSelectors[j].style.display = "none";
							}
							
							// Verstecke subgroup, falls letztes Element
							HideSubgroup("(p"+attributName+")");
						}
					}
				}
				
			}
		break;
		
	}
	
	//Schliesse Reiter falls leer
	CloseOpenTabs();
}

function ProcessDeleteTagKlick(id)
{
	console.log("hallo");
	//Die id liefert uns den Attributnamen. Dabei sind die ersten beiden Stellen dT
	var locId = id.slice(2,id.length);
	/*Danach kommt der Attributname gefolgt von
	* _[Wert] für Charchoice in einer Gruppe
	*/
	var splitter = locId.split("_");
	console.log(splitter);
	
	if (splitter.length == 2)
	{
		//Charchoice
		//Entferne den Wert aus dem Storage
		RemoveAttribute(splitter[0],splitter[1]);
		//Verändere die Auswahl und lass diese wieder enbalen bzw. erscheinen
		var eles = document.getElementsByClassName("("+locId+")");
		for(var i =0; i <eles.length;i++)
		{
			eles[i].disabled = false;
			var par = eles[i].parentNode;
			if (par.parentNode.style.display == "none")
			{
				par.parentNode.style.display = "";
				par.value = eles[i].value;
			}
		}
	}else
	{
		//Int, Bool
		//Entfernen aus dem Storage
		RemoveAttribute(splitter[0],"");
		//Erscheinen lassen in der Auswahl
		var elemens = document.getElementsByClassName("(p"+splitter[0]+")");
		for(var i =0; i <elemens.length;i++)
		{
			elemens[i].style.display = "";
		}
	}
	
	// Stelle Subgroup wieder da, falls benoetigt
	DisplaySubgroup("attributes (p" + splitter[0]+")");
	
	//Aendere maxHeight, falls Reiter offen
	var panel = document.getElementsByClassName("attributes (p" + splitter[0]+")");
	//console.log(panel);
	//console.log(panel.parentNode);
	for(var k = 0; k < panel.length; k++)
	{
		if(panel[k].parentNode.previousSibling.className == "opener active")
		{
			//console.log(panel.parentNode);
			panel[k].parentNode.style.maxHeight = panel[k].parentNode.scrollHeight + "px";
		}
	}
	
}

/**Konzipiert für den Fall, dass wir zurück auf die Seite navigieren.
* Versteckt alle Elemente, die zwar bereits im SessionStorage sind, aber nicht auf der Seite übernommen wurden
*/
function HideAllStorageElements()
{
	//Hole den Content
	var readContent = sessionStorage.getItem("content");
	if (readContent != null && readContent != "")
	{
		var content =JSON.parse(readContent);
		
		for(var i=0;i<content.length;i++)
		{
			switch(content[i].typ)
			{
				case"bool":
				case"int":
					var elemens = document.getElementsByClassName("(p"+content[i].name+")");
					for(var j = 0;j <elemens.length;j++)
					{
						elemens[j].style.display = "none";
					}
				break;
				case"charc":					
					//Blende alle optionen aus, die bereits gewählt wurden
					console.log("charc");
					for (var t = 0; t < content[i].val.length;t++)
					{
					var allOptions = document.getElementsByClassName("("+content[i].name+'_'+content[i].val[t]+")");
					console.log(allOptions);
					console.log(content[i].name+'_'+content[i].value);
					attributName = content[i].name;
						for(var j = 0;j < allOptions.length;j++)
						{
							//Disable die einzelne Option
							allOptions[j].disabled = true;
							//Wähle die nächste verfügbare Option aus oder lasse das Objekt verschwinden, wenn keine Option mehr gewählt werden kann
							var sib = allOptions[j].nextSibling;
							if (sib == null)
								sib = allOptions[j].previousSibling;
							if(sib == null)
							{
								console.log("zweites SIb NUll");
								//Verstecke alle selektoren
								console.log("Verstecke die Selektoren");
								var allSelectors = document.getElementsByClassName("(p"+attributName+")");
								for(var k=0;k<allSelectors.length;k++)
								{
									allSelectors[k].style.display = "none";
								}
							}else
							{	
								while(sib.disabled == true && sib.nodeName == "OPTION" && sib.nextSibling != null)
								{
									sib = sib.nextSibling;
								}
								//Gehe auch die andere Richtung falls ein Element am Ende ausgewählt wurde
								while(sib.disabled == true && sib.nodeName == "OPTION" && sib.previousSibling != null)
								{
									sib = sib.previousSibling;
								}
								//Unterscheide ob noch Auswahlelemente verfügbar sind
								if (sib.nodeName == "OPTION" && sib.disabled == false)
								{
									//Wähle das nächste Element aus und stelle dessen Wert im Selektor dar
									sib.parentNode.value = sib.value;
								}
								else
								{
									//Verstecke alle selektoren
									console.log("Verstecke die Selektoren");
									var allSelectors = document.getElementsByClassName("(p"+attributName+")");
									for(var k=0;k<allSelectors.length;k++)
									{
										allSelectors[k].style.display = "none";
									}
								}
							}
						}
					}
				break;
			}			
		}
	}	
}
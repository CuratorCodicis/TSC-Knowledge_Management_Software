<!DOCTYPE HTML>
<HTML>
<HEAD>
<TITLE>Suchergebnisse</TITLE>
<meta charset="UTF-8">
<LINK rel="stylesheet" href="\general.css">
<LINK rel="stylesheet" href="\colors.css">
<LINK rel="stylesheet" href="styles/ErgebnisseStyle.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<LINK rel="stylesheet" href="\Icons/css/all.css">
<style>
.circle2{
	right: 4%;
	top: 5%;
	position: absolute;
	z-index: +1;
	width: 200px;
	height: 200px;
	border-radius: 50%;
	background-color: white;
}
.treeBranch
{
	float: left;
	width:5%;
	vertical-align:middle;
	background-color:white;
	font-size:5vh;
}
</style>
</HEAD>
<?php
require('../database.php');
require("../APIKey.php");
error_reporting(E_ERROR | E_PARSE);
$finalIDs = null;
//Überprüfe auf die überlieferten Parameter
try
{
	//Nimm unsere Suchargumente direkt aus dem Feld entgegen
	$queryString = $_POST['queryString'];
	$typ = $_POST['Typ'];
	$coords = $_POST['coordinates'];
	$sOrt = $_POST['startort'];
	$maxDist = $_POST['maxDist'];
	$SAs = $_POST['SAs'];
}
catch(Exception $ex)
{
	
}
if ($coords ==  null)
{
	//TODO: Fehlerbehandlung
	echo "<h1 id='fehler'>Wichtige Parameter fehlen!</h1>";
}	
?>
<BODY>
<script>
function leave_site(){
	sessionStorage.clear();
	window.location.replace("../Index.php")
}
function leave_site2(){
	sessionStorage.clear();
	window.location.replace("Suche.php")
}
</script>
<?php
if ($typ == "Unterkunft" && !isset($_POST["finalIDs"]))
	echo '<div class="color" id="colorU1">';
elseif($typ == "Sportstaette" && !isset($_POST["finalIDs"]))
	echo '<div class="color" id="colorS1">';
else
	echo '<div class="color" id="colorSuche">';
?>
<button class="HomeB" onclick="leave_site()">Home</button></br></br>
<h1> Suchergebnisse </h1></br></br>
</div>
<div class="circle"><i style='font-size:50px;margin-top:35%;color:grey;' class='fas'>&#xf5a0;</i></div>

<div id="content">
<?php

$conn = getConnection();
//Definierung der geeigneten Sportarten bei der Suche nach einer Sportstätten
$sportarten = explode(';',$SAs); 

if ($typ == null || ($typ != "Sportstaette" && $typ != "Unterkunft"))
	echo "Fehler: falscher Typ übermittelt. Seite wurde nicht in der Folge einer Unterkunfts- oder Sportstättensuche geladen.";
$finalIDs = array(array());
//Behandlung von einer Parameterfreien Suche
if ($queryString == null)
{
	//Keine Einschränkenden Parameter vorhanden --> Ausgabe von allen Unterkünften
	$content = [];
	$query = "";
	$col = "";
	if ($typ == "Unterkunft")
	{
		$col = "ID";
		$query = "SELECT ID, Koordinaten FROM Unterkunft; ";
	}
	else if ($typ == "Sportstaette" && count($sportarten) <= 1)
	{
		$col = "ID";
		$query = "SELECT ID, Koordinaten FROM Sportstaette;";
	}
	else if ($typ == "Sportstaette" && count($sportarten) > 1)
	{
		//Ausgabe aller Sportstätten, die auf die Sportarten passen
		$col = 'ID';
		$query = "SELECT DISTINCT ID, Koordinaten FROM EignungSASS E JOIN Sportstaette S ON E.SSID = S.ID WHERE SAName LIKE '".$sportarten[1]."'";
		for ($i = 2; $i < count($sportarten); $i++)
		{
			$query .= " OR SAName LIKE '".$sportarten[$i]."'";
		}		
	}
	$result = $conn->query($query);
	if ($result != null && $result->num_rows > 0)
	{			
		while ($row = $result->fetch_assoc())
		{
			if (CheckInRange($coords,$row['Koordinaten'],$maxDist))
			{
				$finalIDs[0][] = $row[$col];
			}
		}
		
	}
}
else
{
	$content = json_decode($queryString);
	echo "<script>console.log(JSON.parse('".$queryString."'));</script>";
}
//Falls nach einer Sportstaette gesucht wurde, erstelle ein Array mit allen Sportstaetten, die dafür geeignet sind
$possibleIDs = array();
if ($typ == "Sportstaette" && count($sportarten) > 1)
{
	$query = "SELECT ID, Koordinaten FROM EignungSASS E JOIN Sportstaette S ON E.SSID = S.ID WHERE SAName LIKE '".$sportarten[1]."'";
	for ($i = 2; $i < count($sportarten); $i++)
	{
		$query .= " OR SAName LIKE '".$sportarten[$i]."'";
	}		
	$query .= "ORDER BY ID DESC;";
	$result = $conn->query($query);
	if ($result != null && $result->num_rows > 0)
	{			
		while ($row = $result->fetch_assoc())
		{
			$possibleIDs[] = $row["ID"];
		}		
	}
}

//Auswahl der einzelnen IDs
$boolUIDs = array();
$intUIDs = array();
$charUIDs = array();

//Erstellen eines Arrays/2-dimensioneller Matrix
//Die Spalten bilden die einzelnen UIDs, die Zeilen werden durch die Attribute bestimmt
//Die Einträge werden mit 0 oder 1 kodiert
$samMatrix = array();
$attrListe= array();
$attrHead = array();

for($i=0; $i <count($content);$i++)
{	
	//Unterscheidung nach dem Typ der Variable
	switch($content[$i]->{'typ'})
	{
		//Boolean
		case "bool":
		$res = array();
		//Unterscheidung in Sportstaette und Unterkunft fuer die Anfrage
		$query = "";
		$col = "";//col um die Auswahl der Spalte bei der Auswertung der Anfrage dynamisch zu halten
		if ($typ == "Unterkunft")
		{
			$col = "UID";
			$query = "SELECT DISTINCT UID, Koordinaten FROM Ubesitzt_bool as b JOIN Unterkunft as u on b.UID=u.ID WHERE (AName = '".trim($content[$i]->{'name'})."') ORDER BY UID ASC";
		}
		else if ($typ == "Sportstaette")
		{
			$col = "SSID";
			$query = "SELECT DISTINCT SSID, Koordinaten FROM SSbesitzt_bool as b JOIN Sportstaette as s on b.SSID=s.ID WHERE (AName = '".trim($content[$i]->{'name'})."') ORDER BY SSID ASC";
		}
		
		//Hinzufügen zur Liste; Liste bildet sozusagen Zeilenbeschriftung der Matrix
		$attrListe[] = trim($content[$i]->{'name'});
		$result = $conn->query($query);
		$mE=0;// matrix Entry zählt die Position im attrHead
		//echo "</br> Ergebnis der Abfrage nach bool ".$content[$i]->{'name'};
		if ($result != null && $result->num_rows > 0)
		{					
			while ($row = $result->fetch_assoc())
			{				
				//Überprüfen, ob die ausgewählte ID überhaupt in die Abstufung gerät
				if (count($possibleIDs) == 0 || in_array($row[$col],$possibleIDs))
				{
					//echo "</br> ".$row[$col];
					//Einfügen in die bisherige Matrix
					if (count($samMatrix) >= 1)
					{
						//Überprüfe zunächst den Head auf Vorhandensein der IDs
						//Beginne dabei vorn und zähle immer hoch, da beides aufsteigend nach der Größe geordnet ist	
						//ID im Head ist kleiner, als unser Ergebnis -- > ID ist nicht vorhanden im Ergebnis						
						while(intval($attrHead[$mE]) < intval($row[$col]) && $mE < count($attrHead))
						{
							//echo intval($attrHead[$mE])." < ".intval($row[$col])." = ".intval($attrHead[$mE]) < intval($row[$col]);
							$res[] = 0;
							$mE++;						
						}			
						//echo "<br>mE ".$mE;
						if(intval($attrHead[$mE]) == intval($row[$col]))
						{
							//echo "</br>".$row[$col]."ist im Ergebnis vorhanden und wird an Position ".$mE." eingefügt";
							//ID ist an derselben Stelle vorhanden 
							$res[] = 1;
							$mE++;
						}
						else if (intval($attrHead[$mE]) > intval($row[$col]))
						{
							//echo "</br>".$row[$col]."ist noch nicht im Ergebnis vorhanden und muss in der Mitte eingefügt werden";
							//ID im Head ist größer, als unser Ergebnis --> Ergebnis ist noch nicht in der Matrix vorhanden
							//Einfügen in den Head an der Stelle und shiften
							//Vorher überprüfen auf ein Erfüllen der Distanz
							if (CheckInRange($coords,$row['Koordinaten'],$maxDist))
							{
								$insert = array($row[$col]);
								array_splice( $attrHead, $mE, 0, $insert);
								//Einfügen in die Matrix
								$res[] = 1;
								for($j=0;$j<count($samMatrix);$j++)
								{
									$insert = array(0);
									array_splice( $samMatrix[$j], $mE, 0, $insert);
								}
								$mE++;
							}
							//echo "</br>Ausgabe der in der Mitte eingefügten ID";
							//var_dump($res);
							
						}
						else if (intval($attrHead[$mE]) != intval($row[$col]) && $mE == count($attrHead))
						{
							//echo $row[$col]."ist noch nicht im Ergebnis vorhanden und muss am Ende eingefügt werden";
							//Attribut ist nicht vorhanden und muss am Ende hinzugefügt werden
							//Vorher überprüfen auf ein Erfüllen der Distanz
							if (CheckInRange($coords,$row['Koordinaten'],$maxDist))
							{
								$attrHead[]=$row[$col];
								//Einfügen der Nullzeile -- Veraltet
								//$res[]=0;
								//$mE++;
								//Einfügen des neuen Eintrags
								$res[]=1;
								//Auffüllen der vorherigen Einträge mi 0
								for($j=0;$j<count($samMatrix);$j++)
								{
									$samMatrix[$j][]=0;
								}
								$mE++;
							}
							//echo "</br>Ausgabe der am Ende eingefügten ID";
							//var_dump($res);						
						}
						else
						{
							echo "</br>".$row[$col]."ist noch nicht im Ergebnis vorhanden und keiner der vorherigen Abfragen ist getriggert. Position ist ".$mE;
						}			
					}
					else
					{
						if (CheckInRange($coords,$row['Koordinaten'],$maxDist))
						{
							$res[] = $row[$col];
						}
					}
				}
			}			
		}
		//Anfrage ist terminiert, bevor alle EInträge in der Matrix befüllt wurde
		//Befülle die restlichen Einträge mit 0
		while($mE < count($attrHead))
		{
			$res[]=0;
			$mE++;
		}
		//Hinzufügen zur Matrix
		if (count($samMatrix) != 0)
			$samMatrix[] = $res;
		else if (count($samMatrix) == 0)
		{
			//Setze den Head als erstes fest
			$attrHead = $res;
			//Füge alle Einträge als 1 hinzu 
			$temp =array();
			for ($j=0;$j < count($attrHead);$j++)
				$temp[]=1;
			$samMatrix[] = $temp;
		}
		break;
		
		//Integer
		case "int":
		//Umwandeln des Vergleichoperators
		$value = $content[$i]->{'val'}[0];
		$operator = '=';
		$start = 1;
		switch(substr($value,0,1))
		{
			case '=':
			$operator = '=';
			break;
			case '&':
			if (substr($value,1,2) == 'gt')
				$operator = '>';
			else if (substr($value,1,2) == 'lt')
				$operator = '<';
			if (substr($value,3,1) == ';')
				$start = 4;
			else
				$start = 3;
			break;
			case '>':
			$operator = '>';
			break;
			case '<':
			$operator = '<';
			break;
			case '≥':
			$operator = '>=';
			break;
			case '≤':
			$operator = '<=';
			break;
			default:
			$operator = null;
			break;
		}
		//Speichern des richtigen Wertes ohne Operator
		$value = substr($value,$start);
		if ($operator != null)
		{
			$res = array();
			//Unterscheidung in Sportstaette und Unterkunft fuer die Anfrage
			$query = "";
			$col = "";//col um die Auswahl der Spalte bei der Auswertung der Anfrage dynamisch zu halten
			if ($typ == "Unterkunft")
			{
				$col = "UID";
				$query = "SELECT DISTINCT UID, Koordinaten FROM Ubesitzt_int as b JOIN Unterkunft as u on b.UID=u.ID WHERE (AName = '".trim($content[$i]->{'name'})."' AND Wert ".$operator.$value.") ORDER BY UID ASC";
			}
			else if ($typ == "Sportstaette")
			{
				$col ="SSID";
				$query = "SELECT DISTINCT SSID, Koordinaten FROM SSbesitzt_int as b JOIN Sportstaette as s on b.SSID=s.ID WHERE (AName = '".trim($content[$i]->{'name'})."' AND Wert ".$operator.$value.") ORDER BY SSID ASC";
			}

			$attrListe[] = trim($content[$i]->{'name'});
			$result = $conn->query($query);
			$mE=0;// matrix Entry zählt die Position im attrHead
			if ($result != null && $result->num_rows > 0)
			{					
				while ($row = $result->fetch_assoc())
				{
					//Überprüfen, ob die ausgewählte ID überhaupt in die Abstufung gerät
					if (count($possibleIDs) == 0 || in_array($row[$col],$possibleIDs))
					{
						//$res[] = $row[$col]; -- noch aus alten Tagen, Soll entfernt werden, wenn das neue System funktioniert
						//Einfügen in die bisherige Matrix
						if (count($samMatrix) >= 1)
						{
							//Überprüfe zunächst den Head auf Vorhandensein der IDs
							//Beginne dabei vorn und zähle immer hoch, da beides aufsteigend nach der Größe geordnet ist	
							//ID im Head ist kleiner, als unser Ergebnis -- > ID ist nicht vorhanden im Ergebnis						
							while(intval($attrHead[$mE]) < intval($row[$col]) && $mE < count($attrHead))
							{
								$res[] = 0;
								$mE++;						
							}
							
							if(intval($attrHead[$mE]) == intval($row[$col]))
							{
								//ID ist an derselben Stelle vorhanden 
								$res[] = 1;
								$mE++;
							}
							else if (intval($attrHead[$mE]) > intval($row[$col]))
							{
								//ID im Head ist größer, als unser Ergebnis --> Ergebnis ist noch nicht in der Matrix vorhanden
								//Einfügen in den Head an der Stelle und shiften
								//Vorher überprüfen auf die Distanz
								if (CheckInRange($coords,$row['Koordinaten'],$maxDist))
								{
									$insert = array($row[$col]);
									array_splice( $attrHead, $mE, 0, $insert);
									//Einfügen in die Matrix
									$res[] = 1;
									for($j=0;$j<count($samMatrix);$j++)
									{
										$insert = array(0);
										array_splice( $samMatrix[$j], $mE, 0, $insert);
									}
									$mE++;
								}
								
							}
							else if (intval($attrHead[$mE]) != intval($row[$col]) && $mE == count($attrHead))
							{
								//Attribut ist nicht vorhanden und muss am Ende hinzugefügt werden
								//Einfügen nur wenn die Distanz erfüllt ist
								if (CheckInRange($coords,$row['Koordinaten'],$maxDist))
								{
									$attrHead[]=$row[$col];
									//$res[]=0;
									//$mE++;
									$res[]=1;
									//Auffüllen der vorherigen Einträge mi 0
									for($j=0;$j<count($samMatrix);$j++)
									{
										$samMatrix[$j][]=0;
										echo "Hallo";
									}
									$mE++;
								}
								
							}
							else
							{
								echo "</br>INT ".$row[$col]."ist noch nicht im Ergebnis vorhanden und keiner der vorherigen Abfragen ist getriggert. Position ist ".$mE;
							}
						}
						else
						{
							if (CheckInRange($coords,$row['Koordinaten'],$maxDist))
							{
								$res[] = $row[$col];
							}
						}
					}
				}			
			}
			//Anfrage ist terminiert, bevor alle EInträge in der Matrix befüllt wurde
			//Befülle die restlichen Einträge mit 0
			while($mE < count($attrHead))
			{
				$res[]=0;
				$mE++;
			}
			//Hinzufügen zur Matrix
			if (count($samMatrix) != 0)
				$samMatrix[] = $res;
			else if (count($samMatrix) == 0)
			{
				//Setze den Head als erstes fest
				$attrHead = $res;
				//Füge alle Einträge als 1 hinzu 
				$temp =array();
				for ($j=0;$j < count($attrHead);$j++)
					$temp[]=1;
				$samMatrix[] = $temp;
			}
		}		
		break;
		case "charc":
		$res = array();
		$values = $content[$i]->{'val'};
		$query = "";
		$col = "";
		$table = "";
		//Unterscheidung in Sportstaette und Unterkunft fuer die Anfrage
		if ($typ == "Unterkunft")
		{
			$col = "UID";
			$table = "Ubesitzt_char as b JOIN Unterkunft as u on b.UID=u.ID";
		}
		else if ($typ == "Sportstaette")
		{
			$col = "SSID";
			$table = "SSbesitzt_char as b JOIN Sportstaette as s on b.SSID=s.ID";
		}
		
		//Unterscheidung in einfache Anfrage oder eine Veroderung
		//var_dump($values);
		if (count($values) > 1)
		{
			//Wir haben eine Veroderung
			$queryVars = "";
			for($j = 0; $j < count($values);$j++)
			{
				$queryVars .= "OR Wert LIKE '".trim($values[$j])."'";				
			}
			$query = "SELECT DISTINCT ".$col.", Koordinaten FROM ".$table." WHERE (AName = '".trim($content[$i]->{'name'})."' ".$queryVars.") ORDER BY ".$col." ASC";

		}
		else
		{
			//Keine Veroderung
			$query = "SELECT DISTINCT ".$col.", Koordinaten FROM ".$table." WHERE (AName = '".trim($content[$i]->{"name"})."' AND Wert LIKE '".trim($content[$i]->{"val"}[0])."') ORDER BY ".$col." ASC";

		}
		
		//echo "</br></br></br> Ergebnis der Abfrage nach charc ".$content[$i]->{'name'};
		//echo "</br> Die Abfrage </br>".$query;
		
		//Hinzufügen zur Liste; Liste bildet sozusagen Zeilenbeschriftung der Matrix
		$attrListe[] = trim($content[$i]->{'name'});
		$result = $conn->query($query);
		$mE=0;// matrix Entry zählt die Position im attrHead
		if ($result != null && $result->num_rows > 0)
		{								
			while ($row = $result->fetch_assoc())
			{
				//Überprüfen, ob die ausgewählte ID überhaupt in die Abstufung gerät
				if (count($possibleIDs) == 0 || in_array($row[$col],$possibleIDs))
				{
					//echo "</br> ".$row[$col];
					//$res[] = $row[$col]; -- noch aus alten Tagen, Soll entfernt werden, wenn das neue System funktioniert
					//Einfügen in die bisherige Matrix
					if (count($samMatrix) >= 1)
					{
						//Überprüfe zunächst den Head auf Vorhandensein der IDs
						//Beginne dabei vorn und zähle immer hoch, da beides aufsteigend nach der Größe geordnet ist	
						//ID im Head ist kleiner, als unser Ergebnis -- > ID ist nicht vorhanden im Ergebnis						
						while(intval($attrHead[$mE]) < intval($row[$col]) && $mE < count($attrHead))
						{
							$res[] = 0;
							$mE++;						
						}
						//echo "</br> mE nach jetzt".$mE;
						if(intval($attrHead[$mE]) == intval($row[$col]))
						{
							//echo "</br>".$row[$col]."ist im Ergebnis vorhanden und wird an Position ".$mE." eingefügt";
							//ID ist an derselben Stelle vorhanden 
							$res[] = 1;
							$mE++;
						}
						else if (intval($attrHead[$mE]) > intval($row[$col]))
						{
							//echo "</br>".$row[$col]."ist noch nicht im Ergebnis vorhanden und muss in der Mitte eingefügt werden";
							//ID im Head ist größer, als unser Ergebnis --> Ergebnis ist noch nicht in der Matrix vorhanden
							//Einfügen in den Head an der Stelle und shiften
							//Prüfe auf Distanz
							if (CheckInRange($coords,$row['Koordinaten'],$maxDist))
							{
								$insert = array($row[$col]);
								array_splice( $attrHead, $mE, 0, $insert);
								//Einfügen in die Matrix
								$res[] = 1;
								for($j=0;$j<count($samMatrix);$j++)
								{
									$insert = array(0);
									array_splice( $samMatrix[$j], $mE, 0, $insert);
								}
								$mE++;
							}
							
						}
						else if (intval($attrHead[$mE]) != intval($row[$col]) && $mE == count($attrHead))
						{
							//echo $row[$col]."ist noch nicht im Ergebnis vorhanden und muss am Ende eingefügt werden";
							//Attribut ist nicht vorhanden und muss am Ende hinzugefügt werden
							//Prüfe auf Distanz
							if (CheckInRange($coords,$row['Koordinaten'],$maxDist))
							{
								$attrHead[]=$row[$col];
								//$res[]=0;
								//$mE++;
								$res[]=1;
								//Auffüllen der vorherigen Einträge mi 0
								for($j=0;$j<count($samMatrix);$j++)
								{
									$samMatrix[$j][]=0;
								}
								$mE++;
							}
							
						}
						else
						{
							echo "</br>CHAR ".$row[$col]."ist noch nicht im Ergebnis vorhanden und keiner der vorherigen Abfragen ist getriggert. Position ist ".$mE;
						}
					}
					else
					{
						if (CheckInRange($coords,$row['Koordinaten'],$maxDist))
						{
							$res[] = $row[$col];
						}
					}
				}
			}			
		}
		//Anfrage ist terminiert, bevor alle EInträge in der Matrix befüllt wurde
		//Befülle die restlichen Einträge mit 0
		while($mE < count($attrHead))
		{
			$res[]=0;
			$mE++;
		}
		//Hinzufügen zur Matrix
		if (count($samMatrix) != 0)
			$samMatrix[] = $res;
		else if (count($samMatrix) == 0)
		{
			//Setze den Head als erstes fest
			$attrHead = $res;
			//Füge alle Einträge als 1 hinzu 
			$temp =array();
			for ($j=0;$j < count($attrHead);$j++)
				$temp[]=1;
			$samMatrix[] = $temp;
		}
		break;
			
	}	
	//var_dump($attrHead);echo "<br>";
	
}

if (count($finalIDs[0]) == 0)
{
	//Einordnen in die Kategorien
	$finalIDs = array(array(),array(),array(),array(),array());
	$joker = array();
	for($i=0;$i<count($attrHead);$i++)
	{
		//Zählen der Übereinstimmungen für die IDs
		$anz = 0;
		for($j = 0;$j<count($attrListe);$j++)
		{
			$anz += intval($samMatrix[$j][$i]);
		}
		if ($anz == count($attrListe))
		{
			//Perfektes Ergebnis
			$finalIDs[0][] = strval($attrHead[$i]);
		}
		else if ($anz == count($attrListe)-1)
		{
			$finalIDs[1][] = strval($attrHead[$i]);
		}
		else if ($anz/count($attrListe) >= 0.8)
		{
			$finalIDs[2][] = strval($attrHead[$i]);
		}
		else if ($anz/count($attrListe) >= 0.6)
		{
			$finalIDs[3][] = strval($attrHead[$i]);
		}
		else if ($anz/count($attrListe) >= 0.4)
		{
			$finalIDs[4][] = strval($attrHead[$i]);
		}
		else
		{
			$joker[] = strval($attrHead[$i]);
		}
	}

	//Falls zu wenige Ergebnisse da sind, wird das Ganze durch die unter 40% ergänzt
	if (count($attrHead)-count($joker) < 10)
	{
		$finalIDs[4] = array_merge($joker,$finalIDs[4]);
	}
}
// Ausgabe der Matrix zu testzwecken
/*
echo '<table><tr><td></td>';
for ($i=0; $i < count($attrHead);$i++)
	echo'<td>'.$attrHead[$i].'</td>';
echo '</tr>';
for($i=0; $i < count($attrListe);$i++)
{
	echo '<tr><td>'.$attrListe[$i].'</td>';
	for($j = 0;$j < count($samMatrix[$i]);$j++)
	{
		echo '<td>'.$samMatrix[$i][$j].'</td>';
	}
	echo '</tr>';
}
echo '</table>';

//Ausgabe der Kategorien zum Testen
/*
echo "<br><table>";
for($i=0; $i < count($finalIDs);$i++)
{
	echo '<tr><th>'.$i.'</th>';
	for($j = 0;$j < count($finalIDs[$i]);$j++)
	{
		echo '<td>'.$finalIDs[$i][$j].'</td>';
	}
	echo "</tr>";
}
echo "</table>";*/

//Falls nach beiden gesucht wird, wird hier die Weiterleitung zu der Sportstaettensuche geschehen
if ((isset($_POST["maxDistZwischen"]) && trim($_POST["maxDistZwischen"] != "")) && (!isset($_POST["finalIDs"])))
{
	//Ablegen und Weitersenden der Ergebnisse der Ersten Suche 
	echo "<form name='delegateBoth' action='Sportartauswahl.php' method='POST'> 
	<input type='hidden' name='finalIDs' value='".json_encode($finalIDs)."' />
	<input type='hidden' name='head' value='".json_encode($attrHead)."' />
	<input type='hidden' name='matrix' value='".json_encode($samMatrix)."' />
	<input type='hidden' name='attrListe' value='".json_encode($attrListe)."' />;
	<input type='hidden' name='joker' value='".json_encode($joker)."' />
	<input type='hidden' name='maxDistZwischen' value='".$_POST['maxDistZwischen']."' />
	<input type='hidden' name='maxDist' value='".$_POST['maxDist']."' />
	<input type='hidden' name='startort' value='".$_POST['startort']."' />
	<input type='hidden' name='coordinates' value='".$_POST['coordinates']."' />
	
	</form>";
	echo '<script> document.delegateBoth.submit();</script>';
}

//Falls nach beiden gesucht wird und bereits die zweite Suche abgeschlossen ist, wird hier der Rest ausgewertet
if (isset($_POST["finalIDs"]))
{
	$finalIDsFirst = json_decode($_POST["finalIDs"]);
	$jokerFirst = json_decode($_POST["joker"]);
	$maxDistZwischen = json_decode($_POST["maxDistZwischen"]);
	//Matrix wurde übernommen, um eventuell abweichende Attribute anzuzeigen
	$attrSamMatrixFirst = json_decode($_POST["matrix"]);
	$attrHeaderFirst = json_decode($_POST["head"]);
	$attrListeFirst = json_decode($_POST["attrListe"]);
	
	//Kreuzweises überprüfen auf Paare in der Nähe
	//Herausfiltern der Koordinaten für die Sportstaette in Vorhand, um Anfragen zu vermeiden
	$KoordinatenSportstaette = array();
	$start = 0;
	$sql = 'SELECT ID,Koordinaten FROM sportstaette WHERE ';
	for($j=0;$j < 5;$j++)
	{
		for($k=0;$k< count($finalIDs[$j]);$k++)
		{
			if ($start == 0)
			{
				$sql .=' ID = "'.$finalIDs[$j][$k].'"';
				$start = 1;
			}
			else
			{
				$sql .=' OR ID = "'.$finalIDs[$j][$k].'"';
			}
		}
	}
	$sql.=' ORDER BY ID;';

	$res = $conn->query($sql);
	if ($res != null && $res->num_rows > 0)
	{			
		while ($row = $res->fetch_assoc())
		{
			$KoordinatenSportstaette[$row["ID"]] = $row["Koordinaten"];
		}		
	}
	
	//Überprüfen beim Durchlaufen nach den Unterkünften
	$ZuordnungUzuS = array();
	for($j=0;$j < 5;$j++)
	{
		for($k=0;$k< count($finalIDsFirst[$j]);$k++)
		{
			$foundOne = false;
			$Koordinaten = "";
			//Holen der Koordinaten für die Unterkunft
			$sql = 'SELECT Koordinaten FROM Unterkunft WHERE ID="'.$finalIDsFirst[$j][$k].'"';
			$result = $conn->query($sql);
			if ($result != null && $result->num_rows > 0)
			{			
				while ($row = $result->fetch_assoc())
				{
					$Koordinaten = $row["Koordinaten"];
				}		
			}
			if ($Koordinaten == "")
			{
				//Fehler
			}
			else
			{
				//Überprüfen der Koordinaten mit allen Sportstaetten
				foreach($KoordinatenSportstaette as $KSID => $value)
				{
					if (CheckInRange($Koordinaten,$value,$maxDistZwischen))
					{
						if (!$foundOne)
							$ZuordnungUzuS[$finalIDsFirst[$j][$k]] = array();
						$foundOne = true;
						$ZuordnungUzuS[$finalIDsFirst[$j][$k]][] = $KSID;						
					}
				}
			}
		}
	}	
	//Hinzufügen der Eigenschaften für die parallele Suche
	echo '<input type="hidden" id="ZuordnungUzuS" value='.json_encode($ZuordnungUzuS).' />';
	echo '<input type="hidden" id="finalIDsFirst" value='.json_encode($finalIDsFirst).' />';
	echo '<input type="hidden" name="maxDistZwischen" value="'.$_POST["maxDistZwischen"].'" />';		
	
}

//Finale Ausgabe
//Vorerst finalIDs in ein input feld stecken, damit es vom Javascript abgefragt werden kann.
echo "<input type='hidden' id='IIDs' value='".json_encode($finalIDs)."'>";
echo "<input type='hidden' id='typ' value ='".$typ."'>";
//Ausgabe der Abweichlermatrizen für das Javascript	
$kurzel = substr($typ,0,1);
echo "<input type='hidden' name='attrListe".$kurzel."' value='".json_encode($attrListe)."' />";
echo "<input type='hidden' id='attrHead".$kurzel."' name='attrHead".$kurzel."' value='".json_encode($attrHead)."' />";
echo '<input type="hidden" id="attrMatrix'.$kurzel.'" name="attrMatrix'.$kurzel.'" value='.json_encode($samMatrix).' />';
if (isset($attrSamMatrixFirst) && $attrSamMatrixFirst != null)
{
	if ($kurzel == "U")
		$kurzel = "S";
	else 
		$kurzel = "U";
	echo "<input type='hidden' name='attrListe".$kurzel."' value='".json_encode($attrListeFirst)."' />";
	echo "<input type='hidden' id='attrHead".$kurzel."' name='attrHead".$kurzel."' value='".json_encode($attrHeaderFirst)."' />";
	echo '<input type="hidden" id="attrMatrix'.$kurzel.'" name="attrMatrix'.$kurzel.'" value="'.json_encode($attrSamMatrixFirst).'" />';
}	



//-------------------------------FUNKTIONEN-------------------------------------------

function CheckInRange($coordsStart,$coordsObj,$maxDistLoca)
{				
	//Beschränkung auf die Vorberechung von Längen- und Breitengrade
	//Erst Breitengrade dann Längengrade
	//Umrechnung von Breitengrade in km: 1 BG = 111.3 km
	//Umrechnung von Längengrade in km: 1 LG = 111.3 * cos(BG)
	$splittsCoordsObj = explode(',',$coordsObj);
	$splittsCoordsStart = explode(',',$coordsStart);
	$cont=true;
	if(count($splittsCoordsStart) == 2 && count($splittsCoordsObj) == 2)
	{
		$diff = (float)($splittsCoordsStart[0])-(float)($splittsCoordsObj[0]);
		if ($diff*111.3*0.9 > $maxDistLoca || $diff*111.3*0.9 < -1*$maxDistLoca) $cont=false;
		$diff = (float)($splittsCoordsStart[1])-(float)($splittsCoordsObj[1]);
		if ($diff < 0) $diff = $diff*-1;
		if (cos((float)$splittsCoordsObj[0])*111.3*$diff*0.9 > $maxDistLoca) $cont =false;
	}
	else
	{
		//Error
		$cont=false;
	}
	return $cont;
}

function intersectAll($allIDsPerGroup)
{
	$inter = $allIDsPerGroup;
	while(count($inter) > 1)
	{
		$zwischen = array();
		for($i =0;$i <count($inter)-1;$i = $i+2)
		{
			$zwischen[] = intersectIDs($inter[$i],$inter[$i+1]);
		}
		
		$inter = $zwischen;		
	}
	return $inter;
}

function intersectIDs($set1, $set2)
{
	$index1 = 0;
	$index2 = 0;
	$resSet = array();
	while(count($set1)>($index1) AND count($set2)>($index2))
	{
		if ($set1[$index1] == $set2[$index2])
		{
			$resSet[] = $set1[$index1];
			$index1=$index1+1;
			$index2=$index2+1;
		}
		else if ($set1[$index1] < $set2[$index2])
		{
			while($set1[$index1] < $set2[$index2] && count($set1)>($index1)) $index1=$index1+1;
		}
		else
		{
			while($set1[$index1] > $set2[$index2] && count($set2)>($index2)) $index2=$index2+1;
		}		
	}
	
	return $resSet;
}

$conn->close();
?>
</div>

	
<div id="loader">
	</br></br><i class="fa fa-spinner fa-spin" style="font-size:100px;color:#4CAF50"></i></br></br></br></br></br></br>
	<div id="progressbar">0%</div>
	</br>
	</br>
	</br>
	</br>
	</br>
	</br>
	</br>
	</br>
	</div>
</div>

</br></br></br></br>
<div id="map_list" >
	<div id="map">
		<!---------------------------- Fabis Box ----------------------------------->
		<div id="mapid" style="height:inherit;">
		
		</div>
		<!-- Leaflet als Möglichkeit, um Marker und sonstiges zu setzen -->
		<link rel="stylesheet" href="leaflet/leaflet.css">
		<script src="leaflet/leaflet.js"></script>
		<!-- Stamen als Quelle für die Darstellung der Kartendaten-->
		<script type="text/javascript" src="http://maps.stamen.com/js/tile.stamen.js?v1.3.0"></script>

	</div>
<div id="Legend_list">
	<div id="listHeader">
	<table style="width:100%">		
	<?php
	$showS = false;
	$showU = false;
	if (isset($_POST["finalIDs"]))
	{
		$showS = true;
		$showU = true;
	}
	else if ($kurzel == "U")
		$showU = true;
	else 
		$showS = true;
	
	if($showU)
		echo '<tr><td><img src="../Marker/markerU0.png" class="markerLegends"></td>
	<td><img src="../Marker/markerU1.png" class="markerLegends"></td>
	<td><img src="../Marker/markerU2.png" class="markerLegends"></td>
	<td><img src="../Marker/markerU3.png" class="markerLegends"></td>
	<td><img src="../Marker/markerU4.png" class="markerLegends"></td>
	</tr>';
	if($showS)
		echo '<tr><td><img src="../Marker/markerS0.png" class="markerLegends"></td>
	<td><img src="../Marker/markerS1.png" class="markerLegends"></td>
	<td><img src="../Marker/markerS2.png" class="markerLegends"></td>
	<td><img src="../Marker/markerS3.png" class="markerLegends"></td>
	<td><img src="../Marker/markerS4.png" class="markerLegends"></td>
	</tr>';
	
	echo'<tr>
	<td>100%</td>
	<td>>99%</td>
	<td>>80%</td>
	<td>>60%</td>
	<td>>40%</td>
	</tr>	
	</table>';
	
	if ($showS && $showU)
		echo '</br>
	Gruppiert nach 
	<input type="radio" name="group" value="U" id="groupU" checked onclick=callAction("U")><label for="groupU">Unterkunft</label>
	<input type="radio" name="group" value="S" id="groupS" onclick=callAction("S")><label for="groupS">Sportstaette</label>
	</br>	';	
	?>
	</div>
	<div id="list">
		<div id="listContent">
		</div>
			<!-- -->
		</div>
	</div>
</div>


<div id="nav">
<div id="zuruckBox">
<?php
if (!isset($_POST["finalIDs"]))
	echo '<button type="button" id="zuruck">Suche anpassen</button>';
?>
</div>
<div id="beendenBox"><button class="continueB" type="button"id ="beenden" onclick="leave_site2()">Suche beenden</button></div>
</div>
<form method="POST" action="<?php if ($typ == "Unterkunft") echo "Unterkunftssuche.php"; else echo "Sportstaettensuche.php";?>" id="backDelegate" name="backDelegate">
<input type="hidden" name="startort" value="<?php echo $sOrt; ?>">
<input type="hidden" name="maxDist" value="<?php echo $maxDist; ?>">
<input type="hidden" name="coordinates" value="<?php echo $coords; ?>">
<input type="hidden" name="queryString" value="<?php echo $queryString;?>">
<input type="hidden" name="backer" value="true">
<?php
if ($typ == "Sportstaette")
	echo "<input type='hidden' name='SAs' value='".$SAs."'>";
?>
</form>

<form id="detailDelegate" action="Detailseite.php" method="GET" target="_blank">
  <input id="submitID" name="submitID" type="hidden" value="default">
  <input id="dist_km" name="dist_km" type="hidden" value="default">
  <input id="dist_h" name="dist_h" type="hidden" value="default">
  <?php
	//Ausgabe der Abweichlermatrizen	
	$kurzel = substr($typ,0,1);
	echo "<input type='hidden' name='attrListe".$kurzel."' value='".json_encode($attrListe)."' />";
	echo '<input type="hidden" id="attrEntries'.$kurzel.'" name="attrEntries'.$kurzel.'" value="default" />';	
	if ($attrListeFirst != null)
	{
		if ($kurzel == "U")
			$kurzel = "S";
		else 
			$kurzel = "U";
		echo "<input type='hidden' name='attrListe".$kurzel."' value='".json_encode($attrListeFirst)."' />";
		echo '<input type="hidden" id="attrEntries'.$kurzel.'" name="attrEntries'.$kurzel.'" value="default" />';
	}	
  ?>
  
</form>

<!--Einbinden des Javascripts-->
<SCRIPT>
function openDetail(id, km, h) {
  document.getElementById("submitID").value = id;
  document.getElementById("dist_km").value = km;
  document.getElementById("dist_h").value = h;
  var typ = id.substr(0,1);
  typ = typ.toUpperCase();
  //Übergabe der Liste der Einträge aus der Übereeinstimmungsmatrix
  var matrix = document.getElementById("attrMatrix"+typ);
  var head = document.getElementById("attrHead"+typ);
  if (matrix != null && head != null)
  {
	  matrix = JSON.parse(matrix.value);
	  head = JSON.parse(head.value);
	  var localId= id.substr(1,id.length-1);
	  var index =  head.indexOf(localId);
	  if (index != -1)
	  {
		  //Index wurde innerhalb des Arrays gefunden. -- > Stimmt mit der Spalte der Matrix überein
		  //Spalte der Matrix herauslösen
		  var col = [];
		  for(var i = 0; i < matrix.length;i++)
			  col.push(matrix[i][index]);
		  var target = document.getElementById("attrEntries"+typ);
		  if (target != null)
			  target.value = JSON.stringify(col);
		  else
			  console.log("Target Element not found");
		  
	  }
	  else
	  {
		  console.log("Nix da");
		  console.log(head);
	  }
  }
  else
	  console.log("nuller");
  
  var hiddenID = document.getElementById("detailDelegate");
  if(hiddenID != null)
  {
	  hiddenID.submit();
  }
}
//Erschaffen der Karte
var startCoordinates = document.getElementsByName("coordinates")[0].value;
var startLat = "51.03538";
var startLng = "13.74427";
if (startCoordinates != null && startCoordinates != 0)
{
	//Tauschen der Koordinaten
	var splitter = startCoordinates.split(',');
	if (splitter.length != 2)
		console.log("error"+splitter);
	else
	{
		startLat=splitter[1];
		startLng=splitter[0];
	}
}
//Festlegen des Zooms
/* Zoomstufen Heuristisch
* 5: 2000
* 6: 1000
* 7:  500
* 8:  250
* 9:  125
* 10: 62,5
*/
var zoomStufe = 7;
var maxDist = document.getElementsByName("maxDist")[0].value*2;
if (maxDist != null && maxDist != 0)
{
	if (maxDist < 62.5)
		zoomStufe = 10;
	else if (maxDist <= 125)
		zoomStufe = 9;
	else if (maxDist <= 250)
		zoomStufe = 8;
	else if (maxDist <= 500)
		zoomStufe = 7;
	else if (maxDist <= 1000)
		zoomStufe = 6;
	else
		zoomStufe = 5;
}

var stamen = new L.StamenTileLayer("terrain");
var osm = new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'});
var ors = new L.TileLayer('https://api.openrouteservice.org/mapsurfer/{z}/{x}/{y}.png?api_key=5b3ce3597851110001cf6248b271a93676ef45f3aba701b29ded2d93');

//Kartenlayer hinzufügen
var map = new L.Map("mapid",
{
	center: new L.LatLng(startLat,startLng),
	zoom: zoomStufe,
	layers: [stamen]

});
var baseMaps =
{
	'<span style="text-align:left;">Stamen         </span>': stamen,
	"Open Street Map":osm
}
L.control.layers(baseMaps).addTo(map);
//map.addLayer(layer);

//Einen Maßstab der Karte hinzufügen
L.control.scale({imperial: false}).addTo(map);

//Start Icon definieren.
var startIcon = L.icon({
	iconUrl: '../Marker/markerStart7.png',
	shadowUrl: 'leaflet/images/marker-shadow.png',
    iconSize:     [25, 35], // size of the icon
    shadowSize:   [25, 35], // size of the shadow
    iconAnchor:   [0, 40] // point of the icon which will correspond to marker's location
    
	
});
console.log(L.Icon.Default.prototype.options);
var startmarker = L.marker([startLat,startLng],{icon: startIcon});
startmarker.addTo(map);
//Darstellen des gesuchten Umkreises in der Luftlinine
var circle = L.circle([startLat,startLng],{
	color: 'yellow',
	radius: document.getElementsByName("maxDist")[0].value*1000,
	fillOpacity: 0.1,
	fillColor: '#fff'
}).addTo(map);
</script>
<script src="functions/ErgebnisFunctions.js"></script>
<script>
function callAction(UorS)
	{
		//Falls wir eine Änderung haben

			//Unterscheide zwischen Gruppierung nach Unterkunft und Sportstätten
			if(UorS == "U")
				displayListElements(true);
			else
				displayListElements(false);
		
		
	}

</script>

</BODY>
</HTML>
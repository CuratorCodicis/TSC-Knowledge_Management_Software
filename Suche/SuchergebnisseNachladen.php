<?php
include('../database.php');
require("../APIKey.php");
$conn = getConnection();

$maxDist = $_GET["maxDist"];
$ID = $_GET["id"];
$typ = $_GET["typ"];
$coords = $_GET["coords"];
$cat = $_GET["cat"];
$overwriteDriving = false;
$ForeignID = "0";
if (isset($_GET["FID"]))
	$ForeignID = $_GET["FID"];
if(isset($_GET["overwriteDriving"])) $overwriteDriving = true;

if ($ID != null)
{
	//Unterscheidung in Sportstaette und Unterkunft fuer die Anfrage
	if ($typ == "Sportstaette")
	{
		$query = "SELECT * FROM Sportstaette WHERE ID =".$ID;
		$typ = "s";	//zum Vergeben von IDs, spaeter fuer Ankerpunkte bei Map wichtig
	}
	else if ($typ == "Unterkunft")
	{
		$query = "SELECT * FROM Unterkunft WHERE ID =".$ID;
		$typ = "u";
	}
	
	$result = $conn->query($query);
	$counter = 0;
	if ($result != null && $result->num_rows > 0)
	{			
		while ($row = $result->fetch_assoc())
		{
			//Überprüfen der Suchergebnisse auf die gewünschte Distanz
			$kilom = "y";
			$time = "x";
			$hours = "";
			$min = "";
			//Beschränkung auf 30 Anfragen -- VERALTET
			$cont = 1;
			if ($counter <= 30)
			{
				$myCoords = $row['Koordinaten'];				
				$res = null;
				if ($myCoords != Null && $cont == 1)
				{
					$res = GetRoute($coords,$myCoords,$overwriteDriving);
					$counter++;
				}
				if ($res != Null && $cont == 1)
				{
					$kilom = $res->{'distance'};
					$time = $res->{'duration'};
				
					$kilom = $kilom/1000;
					$kilom = round($kilom,0);
					$time = $time/60/60;
					$time = round($time,2);
					$min = (($time*100)%100)/100;
					$hours = round($time - $min,0);
					$min = round($min * 60,0);
					
				}								
			}			
			if ($kilom <= $maxDist && $cont == 1)
			{
				if($hours == 0)
				{
					//Ausgabe der Suchergebnisse ohne Stunde
					$ret = array('zeit' => $time,
					'inhalt' =>
					"<div class='Ergebnis' id=".$typ.$ID." onclick=openDetail('".$typ.$ID."','".$kilom."','".$hours."_".$min."') onmouseover=TurnMarkerOn('".$myCoords."') onmouseout=TurnMarkerOff('".$myCoords."')><i style='font-size:24px;float:right;' class='fas'>&#xf05a;</i><h3>".$row['Name']."</h3>
					<div class='ErgebnisOrt'> ".$row['Postleitzahl']." ".$row['Ort']."</div>
					<div class='ErgebnisEntfernung'>".$kilom." km</div><div class='ErgebnisZeit'> ".$min." min</div></div> ",
					'coords' => $myCoords,
					'id' => $typ.$ID,
					'cat' => $cat,
					'FID' => $ForeignID
					);
					echo json_encode($ret);
				}else
				{
					//Ausgabe der Suchergebnisse mit Stunde
					$ret = array('zeit' => $time,
					'inhalt' =>
					"<div class='Ergebnis' id=".$typ.$ID." onclick=openDetail('".$typ.$ID."','".$kilom."','".$hours."_".$min."') onmouseover=TurnMarkerOn('".$myCoords."') onmouseout=TurnMarkerOff('".$myCoords."')><i style='font-size:24px;float:right;' class='fas'>&#xf05a;</i><h3>".$row['Name']."</h3>
					<div class='ErgebnisOrt'> ".$row['Postleitzahl']." ".$row['Ort']."</div>
					<div class='ErgebnisEntfernung'>".$kilom." km</div><div class='ErgebnisZeit'> ".$hours." h ".$min." min</div></div> ",
					'coords' => $myCoords,
					'id' => $typ.$ID,
					'cat' => $cat,
					'FID' => $ForeignID
					);
					echo json_encode($ret);
				}
			}
			else
			{
				if ($cont == 0)
					echo("c");
				else
					echo ("0");
			}
		}		
	}
	else echo ("0");
}
else
{
	echo ("0");
}


function GetRoute($startCoords,$targetCoords, $overwriteD)
{	
	$profile = "driving-car";
	if($overwriteD) $profile ="foot-walking";
	$query = http_build_query(
		array(
			'api_key' => GetAPIKey(),
			'coordinates' => $startCoords.'|'.$targetCoords,
			'profile' => $profile,
			'instructions' => 'false'
		)
	);

	$options = array('http' =>
		array(
			'method'  => 'GET',
			'header'  => 'Content-type: application/x-www-form-urlencoded'
		)
	);

	$res = json_decode(file_get_contents('https://api.openrouteservice.org/directions?' . $query, false, stream_context_create($options)));
	$test = $res->{'routes'}[0]->{'summary'};
	return $test;
}

$conn->close();
?>
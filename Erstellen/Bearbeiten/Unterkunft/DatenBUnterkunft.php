<?php

session_start();
require("../../../APIKey.php");

?>

<html>
<head>
  <title>Daten</title>
  <meta charset="UTF-8">
  <LINK rel="stylesheet" href="\general.css">
  <LINK rel="stylesheet" href="\colors.css">
  <LINK rel="stylesheet" href="../../Anlegenstyle.css">
  <LINK rel="stylesheet" href="\Icons/css/all.css">
  <style>
  .reset{
	  float:left;
	  margin-left:10%;
  }
  </style>
</head>
<body>

<script>
function leave_site() {
	window.location.replace("../../Homebutton.php");
}
</script>

<div class="color" id="colorU1">
<button type="button" onclick="leave_site()" class="HomeB">Home</button></br></br>
<h1>Unterkunft bearbeiten</h1></br></br>
</div>
<div class="circle"><i style='font-size:50px;margin-top:35%;color:grey;' class='far'>&#xf044;</i></div>


<?php

include('../../../database.php');
$conn = getConnection();

//check required fields
$NameErr = $StrErr = $PlzErr = $OrtErr = $LandErr = "";
$Name = $TelNum = $EMail = $Internet = $Str = $Hausnummer = $Plz = $Ort = $Land = "";
$NameP = $TelNumP = $EMailP = $InternetP = $StrP = $HausnummerP = $PlzP = $OrtP = $LandP = "";
$ID = $_SESSION["UID"];

$sql = 'SELECT * FROM unterkunft WHERE ID='.$ID.';';
$result = $conn->query($sql);

if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}

while ($row=$result->fetch_assoc()) {
	$NameP = $row["Name"];
	$TelNumP = $row["Telefonnummer"];
	$EMailP = $row["MailAdresse"];
	$InternetP = $row["Internetseite"];
	$StrP = $row["Strasse"];
	$HausnummerP = $row["Hausnummer"];
	$PlzP = $row["Postleitzahl"];
	$OrtP = $row["Ort"];
	$LandP = $row["Land"];
}






if($_SERVER["REQUEST_METHOD"]=="POST") {
	if(empty($_POST["Name"])) {$NameErr = "Name wird benötigt";} else {$Name = "'".$_POST["Name"]."'";$NameP = $_POST["Name"];}
	if(empty($_POST["TelNum"])){$TelNum = "NULL";} else {$TelNum = "'".$_POST["TelNum"]."'";$TelNumP = $_POST["TelNum"];}
	if(empty($_POST["EMail"])){$EMail = "NULL";} else {$EMail = "'".$_POST["EMail"]."'";$EMailP = $_POST["EMail"];}
	if(empty($_POST["Internet"])){$Internet = "NULL";} else {$Internet = "'".$_POST["Internet"]."'";$InternetP = $_POST["Internet"];}
	if(empty($_POST["Str"])) {$StrErr = "Straße wird benötigt";} else {$Str ="'". $_POST["Str"]."'";$StrP =$_POST["Str"];}
	if(empty($_POST["Hausnummer"])){$Hausnummer = "NULL";} else {$Hausnummer = "'".$_POST["Hausnummer"]."'";$HausnummerP = $_POST["Hausnummer"];}
	if(empty($_POST["Plz"])) {$Plz = "NULL";} else {$Plz = "'".$_POST["Plz"]."'";$PlzP = $_POST["Plz"];}
	if(empty($_POST["Ort"])) {$OrtErr = "Ort wird benötigt";} else {$Ort = "'".$_POST["Ort"]."'";$OrtP = $_POST["Ort"];}
	if(empty($_POST["Land"])){$Land = "DEFAULT";} else {$Land = "'".$_POST["Land"]."'";$LandP = $_POST["Land"];}
}

//Restrict area to the specific country
$countryCode = 'DE';
if (isset($_POST['Land']) && $_POST['Land'] != "" && !empty($_POST['Land']))
{
	$countryCode = GetLandCode($_POST['Land']);
}
$errorPflicht = false;
if($_SERVER["REQUEST_METHOD"]=="POST") {
	if(empty($_POST["Name"]) || empty($_POST["Str"]) || empty($_POST["Ort"]))
	{
		echo '<span class="error" style="color: red; font-size:1.4em;">Nicht alle relevanten Felder wurden ausgefüllt!</span>';
		$errorPflicht = true;
	}
	else
	{
		//Add the geolocation to the Dataset
		$query = http_build_query(
		array(
				'api_key' => GetAPIKey(),
				'text' => $_POST["Str"].' '.$_POST["Plz"].' '.$_POST["Ort"],
				'boundary.country' => $countryCode
				//'layers' => "address"

			)
		);

		if ($countryCode == "0")
		{			
			echo '<span class="error">Das angegebene Land konnte nicht gefunden werden!<br> Mögliche Länder sind am Ende der Seite aufgelistet.</span>';
			$LandErr = "Ungültige Eingabe";
		}
		else
		{
			$options = array('http' =>
				array(
					'method'  => 'GET',
					'header'  => 'Content-type: application/x-www-form-urlencoded'
				)
			);
			//Get result as JSON
			$res = json_decode(file_get_contents('https://api.openrouteservice.org/geocode/search?' . $query, false, stream_context_create($options)));
			if (count($res->{'features'})<= 0)
			{
				echo '<span class="error">Die gewünschte Adresse konnte nicht gefunden werden!</span>';
				$StrErr = "Ungültige Eingabe";
				$OrtErr = "Ungültige Eingabe";
			}
			else
			{
				$i=0;
				$found = 0;
				$error = "";
				for($i = 0;(($found == 0) && ($i < count($res->{'features'}))) ;$i++)
				{
					//Überprüfen auf den LOrt
					//TODO: PLZ fehlt, da von der Angabe nicht zurück gegeben
					//Überprüfen LOrt
					if (isset($res->{'features'}[$i]->{'properties'}->{'locality'}))
					{
						$ROrt = trim($res->{'features'}[$i]->{'properties'}->{'locality'});
						$LOrt = trim($_POST['Ort']);
						$VOrt = false;
						if (strlen($ROrt) > strlen($LOrt))
							$VOrt = strpos($ROrt,$LOrt) !== false;
						else
							$VOrt = strpos($LOrt,$ROrt) !== false;
						//echo $VOrt;
						if ($VOrt == true)			
						{
							//Überprüfen auf die Straße
							if (isset($res->{'features'}[$i]->{'properties'}->{'street'}))
							{							
								$RStr = trim($res->{'features'}[$i]->{'properties'}->{'street'});
								$LStr = trim($_POST['Str']);
								$VStr = false;
								if (strlen($RStr) > strlen($Str))
									$VStr = strpos($RStr,$Str) !== false;
								else
									$VStr = strpos($Str,$RStr) !== false;
								//echo $VStr;
								if ($VStr == true)
								{
									$found = 1;
									//echo "found";
								}
							}
							else 
								$error="Straße";
						
						}
						else if ($error != "Straße")
							$error="Ort";
					}
					else if ($error != "Straße")
						$error="Ort";
				}	
				$i--;//KOMISCH Muss wieder runter gezählt werden, da Schleife anscheinend einen weiter zählt
				//echo $i;
				if ($found == 1)
				{
					$Koords = "'".$res->{'features'}[$i]->{'geometry'}->{'coordinates'}[0].','.$res->{'features'}[$i]->{'geometry'}->{'coordinates'}[1]."'";		
					
					$sql = 'UPDATE unterkunft SET Name='.$Name.', Telefonnummer='.$TelNum.', MailAdresse='.$EMail.', Internetseite='.$Internet.', Strasse='.$Str.', Hausnummer='.$Hausnummer.', Postleitzahl='.$Plz.', Ort='.$Ort.', Land='.$Land.', Koordinaten='.$Koords.' WHERE ID='.$ID.';';
					$result = $conn->query($sql);

					if ($result === TRUE)
					{
						echo "New record created successfully";

						header('Location: AttBUnterkunft.php');
					}
					else
					{
						echo "Error: " . $sql . "<br>" . $conn->error;
					}
				}
				else
				{
					echo "Die gewünschte Adresse konnte nicht gefunden werden. Bitte überprüfen Sie ihre Eingabe bei:".$error;
				}
			}
		}
	}
}

$conn->close();

?>

<div>
<form action ="DatenBUnterkunft.php" method="Post">
<table style="width:100%">
<colgroup>
    <col style="width: 30%" />
    <col style="width: 40%" />
    <col style="width: 30%" />
  </colgroup>
<tr>
<th>Name*</th><td><input type="text" name="Name" value="<?php echo $NameP;?>" style="<?php if($errorPflicht == true){echo "border: 2px solid red;";}?>"/></td>
</tr>
<tr>
<th>Telefonnummer</th><td><input type="text" name="TelNum" value="<?php echo $TelNumP;?>"/></td>
</tr>
<tr>
<th>E-Mail Adresse</th><td><input type="text" name="EMail" value="<?php echo $EMailP;?>"/></td>
</tr>
<tr>
<th>Internetseite</th><td><input type="text" name="Internet" value="<?php echo $InternetP;?>"/></td>
</tr>
<tr>
<th>Straße*</th><td><input type="text" name="Str" value="<?php echo $StrP;?>" style="<?php if($errorPflicht == true){echo "border: 2px solid red;";}?>"/></td>
</tr>
<tr>
<th>Haus-Nr.</th><td><input type="text" name="Hausnummer" value="<?php echo $HausnummerP;?>"/></td>
</tr>
<tr>
<th>PLZ</th><td><input type="text" name="Plz" value="<?php echo $PlzP;?>"/></td><td id="span"></td>
</tr>
<tr>
<th>Ort*</th><td><input type="text" name="Ort" value="<?php echo $OrtP;?>" style="<?php if($errorPflicht == true){echo "border: 2px solid red;";}?>"/></td>
</tr>
<tr>
<th>Land</th><td><input type="text" name="Land" placeholder="Deutschland" value="<?php echo $LandP;?>"/></td>
</tr>
</table>

</br></br><input id="submitB" type = submit value="Übernehmen">
<button class="reset" type="reset" value="Reset">Änderungen zurücksetzten</button>
</br></br></br></br>
</form>
</div>

<script>
//frage nur, falls Aenderungen und nicht submittet
function sicher(e) {
	if(!window.btn_clicked && changed){
        e.preventDefault();
    }
}
document.getElementById("submitB").onclick = function(){
    window.btn_clicked = true;
};

//tracke Aenderungen
var changed = false;
var inputs = document.getElementsByTagName('input')
for (var i = 0; i < inputs.length; i++) {
	inputs[i].addEventListener("click", function(){changed = true});
}
inputs = document.getElementsByTagName('select')
for (var i = 0; i < inputs.length; i++) {
	inputs[i].addEventListener("click", function(){changed = true});
}
inputs = document.getElementsByTagName('textarea')
for (var i = 0; i < inputs.length; i++) {
	inputs[i].addEventListener("click", function(){changed = true});
}

// falls versucht wird, die Seite zu verlassen, warne vor
window.addEventListener('beforeunload', function(e){sicher(e)});
</script>


</body>
</html>
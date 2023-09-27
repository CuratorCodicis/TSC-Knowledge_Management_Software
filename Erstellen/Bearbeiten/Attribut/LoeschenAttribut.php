<?php
session_start();
?>

<HTML>
<HEAD>
<TITLE>Löschen</TITLE>
<meta charset="UTF-8">
<LINK rel="stylesheet" href="\general.css">
<LINK rel="stylesheet" href="\colors.css">
<LINK rel="stylesheet" href="\Icons/css/all.css">
<style>
table, th, td {
	margin-left: auto;
	margin-right: auto;
	margin-top: 80px;
	position:relative;
    border: none;
    border-collapse: collapse;
}
td {
    text-align: center; 	
}
th {
    text-align: center;
}
  a:link, a:visited{
    min-width:54px;
	box-sizing:content-box;
	background-color: #3c3c3c;
    color: white;
    padding: 10px 20%;
    text-align: center;
    text-decoration: none;
    display: inline-block;
	margin: 8px 0;
    border-radius: 4px;
    cursor: pointer;
}

a:hover, a:active {
    background-color: #A9A9A9;
}
.ramen
{
	text-align:center;
	margin-top: 10%;
	padding-left: 20%;
	padding-right: 20%;
}
p{
	color:black;
	font-size:25px;
}

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
</style>
</HEAD>
<body>
<script>
function leave_site() {
	window.location.replace("../../Homebutton.php");
}
</script>

<div class="color" id="colorSuche">
<button type="button" onclick="leave_site()" class="HomeB">Home</button></br></br>
<h1><b></br></b></h1></br></br>
</div>
<div class="circle"><i style='font-size:50px;margin-top:35%;margin-left:37%;color:grey;' class='far'>&#xf044;</i></div>
<div class="ramen">
<p>Sind Sie sicher, dass Sie das Attribut löschen wollen?</br>
Alle Zuordnungen zu diesem Attribut werden unwideruflich gelöscht!</p>
</div>

<form action ="LoeschenAttribut.php" method="Post">
<table style="width:40%">
<colgroup>
    <col style="width: 50%" />
    <col style="width: 50%" />
</colgroup>
<div class="Buttons">
<tr>
<th><a href="UebersichtAttribut.php">Zurück</a></th>
<td><input id="submitB" type = submit value="Löschen" style="margin-right:0;float:none;padding:10px 20%;font-family: Verdana, Helvetica, Arial, sans-serif;background-color:red;"></td>
</tr>
</table>
</form>

<?php
include('../../../database.php');
$conn = getConnection();

$object = substr($_SESSION["att"],0 , 1);
$att = substr($_SESSION["att"],1);

if($_SERVER["REQUEST_METHOD"]=="POST"){
	//delete from DB
	if($object == "U"){
		//delete int
		$sql = 'DELETE FROM ubesitzt_int WHERE AName="'.$att.'"';
		$result = $conn -> query($sql);
		
		if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
		
		//delete bool
		$sql = 'DELETE FROM ubesitzt_bool WHERE AName="'.$att.'"';
		$result = $conn -> query($sql);
		
		if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
		
		//delete char
		$sql = 'DELETE FROM ubesitzt_char WHERE AName="'.$att.'"';
		$result = $conn -> query($sql);
		
		if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
		
		//delete Auswahlwerte
		$sql = 'DELETE FROM attributefuerunterkunftauswahlwerte WHERE AName="'.$att.'"';
		$result = $conn -> query($sql);
		
		if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
		
		//delete Attribut
		$sql = 'DELETE FROM attributefuerunterkunft WHERE AName="'.$att.'"';
		$result = $conn -> query($sql);
		
		if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
	}
	if($object == "S"){
		//delete int
		$sql = 'DELETE FROM ssbesitzt_int WHERE AName="'.$att.'"';
		$result = $conn -> query($sql);
		
		if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
		
		//delete bool
		$sql = 'DELETE FROM ssbesitzt_bool WHERE AName="'.$att.'"';
		$result = $conn -> query($sql);
		
		if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
		
		//delete char
		$sql = 'DELETE FROM ssbesitzt_char WHERE AName="'.$att.'"';
		$result = $conn -> query($sql);
		
		if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
		
		//delete Auswahlwerte
		$sql = 'DELETE FROM attributefuersportstaetteauswahlwerte WHERE AName="'.$att.'"';
		$result = $conn -> query($sql);
		
		if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
		
		//delete Zuordnung
		$sql = 'DELETE FROM zuordnungsaa WHERE AName="'.$att.'"';
		$result = $conn -> query($sql);
		
		if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
		
		//delete Attribut
		$sql = 'DELETE FROM attributefuersportstaette WHERE AName="'.$att.'"';
		$result = $conn -> query($sql);
		
		if ($result === FALSE) {echo "Error: " . $sql . "<br>" . $conn->error;}
	}
	
	session_unset();
	session_destroy();
	
	header("Location: \Verwaltung.php");
}

?>

</div>
</body>
</HTML>
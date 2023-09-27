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
    text-align: center;
    text-decoration: none;
    display: inline-block;
	margin: 8px 0;
    border-radius: 4px;
    cursor: pointer;
	padding:10px 20%;
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
<p>Sind Sie sicher, dass Sie die Unterkunft löschen wollen?</br>
Alle zur Unterkunft zugehörigen Daten werden unwideruflich gelöscht!</p>
</div>

<form action ="LoeschenUnterkunft.php" method="Post">
<table style="width:40%">
<colgroup>
    <col style="width: 50%" />
    <col style="width: 50%" />
</colgroup>
<tr>
<th><a href="UebersichtUnterkunft.php">Zurück</a></th>
<td><input id="submitB" type = submit value="Löschen" style="margin-right:0;float:none;padding:10px 20%;font-family: Verdana, Helvetica, Arial, sans-serif;background-color:red;"></td>
</tr>
</table>
</form>

<?php
include('../../../database.php');
$conn = getConnection();

$ID = $_SESSION["UID"];

if($_SERVER["REQUEST_METHOD"]=="POST"){
	//delete from DB
	$sql = 'DELETE FROM ubesitzt_int WHERE UID='.$ID.';';
	$result = $conn -> query($sql);
	
	if($result === false) {echo "FEHLER: ".$conn->error;}
	
	$sql = 'DELETE FROM ubesitzt_bool WHERE UID='.$ID.';';
	$result = $conn -> query($sql);
	
	if($result === false) {echo "FEHLER: ".$conn->error;}
	
	$sql = 'DELETE FROM ubesitzt_char WHERE UID='.$ID.';';
	$result = $conn -> query($sql);
	
	if($result === false) {echo "FEHLER: ".$conn->error;}
	
	$sql = 'DELETE FROM kontakte_unterkunft WHERE UID='.$ID.';';
	$result = $conn -> query($sql);
	
	if($result === false) {echo "FEHLER: ".$conn->error;}
	
	$sql = 'DELETE FROM unterkunft WHERE ID='.$ID.';';
	$result = $conn -> query($sql);
	
	if($result === false) {echo "FEHLER: ".$conn->error;}
	
	session_unset();
	session_destroy();
	
	header("Location: \Verwaltung.php");
}

?>

</div>
</body>
</HTML>
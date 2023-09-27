<!DOCTYPE HTML>
<HTML>
<HEAD>
<TITLE>Datenverwaltung</TITLE>
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
	width: 90%;
    background-color: Transparent;
    color: black;
    padding: 14px 25px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
	margin: 8px 0;
    border: 2px solid black;
    border-radius: 4px;
    cursor: pointer;
}

a:hover, a:active {
    background-color: #A9A9A9;
}
.ramen
{
	margin: 2%;
	padding-left: 20%;
	padding-right: 20%;
}
.Bearbeiten
{
	width: 50%;
	float: left;
	position: relative;
	
}
.Anlegen
{
	width: 50%;
	float: left;
	position: relative;
}
p{
	color:black;
	font-size:30px;
	margin-left:9%
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
	window.location.replace("Erstellen/Homebutton.php");
}
</script>

<div class="color" id="colorSuche">
<button type="button" onclick="leave_site()" class="HomeB">Home</button></br></br>
<h1><b></br></b></h1></br></br>
</div>
<div class="circle"><i style='font-size:50px;margin-top:35%;margin-left:37%;color:grey;' class='far'>&#xf044;</i></div>
<div class="ramen">
<div class="Bearbeiten">
<p>Zum Bearbeiten</p>
<table style="width:90%">
<colgroup>
    <col style="width: 90%" />
</colgroup>
	<tr><th><a href="Erstellen/Bearbeiten/Auswahlseite.php?typ=U">Unterkunft bearbeiten</a></th><td></td></tr>
	<tr><th></br></th><td></br></td><td></br></td></tr>
	<tr><th><a href="Erstellen/Bearbeiten/Auswahlseite.php?typ=S">Sportst&auml;tte bearbeiten</a><th><td></td></tr>
	<tr><th></br></th><td></br></td><td></br></td></tr>
	<tr><th><a href="Erstellen/Bearbeiten/Auswahlseite.php?typ=K">Kontaktperson bearbeiten</a><th><td></td></tr>
	<tr><th></br></th><td></br></td><td></br></td></tr>
	<tr><th><a href="Erstellen/Bearbeiten/Auswahlseite.php?typ=A">Attribut bearbeiten</a><th><td></td></tr>
	<tr><th></br></th><td></br></td><td></br></td></tr>
	<tr><th><a href="Erstellen/Sportart.php?typ=b">Sportarten bearbeiten</a><th><td></td></tr>
</table></div>
<div class="Anlegen">
<p>Zum Anlegen</p>
<table style="width:90%">
<colgroup>
    <col style="width: 90%" />
</colgroup>
    <tr><th><a href="Erstellen/Unterkunft/UnterkunftStart.php">Unterkunft anlegen</a></th><td></td></tr>
	<tr><th></br></th><td></br></td><td></br></td></tr>
    <tr><th><a href="Erstellen/Sportstätte/SportstaetteStart.php">Sportstätte anlegen</a></th><td></td></tr>
	<tr><th></br></th><td></br></td><td></br></td></tr>
	<tr><th><a href="Erstellen/Kontaktperson/KontaktStart.php">Kontaktperson anlegen</a></th><td></td></tr>
	<tr><th></br></th><td></br></td><td></br></td></tr>
	<tr><th><a href="Erstellen/Attribut/AttributStart.php">Attribut anlegen</a></th><td></td></tr>
	<tr><th></br></th><td></br></td><td></br></td></tr>
	<tr><th><a href="Erstellen/Sportart.php?typ=a">Sportart anlegen</a></th><td></td></tr>
</table></div>
</div>
</body>
</HTML>
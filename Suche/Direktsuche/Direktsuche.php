<!DOCTYPE HTML>
<HTML>
<HEAD>
<TITLE>Direktsuche</TITLE>
<meta charset="UTF-8">
<LINK rel="stylesheet" href="..\..\general.css">
<LINK rel="stylesheet" href="..\..\colors.css">
<LINK rel="stylesheet" href="..\..\Icons/css/all.css">
<style>
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

.search{
	width:60%;
	margin-left:auto;
	margin-right:auto;
	margin-top:15%
}
form.example input[type=text] {
  padding: 10px;
  font-size: 17px;
  border: 1px solid grey;
  float: left;
  width: 100%;
  background:white;
}

form.example button {
  width: 50%;
  float:left;
  padding: px;
  background: #5F9EA0;
  font-size: 17px;
  border: 1px solid grey;
  cursor: pointer;
}

form.example button:hover {
  background: #9fc4c6;
}

form.example::after {
  content: "";
  clear: both;
  display: table;
}
  table, th, td {
    border: none;
    border-collapse: collapse;
	margin-left: auto;
	margin-right:auto;
}
td {
    text-align: center; 	
}
th {
    text-align: center;
}
</style>
</HEAD>
<body>

<script>
function leave_site() {
	window.location.replace("../../Index.php");
}
</script>

<div class="color" id="colorSuche">
<button type="button" onclick="leave_site()" class="HomeB">Home</button></br></br>
<h1><b></br></b></h1></br></br>
</div>
<div class="circle"><i style='font-size:50px;margin-top:35%;margin-left:37%;color:grey;' class='fas'>&#xf5eb;</i></div>

<div class="search">
<form class="example" action="Direktergebnis.php" method="POST">
<table id="Search" style="width:100%">
<colgroup>
    <col style="width: 40%" />
    <col style="width: 40%" />
	<col style="width: 20%" />
  </colgroup>
<tr>
  <th><input type="text" placeholder="Straße oder PLZ oder Ort oder..." name="search2" <?php if (isset($_POST['searchInput'])) echo 'value="'.$_POST['searchInput'].'"'?> ></th>
	<td><select name="wähle" style="padding: 9px;border: 1px solid grey;">
		<option value="Alle">Alle</option>
		<option value="Sportstätte" <?php if(isset($_POST['wahl']) && $_POST['wahl'] == "Sportstätte") echo "selected" ?>>Sportstätte</option>
		<option value="Unterkunft" <?php if(isset($_POST['wahl']) && $_POST['wahl'] == "Unterkunft") echo "selected" ?>>Unterkunft</option>
		<option value="Kontaktperson" <?php if(isset($_POST['wahl']) && $_POST['wahl'] == "Kontaktperson") echo "selected" ?>>Kontaktperson</option>
		</select>
  <td><button type="submit" name="submit"><i class="fa fa-search"></i></button></td>
</tr></table></div>
</form>
</body>
</HTML>
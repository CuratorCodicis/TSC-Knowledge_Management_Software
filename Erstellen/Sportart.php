<?php
require('../database.php');
$typ = $_GET["typ"];
?>

<!DOCTYPE html>
<html>
<head>
  <title><?php if($typ=="a") echo "Sportart anlegen"; else echo "Sportarten bearbeiten";?></title>
  <meta charset="UTF-8">
  <LINK rel="stylesheet" href="\general.css">
  <LINK rel="stylesheet" href="\colors.css">
  <LINK rel="stylesheet" href="Anlegenstyle.css">
 <LINK rel="stylesheet" href="\Icons/css/all.css">
</head>
<body>

 <script>
function leave_site() {
	window.location.replace("../Index.php");
}
</script>
 <?php
// Create and check connection
$conn = getConnection();

if($_SERVER["REQUEST_METHOD"]=="POST") {
	if ($_POST["typ"] == "a")
	{
		$i=0;
		$col = "neueSArt".strval($i);
		while(isset($_POST[$col]) && $_POST[$col] != "")
		{
			$sql= "INSERT INTO sportart VALUES('".$_POST[$col]."');";
			$conn->query($sql);
			echo $sql;
			$i++;
			$col = "neueSArt".strval($i);
		}
	}
	if ($_POST["typ"] == "b")
	{
		$i=0;
		$col = "aendereSArt".strval($i);	
		while(isset($_POST[$col]))
		{
			if($_POST[$col] == "n")
			{
				//Die SArt wird nicht gelöscht, sondern geändert
				if (trim($_POST["alteSArt".$i]) != "" && (trim($_POST["alteSArt".$i]) != $_POST["originalSArt".$i]))
				{
					$sql='UPDATE sportart SET Name="'.$_POST["alteSArt".$i].'" WHERE Name="'.trim($_POST["originalSArt".$i]).'"';
					$conn->query($sql);
				}
			}
			else
			{
				//Die Sportart muss gelöscht werden
				$sql='DELETE FROM sportart WHERE Name="'.trim($_POST["originalSArt".$i]).'"';
				$conn->query($sql);
			}
			$i++;
			$col = "aendereSArt".strval($i);
		}
	}
	
	header('Location: ../Verwaltung.php');

}
?>
<div class="color" id="colorSuche">
<button type="button" onclick="leave_site()" class="HomeB">Home</button></br></br>
<h1><?php if($typ=="a") echo "Neue Sportart anlegen"; else echo "Sportarten bearbeiten";?></h1></br></br>
</div>
<div class="circle"><i style='font-size:50px;margin-top:35%;color:grey;' class='fas'><?php if($typ=="a") echo '&#xf65e;'; else echo '&#xf044;'; ?></i></div>
<div>
<form action ="Sportart.php" method="Post">
<table style="width:100%">
<colgroup>
    <col style="width: 30%" />
    <col style="width: 40%" />
    <col style="width: 30%" />
  </colgroup>

<?php
$sql = "SELECT * FROM sportart;";
$result = $conn ->query($sql);
$firstLetter = null;
$i = 1;

if ($result != null && $result->num_rows > 0)
{					
	while ($row = $result->fetch_assoc())
	{
		if($firstLetter != strtoupper(substr($row['Name'], 0, 1))){
			$firstLetter = strtoupper(substr($row['Name'], 0, 1));
			echo '<tr><th>'.$firstLetter.'</th><td><input type="text" name="alteSArt'.($i-1).'" value="'.$row['Name'].'"';
		}else{
			echo '<tr><th></th><td><input type="text" name="alteSArt'.($i-1).'" value="'.$row['Name'].'"';
		}
		
		if ($typ == "a") echo "disabled";
		echo '/> <input type="hidden" name="originalSArt'.($i-1).'" value="'.$row['Name'].'"/> </td>';
		if ($typ == "b")
		{
			echo '<td style="text-align:left;vertical-align:middle;"><button type="button" class="delete"><i class="fas fa-trash"></i></button><input type="hidden" name="aendereSArt'.($i-1).'" value="n"/></td>';
		}
		echo '</tr>';
		$i++;
	}
	
}
//Einfügen von der Möglichkeit neue Sportarten hinzu zu fügen
if ($typ == "a")
{
	echo '<tr><th></th><td><input type="text" name="neueSArt0"/></td><td style="text-align:left;vertical-align:top;"><button type="button" class="adder" style="margin:2px;">+</button><button type="button" class="miner">-</button></td></tr>';
}
?>
</table>
<input type="hidden" name="typ" value="<?php if($_GET["typ"] == "a") echo "a"; else echo"b"; ?>"/>
<input id="submitB" type = submit value="Speichern">
</br></br></br></br>
</form>
</div>


 <?php
$conn->close();
?>

<script>
var clicker = document.getElementsByClassName("adder");
var lastElementID = null;
for(i = 0; i < clicker.length;i++)
{
	clicker[i].addEventListener('click',function(e){
	e = e || window.event;
	var target = e.target || e.srcElement;
	var locid = target.id;
	//Navigate to source select
	var source = target.parentNode.previousSibling.lastChild;
	var j = parseInt(source.name.substr(8,source.name.length-8))+1;
	var newNode = document.createElement("input");
	newNode.type = "text";
	newNode.name = "neueSArt"+j;
	var newRow = document.createElement("tr");
	if (lastElementID == null)
	{
		lastElementID = parseInt(source.parentNode.parentNode.firstChild.innerHTML);
		lastElementID++;
	}
	else
		lastElementID++;
	//Bewege die Buttons in das die neue Zeile
	newRow.innerHTML = '<th></th><td></td>';
	newRow.childNodes[1].appendChild(newNode);
	newRow.appendChild(target.parentNode);
	source.parentNode.parentNode.parentNode.append(newRow);
	newNode.addEventListener("click", function(){changed = true});
},false);
}
var clicker = document.getElementsByClassName("miner");
for(i = 0; i < clicker.length;i++)
{
	clicker[i].addEventListener('click',function(e){
	e = e || window.event;
	var target = e.target || e.srcElement;
	var locid = target.id;
	//Navigate to source select
	var source = target.parentNode.previousSibling.lastChild;
	var j = parseInt(source.name.substr(8,source.name.length-8));
	if(j != 0)
	{
		//Bewege die Buttons zurück
		source.parentNode.parentNode.previousSibling.appendChild(source.parentNode.nextSibling);
		//Lösche die eingegeben Zeile
		source.parentNode.parentNode.parentNode.removeChild(source.parentNode.parentNode.parentNode.lastChild);
		lastElementID--;
	}	
},false);
}

var clicker = document.getElementsByClassName("delete");
for(i = 0; i < clicker.length;i++)
{
	clicker[i].addEventListener('click',function(e){
	e = e || window.event;
	var target = e.target || e.srcElement;
	var locid = target.id;
	
	// Toggle class of delete-button icon
	// Unterscheide, ob auf Button oder icon geklickt wurde
	if(target.firstChild != null){
		var icon = target.firstChild;
	}else{
		var icon = target;
	}
	icon.classList.toggle("fa-trash");
	icon.classList.toggle("fa-trash-restore");
	
	//Navigate to source select
	// Unterscheide, ob auf Button oder icon geklickt wurde
	if(target.firstChild != null){
		var source = target.parentNode.previousSibling.firstChild;
	}else{
		var source = target.parentNode.parentNode.previousSibling.firstChild;
	}
	
	if (source.disabled == true)
	{
		source.disabled = false;
		source.style ="";
		source.parentNode.nextSibling.lastChild.value ="n";
	}
	else
	{
		source.disabled = true;
		source.style ="text-decoration: line-through";
		source.parentNode.nextSibling.lastChild.value ="y";
	}
	
	
},false);
}

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
var deleter = document.getElementsByClassName("delete");
for (var i = 0; i < deleter.length; i++) {
	deleter[i].onclick = function(){
		changed = true;
	};
}
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

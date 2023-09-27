<?php
include('../database.php');
$conn = getConnection();
//Updaten der Kommentare
$typ = $_POST['typ'];
$text = $_POST['text'];
$ID = $_POST['ID'];
$Art = $_POST['Art'];

if ($typ == NULL || $text == NULL || $ID == NULL || $Art == NULL)
	echo "FALSE";
else
{
	if ($Art == "Kommentar")
	{
		if ($text == "")
			$text = "NULL";
		
		$sql = "UPDATE ";
		if ($typ == "u")
			$sql .= "unterkunft ";
		else if ($typ == "s")
			$sql .= "sportstaette ";
		else if ($typ == "k")
			$sql .= "kontaktpersonen ";
		
		$sql .= "SET Kommentar = '".$text."' WHERE ID = ".$ID;
		
		$res = $conn->query($sql);
		if($res === false)
			echo "FEHLER: ".$conn->error;
		else
			echo "TRUE";
	}
	else if ($Art == "Preis")
	{
		if (trim($text) == "")
			$text = "NULL";
		else
		{
			$text = str_replace("\\","\\\\",$text);
			$testerText = str_replace("\\\\","",$text);
			$testerText = str_replace("|","",$testerText);
			if ($testerText == "")
				$text = "||||||||||\\\\||||||||||\\\\||||||||||";
			else
				$text = "'".$text."'";		
		}
		$sql = "UPDATE ";
		if ($typ == "u")
			$sql .= "unterkunft ";
		else if ($typ == "s")
			$sql .= "sportstaette ";
		
		$sql .= "SET KommentarPreis =".$text." WHERE ID = ".$ID;
		
		$res = $conn->query($sql);
		if($res === false)
		{
			echo "FEHLER: ".$conn->error;
			echo $sql;
		}
		else
			echo "TRUE";
	}
	else if ($Art == "AbfragePreis")
	{
		$sql = "SELECT KommentarPreis FROM ";
		if ($typ == "u")
			$sql .= "unterkunft ";
		else if ($typ == "s")
			$sql .= "sportstaette ";
		$sql.= "WHERE ID = ".$ID;
		$res = $conn->query($sql);
		$row = $res->fetch_assoc();
		
		$result = "";
		$preis = $row["KommentarPreis"];
		if($preis != null && trim($preis) != ""){
			//Dekodierung Preis-String
			//Split in Jahre - backslash muss durch backslash escaped werden, sodass da eigentlich nur einer steht
			$jahre = explode("\\", $preis);
			$keinJahr = false;
			for($i = 0; $i < sizeof($jahre); $i++)
			{
				$preisInf = explode("|", $jahre[$i]);
				// Ausgabe Jahr
				if (trim($preisInf[0]) == "")
				{
					$keinJahr = true;
					$result .=  '<tr><th><h4 style="text-align: left;font-size: initial;"></h4></th><td></td></tr>'.
						'<tr><th></th><td></td></tr>';
				}
				else
					$result .=  '<tr><th><h4 style="text-align: left;font-size: initial;">'.$preisInf[0].'</h4></th><td></td></tr>';
				// Ausgabe Gruppen + Preis
				$ausgabe = false;
				$inner = "";
				for($j = sizeof($preisInf)-2; $j >= 0; $j=$j-2)
				{
					if (trim($preisInf[$j]) != "" || $ausgabe)
					{
						$inner = '<tr><th>'.$preisInf[$j].'</th><td>'.$preisInf[$j+1].'</td></tr>' . $inner;
						$ausgabe = true;
					}
				}
				if(!$ausgabe)
				{
					if (!$keinJahr)
						$result .= '<tr><th colspan="2" style="font-weight: normal;">Keine Preisinformationen für dieses Jahr angegeben.</th></tr>';
				}
				else
					$result .= $inner;
				$result .= '<tr style="height: 20px !important;"></tr>';
				
			}
			
		}else{
			$result = '<p class="empty" id="noPrices">Es sind keine Preisinformationen vorhanden.</p>';
		}
		echo $result;
	}
	
}

$conn->close();
?>
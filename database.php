<?php

 $servername="localhost";
 $userName="root";
 $password="";
 $dbName ="redbottledb";


function getConnection()
{
	global $servername,$userName,$password,$dbName;
	$conn = new mysqli($servername,$userName,$password,$dbName);
	if ($conn->connect_error)
	{
		die("Connection failed: ".$conn->connect_error);
	}
	else
		return $conn;
}




?>
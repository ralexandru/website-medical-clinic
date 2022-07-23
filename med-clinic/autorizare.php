<?php


if($_SESSION["keyadmin"] != session_id())
	{
		echo "Acces neautorizat !  
				<a href=\"autentificare.php\"> Login </a>";
		exit;
	}

include("connect.php");

$user = $_SESSION["nume_utilizator"];
$passCript = $_SESSION["passCript"];

$sql = "SELECT * FROM utlizatori 
			WHERE nume_utilizator = '$user' AND parola = '$passCript' ";

$result = mysqli_query($link,$sql);

if(mysqli_num_rows($result) != 1)
{
	echo "<center><h1>Datele de conectare au fost modificate.</h1> <br /> 
			<h3><a href=\"logout.php\"> Conectează-te din nou </a></h3></center>";
	exit;	
}
$row = mysqli_fetch_array($result);
if($row["nivel_administrare"] == 2){
	echo "<center><h1>Accesul dvs. a fost restrictionat.</h1> <br /> 
			<h3><a href=\"logout.php\"> Reveniti la pagina principala! </a></h3></center>";
	exit;	
}


?>

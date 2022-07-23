<?php
$link = mysqli_connect("localhost","root","root","proiectweb");

if(mysqli_connect_errno())
{
	echo "Eroare la conexiunea cu BD: ".mysqli_connect_error();
	exit;
}
?>
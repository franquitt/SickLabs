<?php
session_start();
if(!isset($_SESSION['usuario']))
{
	header("Location: login.php");
}
else{
	include('seguridad.php');
	$coneccion = conectardb();
	$idUser = $_SESSION['usuario'];
	$stmt = $coneccion->prepare("SELECT * FROM users WHERE id = ?;");
	$stmt->bind_param('i', $idUser);
	$stmt->execute();
	$result = $stmt->get_result();
	$user=mysqli_fetch_array($result);
	if(isset($_GET["name"])){
		$name = $_GET["name"];
		$stmt = $coneccion->prepare('SELECT * FROM alianzas WHERE nombre = ?');
		$stmt->bind_param('s', $name);
		$stmt->execute();
		$result = $stmt->get_result();
		
		$countlabs=mysqli_num_rows($result);
		echo $countlabs;
	}else
	echo "0";
	
}

?>
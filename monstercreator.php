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
	$consulta=mysqli_query($coneccion, "SELECT * FROM users WHERE id = '$idUser';");
	$user=mysqli_fetch_array($consulta);
	$consulta=mysqli_query($coneccion, "SELECT * FROM (SELECT id AS idciu FROM ciudades WHERE owner='$idUser') AS CIUDAD, labs WHERE ciudad=CIUDAD.idciu;");
	$ciudad=mysqli_fetch_array($consulta);
	$countciudades=mysqli_num_rows($consulta);
	if($countciudades!=1){
		echo "Tienes mas de un laboratorio!";
	}
	$idcity=$ciudad['id'];
	$tipomonstruo="";
	if(isset($_GET["tipo"])){
		$tipomonstruo = $_GET["tipo"];
	}
	switch($tipomonstruo){
		case "bug":
			mysqli_query($coneccion, "INSERT INTO monstruos(lab, tipo, evasivo, veneno, velocidad) VALUES('$idcity', '1', '2', '2', '1');");
			echo "home";
			break;
		case "demon":
			mysqli_query($coneccion, "INSERT INTO monstruos(lab, tipo, garras, resistencia, velocidad) VALUES('$idcity', '2', '2', '2', '1');");
			echo "home";
			break;
		case "wild":
			mysqli_query($coneccion, "INSERT INTO monstruos(lab, tipo, garras, velocidad, escamas) VALUES('$idcity', '3', '2', '2', '1');");
			echo "home";
			break;
		case "element":
			mysqli_query($coneccion, "INSERT INTO monstruos(lab, tipo, fuerza, resistencia, evasivo) VALUES('$idcity', '4', '2', '2', '1');");
			echo "home";
			break;
		case "beast":
			mysqli_query($coneccion, "INSERT INTO monstruos(lab, tipo, fuerza, escamas, garras) VALUES('$idcity', '5', '2', '2', '1');");
			echo "home";
			break;
		default:
			echo "Error, no existe el tipo de monstruo";
			break;
		
	}
	
}

?>
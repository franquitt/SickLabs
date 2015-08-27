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
	if(isset($_GET["name"])&&isset($_GET["alianza"])){
		$name = $_GET["name"];
		$alianza = $_GET["alianza"];
		$stmt = $coneccion->prepare('SELECT * FROM miembros WHERE iduser = ? AND idalianza = ?');
		$stmt->bind_param('ii', $idUser, $alianza);
		$stmt->execute();
		$result = $stmt->get_result();
		$countmember=mysqli_num_rows($result);
		$mimembresia=mysqli_fetch_array($result);
		if($countmember==0)
			echo "Se produjo un problema al corroborar tus credenciales con esta alianza";
		else{
			if($mimembresia["rango"]==1||$mimembresia["rango"]==2){
				$stmt = $coneccion->prepare('SELECT * FROM users WHERE nickname = ?');
				$stmt->bind_param('s', $name);
				$stmt->execute();
				$result = $stmt->get_result();
				$hisuser=mysqli_fetch_array($result);
				$stmt = $coneccion->prepare('SELECT * FROM miembros WHERE iduser = ? AND idalianza = ?');
				$stmt->bind_param('ii', $hisuser["id"], $alianza);
				$stmt->execute();
				$result = $stmt->get_result();
				$countmember=mysqli_num_rows($result);
				if($countmember==0){
					$stmt = $coneccion->prepare('INSERT INTO miembros(iduser, idalianza) VALUES(?, ?)');
					$stmt->bind_param('ii', $hisuser["id"], $alianza);
					$stmt->execute();
					echo $name." ha sido invitado a tu alianza.";
				}else
					echo "Este usuario ya es parte o ya ha sido invitado a esta alianza";
			}
				
			else
				echo "Se produjo un problema al corroborar tus credenciales con esta alianza";

		}
		/*
		$stmt = $coneccion->prepare('INSERT INTO alianzas(id, nombre) VALUES(?, ?)');
		$name="jejoxx";
		$id=5;
		$stmt->bind_param('is', $id, $name);
		$stmt->execute();
*/
	}else
	echo "Se produjo un problema obteniendo los parametros necesarios";
	
}

?>
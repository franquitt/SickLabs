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
	if(isset($_GET["alianza"])){
		$alianza = $_GET["alianza"];
		$stmt = $coneccion->prepare('SELECT * FROM miembros WHERE iduser = ? AND idalianza = ?');
		$stmt->bind_param('ii', $idUser, $alianza);
		$stmt->execute();
		$result = $stmt->get_result();
		$countmember=mysqli_num_rows($result);
		$mimembresia=mysqli_fetch_array($result);
		if($countmember==0)
			echo "No eras parte ni habias sido invitado a esta alianza";
		else{
			if($mimembresia["rango"]==1){
				
				$stmt = $coneccion->prepare('DELETE FROM miembros WHERE idalianza = ?');
				$stmt->bind_param('i',$alianza);
				$stmt->execute();
				$stmt = $coneccion->prepare('DELETE FROM alianzas WHERE id = ? LIMIT 1');
				$stmt->bind_param('i',$alianza);
				$stmt->execute();
				echo "exito";
			}else{
				$stmt = $coneccion->prepare('DELETE FROM miembros WHERe iduser = ? AND idalianza = ? LIMIT 1');
				$stmt->bind_param('ii', $idUser, $alianza);
				$stmt->execute();
				echo "exito";
			}
		}
	}else
		echo "Se produjo un problema obteniendo los parametros necesarios";	
}

?>
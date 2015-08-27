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
	if(isset($_GET["id"])&&isset($_GET["alianza"])&&isset($_GET["name"])){
		$hisid = $_GET["id"];
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
			if($mimembresia["rango"]==1){
				$stmt = $coneccion->prepare('SELECT * FROM miembros WHERE iduser = ? AND idalianza = ?');
				$stmt->bind_param('ii', $hisid, $alianza);
				$stmt->execute();
				$result = $stmt->get_result();
				$countmember=mysqli_num_rows($result);
				if($countmember==1){
					$stmt = $coneccion->prepare('DELETE FROM miembros WHERE iduser = ? AND idalianza = ? LIMIT 1');
					$stmt->bind_param('ii', $hisid, $alianza);
					$stmt->execute();
					echo $name." ha sido expulsado de la alianza";
				}else
					echo $name." no era parte ni habia sido invitado a esta alianza";
			}else
				echo "Se produjo un problema al corroborar tus credenciales con esta alianza";
		}
	}else
		echo "Se produjo un problema obteniendo los parametros necesarios";	
}

?>
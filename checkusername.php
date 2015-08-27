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
		$alianza = $_GET["alianza"];
		$name = $name."%";
		$stmt = $coneccion->prepare('SELECT id, nickname FROM users WHERE nickname LIKE ?');
		$stmt->bind_param('s', $name);
		$stmt->execute();
		$result = $stmt->get_result();
		/*	
		$stmt = $coneccion->prepare('INSERT INTO alianzas(id, nombre) VALUES(?, ?)');
		$name="jejoxx";
		$id=5;
		$stmt->bind_param('is', $id, $name);
		$stmt->execute();
*/
		echo '{"usuarios":[';
		$ready=false;
		while($subuser = mysqli_fetch_array($result)){
			$stmt = $coneccion->prepare('SELECT * FROM miembros WHERE iduser = ? AND idalianza = ?');
			$stmt->bind_param('ii', $subuser["id"], $alianza);
			$stmt->execute();
			$subresult = $stmt->get_result();
			$countalied=mysqli_num_rows($subresult);
			if($subuser["nickname"]!=$user["nickname"]&&$countalied!=1){
				if($ready)
					echo ", ";
				else
					$ready=true;
				echo '"'.$subuser["nickname"].'"';
			}
		}
		echo "]}";
	}else
	echo "0";
	
}

?>
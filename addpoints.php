<?php
session_start();
if(!isset($_SESSION['usuario']))
	header("Location: login.php");
else{
	include('seguridad.php');
	$coneccion = conectardb();
	$idUser = $_SESSION['usuario'];
	$monsterid=0;
	$tipo=0;
	if(isset($_GET["type"]))
		$tipo = $_GET["type"];	
	if(isset($_GET["monsterid"]))
		$monsterid = $_GET["monsterid"];	
	$consulta=mysqli_query($coneccion, "SELECT * FROM users WHERE id = '$idUser';");
	$user=mysqli_fetch_array($consulta);
	$consulta=mysqli_query($coneccion, "SELECT * FROM monstruos WHERE id = $monsterid;");
	$monster=mysqli_fetch_array($consulta);
	$countmostros=mysqli_num_rows($consulta);
	if($countmostros!=0){
		$consulta=mysqli_query($coneccion, "SELECT * FROM labs WHERE id = ".$monster['lab'].";");
		$lab=mysqli_fetch_array($consulta);
		$countlabs=mysqli_num_rows($consulta);
		if($countlabs!=0){
			$consulta=mysqli_query($coneccion, "SELECT * FROM ciudades WHERE id = ".$lab['ciudad'].";");
			$ciudad=mysqli_fetch_array($consulta);
			$countcity=mysqli_num_rows($consulta);
			if($countcity!=0){
				if($ciudad['owner']==$idUser){
					if($tipo=="fuerza"||$tipo=="velocidad"||$tipo=="garras"||$tipo=="veneno"||$tipo=="evasivo"||$tipo=="escamas"||$tipo=="resistencia"){
						$cantactual=$monster[$tipo];
						if($cantactual==0)
							$cantactual=1;
						else
							$cantactual=$cantactual*2;
						if($cantactual >$lab['puntosgeneticos'])
							echo '{"mensaje":"Puntos geneticos insuficientes(Son necesarios '.$cantactual.').", "error":"true"}';
						else{
							$nuevospuntos=$lab['puntosgeneticos']-$cantactual;
							$plus = 0;
							switch($monster['tipo']){
								case "1":
									if($tipo=="velocidad"||$tipo=="veneno"||$tipo=="evasivo")
										$plus=1;
									break;
								case "2":
									if($tipo=="velocidad"||$tipo=="garras"||$tipo=="resistencia")
										$plus=1;
									break;
								case "3":
									if($tipo=="velocidad"||$tipo=="garras"||$tipo=="escamas")
										$plus=1;
									break;
								case "4":
									if($tipo=="fuerza"||$tipo=="evasivo"||$tipo=="resistencia")
										$plus=1;
									break;
								case "5":
									if($tipo=="fuerza"||$tipo=="garras"||$tipo=="escamas")
										$plus=1;
									break;							
							}
							mysqli_query($coneccion, "UPDATE labs SET puntosgeneticos = $nuevospuntos WHERE id = ".$monster['lab'].";");
							mysqli_query($coneccion, "UPDATE monstruos SET $tipo = ".($monster[$tipo]+1+$plus)." WHERE id = ".$monster['lab'].";");
							echo '{"mensaje":"Monstruo mejorado.", "error":"false", "newpoints":"'.($monster[$tipo]+1+$plus).'", "type":"'.$tipo.'", "newgenetics":"'.$nuevospuntos.'"}';
						}
					}
					else
						echo '{"mensaje":"Tipo de propiedad del monstruo erronea.", "error":"true"}';				}	
			}else
				echo '{"mensaje":"Se produjo un problema buscando la ciudad.", "error":"true"}';	
		}else
			echo '{"mensaje":"Se produjo un problema buscando el laboratorio.", "error":"true"}';
	}else
		echo '{"mensaje":"Se produjo un problema buscando el monstruo id.", "error":"true"}';	
}
?>
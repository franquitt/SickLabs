<?php
session_start();
if(!isset($_SESSION['usuario']))
{
	header("Location: login.php");
}
else{

	function getLab($city, $x, $y){
		
	}
	function getCity($owner, $x, $y){
		
	}

	include('seguridad.php');
	$coneccion = conectardb();
	$idUser = $_SESSION['usuario'];
	$consulta=mysqli_query($coneccion, "SELECT * FROM users WHERE id = '$idUser';");
	$user=mysqli_fetch_array($consulta);
	


	$monsterMaxP=0;
	$monsterMinP=0;
	$subconsulta=mysqli_query($coneccion, "SELECT * FROM `monstruos`,(SELECT labs.id AS LABID FROM labs, (SELECT id AS LABCITY FROM ciudades WHERE owner='$idUser') AS CIUDAD WHERE labs.ciudad = LABCITY) AS LAB WHERE monstruos.lab = LABID ORDER BY puntaje DESC LIMIT 1;");
	$monstruo=mysqli_fetch_array($subconsulta);
	$monsterMaxP=$monstruo['puntaje'];
	$subconsulta=mysqli_query($coneccion, "SELECT * FROM `monstruos`,(SELECT labs.id AS LABID FROM labs, (SELECT id AS LABCITY FROM ciudades WHERE owner='$idUser') AS CIUDAD WHERE labs.ciudad = LABCITY) AS LAB WHERE monstruos.lab = LABID ORDER BY puntaje ASC LIMIT 1;");
	$monstruo=mysqli_fetch_array($subconsulta);
	$monsterMinP=$monstruo['puntaje'];


	$globant=$user;
	$cx=-1;
	$cy=-1;
	$lx="warning";
	$ly="warning";
	$mode=1;
	$getmode=1;
	if(isset($_GET['mode']))
		$getmode= $_GET['mode'];
	if(isset($_GET['cx']) && isset($_GET['cy'])){
		$cx= $_GET['cx'];
		$cy= $_GET['cy'];
		if(is_intF($cx) && is_intF($cy)){
			if(isset($_GET['lx']) && isset($_GET['ly'])){
				$lx= $_GET['lx'];
				$ly= $_GET['ly'];
				if($lx=="0"&&$ly=="0"&&$getmode==2){
					$mode=2;
					$lx="0";
					$ly="0";
				}else{
					$ly=(int) $ly;
					$lx=(int) $lx;

					if((is_intF($lx) && is_intF($ly)) || (is_int($ly)==1 && is_int($ly)==1)){
						$mode=2;
					}
					else{
						$lx="warning";
						$ly="warning";
					}
				}
			}
		}
		else{
			$cx=-1;
			$cy=-1;
		}
		
	}
	$ciudadseteada=false;
	if(	$cx==-1 && $cy==-1){
		$consulta=mysqli_query($coneccion, "SELECT * FROM ciudades WHERE owner = '$idUser' ORDER BY ID ASC LIMIT 1;");
		$firstciudad=mysqli_fetch_array($consulta);
		$cx= $firstciudad['x'];
		$cy= $firstciudad['y'];
		$ciudadseteada=true;
		$globant=$firstciudad;
	}
	if($lx=="warning" && $ly=="warning"){
		$mode=1;
		$lx=0;
		$ly=0;
	}
	if($mode==1){
		$vecx[0]=$cx-1;
		$vecy[0]=$cy+1;

		$vecx[1]=$cx;
		$vecy[1]=$cy+1;

		$vecx[2]=$cx+1;
		$vecy[2]=$cy+1;

		$vecx[3]=$cx-1;
		$vecy[3]=$cy;

		$vecx[4]=$cx;
		$vecy[4]=$cy;

		$vecx[5]=$cx+1;
		$vecy[5]=$cy;

		$vecx[6]=$cx-1;
		$vecy[6]=$cy-1;

		$vecx[7]=$cx;
		$vecy[7]=$cy-1;

		$vecx[8]=$cx+1;
		$vecy[8]=$cy-1;
		/*MODO 1
x  y
nombre y rango del tipo
hostil?
cant labs
mejor lab de la city
tipo mejor mostro
coins
last seen connected*/
		echo '{"mode":"1", "ciudades":[';
		for($index=0;$index<9;$index++){
			$cx = $vecx[$index];
			$cy = $vecy[$index];
			$consulta=mysqli_query($coneccion, "SELECT * FROM ciudades WHERE x='$cx' AND y='$cy' ORDER BY ID ASC LIMIT 1;");
			$city=mysqli_fetch_array($consulta);
			$cityid=$city['id'];
			$countcity=mysqli_num_rows($consulta);
			



			if($countcity==1){
				
				$idextranjera=$city['owner'];
				
				$consulta=mysqli_query($coneccion, "SELECT * FROM labs WHERE ciudad = '$cityid';");
				$countlabs=mysqli_num_rows($consulta);
				
				$consulta=mysqli_query($coneccion, "SELECT * FROM miembros WHERE iduser = '$idUser' AND aceptado=1;");
				$countalianza=mysqli_num_rows($consulta);

				$consulta=mysqli_query($coneccion, "SELECT * FROM users WHERE id = '$idextranjera';");
				$userextranjero=mysqli_fetch_array($consulta);




				$hostil="si";
				if($idextranjera == $idUser)
					$hostil="no";
				else if($countalianza!=0){
					$membresia=mysqli_fetch_array($consulta);
					$idalianza=$membresia['idalianza'];
					$consulta=mysqli_query($coneccion, "SELECT * FROM miembros WHERE iduser = '$idextranjera' AND idalianza = '$idalianza' AND aceptado=1;");
					$countalied=mysqli_num_rows($consulta);
					if($countalied!=0)
						$hostil="no";
				}
				if($hostil=="no"){
					echo '{"free":"false","x":"'.$vecx[$index].'","y":"'.$vecy[$index].'","cityid":"'.$cityid.'","cantlabs":"'.$countlabs.'","hostil":"'.$hostil.'","ownernick":"'.$userextranjero['nickname'].'","cityname":"'.$city['nombre'].'","ownerlevel":"'.$userextranjero['nivel'].'"}';
				}
				else{

					$visible=0;
					$hisMonsterMaxP=0;
					$hisMonsterMinP=0;
					$consulta=mysqli_query($coneccion, "SELECT * FROM `monstruos`,(SELECT labs.id AS LABID FROM labs WHERE labs.ciudad = '$cityid') AS LAB WHERE monstruos.lab = LABID ORDER BY puntaje DESC LIMIT 1");
					$monstruo=mysqli_fetch_array($consulta);
					$hisMonsterMaxP=$monstruo['puntaje'];
					$consulta=mysqli_query($coneccion, "SELECT * FROM `monstruos`,(SELECT labs.id AS LABID FROM labs WHERE labs.ciudad = '$cityid') AS LAB WHERE monstruos.lab = LABID ORDER BY puntaje ASC LIMIT 1");
					$monstruo=mysqli_fetch_array($consulta);
					$hisMonsterMinP=$monstruo['puntaje'];
					/*echo "<br> hisMonsterMaxP ".$hisMonsterMaxP;
					echo "<br> hisMonsterMinP ".$hisMonsterMinP;
					echo "<br> monsterMaxP ".$monsterMaxP ;
					echo "<br> monsterMinP ".$monsterMinP ;*/
					if( ( ($hisMonsterMaxP + 10) >= $monsterMinP && ($hisMonsterMaxP - 10) <= $monsterMaxP ) || ( ($hisMonsterMinP - 10) <= $monsterMaxP &&  ($hisMonsterMinP + 10) >= $monsterMinP) )
						$visible=1;


					if($visible==1)
						echo '{"free":"false","arbol":"false","x":"'.$vecx[$index].'","y":"'.$vecy[$index].'","cityid":"'.$cityid.'","cantlabs":"'.$countlabs.'","hostil":"'.$hostil.'","ownernick":"'.$userextranjero['nickname'].'","cityname":"'.$city['nombre'].'","ownerlevel":"'.$userextranjero['nivel'].'"}';	
					else
						echo '{"free":"false","arbol":"true","x":"'.$vecx[$index].'","y":"'.$vecy[$index].'"}';
				}
			}else
				echo '{"free":"true","x":"'.$vecx[$index].'","y":"'.$vecy[$index].'"}';
			if($index==8)
				echo "]}";
			else
				echo ",";
		}
	}
	if($mode==2){
		if(!$ciudadseteada){
			
			$consulta=mysqli_query($coneccion, "SELECT * FROM ciudades WHERE x='$cx' AND y='$cy' ORDER BY ID ASC LIMIT 1;");
			$globant=mysqli_fetch_array($consulta);
		}
		$vecx[0]=$lx-1;
		$vecy[0]=$ly+1;

		$vecx[1]=$lx;
		$vecy[1]=$ly+1;

		$vecx[2]=$lx+1;
		$vecy[2]=$ly+1;

		$vecx[3]=$lx-1;
		$vecy[3]=$ly;

		$vecx[4]=$lx;
		$vecy[4]=$ly;

		$vecx[5]=$lx+1;
		$vecy[5]=$ly;

		$vecx[6]=$lx-1;
		$vecy[6]=$ly-1;

		$vecx[7]=$lx;
		$vecy[7]=$ly-1;

		$vecx[8]=$lx+1;
		$vecy[8]=$ly-1;
		$cityid=$globant['id'];
		$consulta=mysqli_query($coneccion, "SELECT owner, nickname FROM ciudades, (SELECT nickname, id FROM users) AS USER WHERE ciudades.id = '$cityid' AND USER.id=owner;");
		
		$city=mysqli_fetch_array($consulta);

		echo '{"mode":"2", "cityid":"'.$cityid.'", "cityowner":"'.$city["owner"].'", "ownernickciudad":"'.$city["nickname"].'", "labs":[';
		
		for($index=0;$index<9;$index++){
			$labx = $vecx[$index];
			$laby = $vecy[$index];
			if($labx==0 && $laby==0){
				echo '{"lab":"false","x":"'.$labx.'","y":"'.$laby.'"}';
				//echo '{"lab":"false","x":"'.$labx.'","y":"'.$laby.'", "nivel":"'.$city['poblacionnivel'].'"}';
			}else{
				$consulta=mysqli_query($coneccion, "SELECT * FROM labs WHERE ciudad = '$cityid' AND x='$labx' AND y='$laby';");
				
				

				$laboratorio=mysqli_fetch_array($consulta);
				$idlab=$laboratorio['id'];
				$consulta=mysqli_query($coneccion, "SELECT * FROM monstruos WHERE lab = '$idlab';");
				
				$monstruo=mysqli_fetch_array($consulta);
				$countlab=mysqli_num_rows($consulta);

				/*hostilidad
----------------------------------------------------------------------------------------
				*/

				$idextranjera=$globant['owner'];
				
				$consulta=mysqli_query($coneccion, "SELECT * FROM miembros WHERE iduser = '$idUser' AND aceptado=1;");
				$countalianza=mysqli_num_rows($consulta);

				$consulta=mysqli_query($coneccion, "SELECT * FROM users WHERE id = '$idextranjera';");
				$userextranjero=mysqli_fetch_array($consulta);

				$hostil="si";
				if($idextranjera == $idUser)
					$hostil="no";
				else if($countalianza!=0){
					$membresia=mysqli_fetch_array($consulta);
					$idalianza=$membresia['idalianza'];
					$consulta=mysqli_query($coneccion, "SELECT * FROM miembros WHERE iduser = '$idextranjera' AND idalianza = '$idalianza' AND aceptado=1;");
					$countalied=mysqli_num_rows($consulta);
					if($countalied!=0)
					$hostil="no";
				}
/*hostilidad
----------------------------------------------------------------------------------------
				*/

				if($countlab!=0){
					
					if($city['owner']==$idUser)
						echo '{"lab":"true","free":"false", "name":"'.$laboratorio['nombre'].'","hostil":"'.$hostil.'","id":"'.$laboratorio['id'].'","x":"'.$labx.'","y":"'.$laby.'","monstertype":"'.$monstruo['tipo'].'","oro":"'.$laboratorio['oro'].'","puntosgeneticos":"'.$laboratorio['puntosgeneticos'].'","internos":"'.$laboratorio['internos'].'","energia":"'.$laboratorio['energia'].'"}';
					else{
						$visible=0;
						$hisMonsterMaxP=0;
						$hisMonsterMinP=0;
						$consulta=mysqli_query($coneccion, "SELECT * FROM `monstruos` WHERE monstruos.lab = '$idlab' ORDER BY puntaje DESC LIMIT 1");
						$monstruo=mysqli_fetch_array($consulta);
						$hisMonsterMaxP=$monstruo['puntaje'];
						$consulta=mysqli_query($coneccion, "SELECT * FROM `monstruos` WHERE monstruos.lab = '$idlab' ORDER BY puntaje ASC LIMIT 1");
						$monstruo=mysqli_fetch_array($consulta);
						$hisMonsterMinP=$monstruo['puntaje'];
						if( ( ($hisMonsterMaxP + 10) >= $monsterMinP && ($hisMonsterMaxP - 10) <= $monsterMaxP ) || ( ($hisMonsterMinP - 10) <= $monsterMaxP &&  ($hisMonsterMinP + 10) >= $monsterMinP) )
							$visible=1;
						if($visible==1)
							echo '{"lab":"true","free":"false", "name":"'.$laboratorio['nombre'].'","hostil":"'.$hostil.'","puntaje":"'.$hisMonsterMaxP.'","id":"'.$laboratorio['id'].'","x":"'.$labx.'","y":"'.$laby.'","monstertype":"'.$monstruo['tipo'].'","oro":"'.$laboratorio['oro'].'","puntosgeneticos":"'.$laboratorio['puntosgeneticos'].'"}';
						else
							echo '{"lab":"true","free":"true","x":"'.$labx.'","y":"'.$laby.'"}';
				

					}
				}
				else{
					echo '{"lab":"true","free":"true","x":"'.$labx.'","y":"'.$laby.'"}';
				}
				
			}
			if($index==8)
				echo "]}";
			else
				echo ",";
				
		}
	}
}
?>
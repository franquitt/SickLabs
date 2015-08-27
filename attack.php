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
	$globant=$user;
	$cx=-1;
	$cy=-1;
	$mode=1;
	$options='';
	$getlabid=0;
	$atacado=0;
	$confirm=0;
	$attackResult="{";
	if(isset($_GET['id']))
		$getlabid= $_GET['id'];
	if(isset($_POST['id']))
		$getlabid= $_POST['id'];
	if(isset($_GET['confirm']))
		$confirm= $_GET['confirm'];
	if(isset($_POST['confirm']))
		$confirm= $_POST['confirm'];	
  	$mycitys=array();
  	$mylabs=array();	
	$consulta=mysqli_query($coneccion, "SELECT * FROM ciudades WHERE owner = '$idUser';");
	while($ciudad=mysqli_fetch_array($consulta))
	{
		$mycitys[]=$ciudad;
		$ciudadid=$ciudad['id'];
		$subconsulta=mysqli_query($coneccion, "SELECT * FROM labs WHERE ciudad = '$ciudadid';");
		while($lab=mysqli_fetch_array($subconsulta))
		{
			$mylabs[]=$lab;			
		}
	}
	$options="";
	$subconsulta=mysqli_query($coneccion, "SELECT * FROM labs WHERE ciudad = '$getlabid';");
	if($lab=mysqli_fetch_array($subconsulta))
	{
		$hislab=$lab;
		$subconsulta=mysqli_query($coneccion, "SELECT * FROM monstruos WHERE lab = '$getlabid';");
		$hismonster=mysqli_fetch_array($subconsulta);
		$hiscityid=$lab['ciudad'];
		$subconsulta=mysqli_query($coneccion, "SELECT * FROM ciudades WHERE id = '$hiscityid';");
		$hiscity=mysqli_fetch_array($subconsulta);
		for($i=0;$i<count($mycitys);$i++)
		{
			$ciudad=$mycitys[$i];
			$distancia=0;
			$distanciay=0;
			$distanciax=0;
			if($ciudad['y']<$hiscity['y'])
				$distanciay=$hiscity['y']-$ciudad['y'];
			else
				$distanciay=$ciudad['y']-$hiscity['y'];

			if($ciudad['x']<$hiscity['x'])
				$distanciax=$hiscity['x']-$ciudad['x'];
			else
				$distanciax=$ciudad['x']-$hiscity['x'];
			$distancia=$distanciax+$distanciay;
			if($distancia>200){
				$distancia = $distancia -50;
			}
			else if($distancia>100){
				$distancia = $distancia -25;
			}
			$options.='<optgroup label="'.$ciudad['nombre'].' (consume '.$distancia.' de energia)">';
			for($i2=0;$i2<count($mylabs);$i2++)
			{
				$mylab=$mylabs[$i2];
				$mylabid=$mylab['id'];
				if($mylab['ciudad']==$ciudad['id']){
					if($confirm==0){
						$subconsulta=mysqli_query($coneccion, "SELECT * FROM monstruos WHERE lab = '$mylabid';");
						$mymonster=mysqli_fetch_array($subconsulta);						
						$disabled=" disabled";
						if( ( ($hismonster['puntaje'] + 10) >= $mymonster['puntaje'] && ($hismonster['puntaje'] - 10) <= $mymonster['puntaje'] ))
							$disabled="";
						$options.='<option value="'.$mylabid.'" '.$disabled.'>'.$mylab['nombre']." (posee ".$mylab['energia'].' de energia)</option>';
					}
					else{
						if($mylabid==$confirm){
							$subconsulta=mysqli_query($coneccion, "SELECT * FROM monstruos WHERE lab = '$mylabid';");
							$mymonster=mysqli_fetch_array($subconsulta);
							if( ( ($hismonster['puntaje'] + 10) >= $mymonster['puntaje'] && ($hismonster['puntaje'] - 10) <= $mymonster['puntaje'] )){
								$distancia=0;
								$distanciay=0;
								$distanciax=0;
								if($ciudad['y']<$hiscity['y'])
									$distanciay=$hiscity['y']-$ciudad['y'];
								else
									$distanciay=$ciudad['y']-$hiscity['y'];
								if($ciudad['x']<$hiscity['x'])
									$distanciax=$hiscity['x']-$ciudad['x'];
								else
									$distanciax=$ciudad['x']-$hiscity['x'];
								$distancia=$distanciax+$distanciay;
								if($distancia>200)
									$distancia = $distancia -50;								
								else if($distancia>100)
									$distancia = $distancia -25;								
								$dmgTotal=0;
								$dmgFuerza=0;
								$dmgVeneno=0;
								$dmgGarras=0;
								$dmgVelocidad=0;
								if($mymonster['fuerza'] >= $hismonster['fuerza']){
									$dmgFuerza=$mymonster['fuerza'] - $hismonster['fuerza'];
									$dmgTotal+=$dmgFuerza;
								}else{
									$dmgFuerza= $hismonster['fuerza'] -$mymonster['fuerza'];
									$dmgTotal-=$dmgFuerza;
								}
								if($mymonster['veneno'] >= $hismonster['resistencia']){
									$dmgVeneno=$mymonster['veneno'] - $hismonster['resistencia'];
									$dmgTotal+=$dmgVeneno;
								}else{
									$dmgVeneno=$hismonster['resistencia'] - $mymonster['veneno'];
									$dmgTotal-=$dmgVeneno;
								}
								if($mymonster['garras'] >= $hismonster['escamas']){
									$dmgGarras=$mymonster['garras'] - $hismonster['escamas'];
									$dmgTotal+=$dmgGarras;
								}else{
									$dmgGarras=$hismonster['escamas'] - $mymonster['garras'];
									$dmgTotal-=$dmgGarras;
								}
								if($mymonster['velocidad'] >= $hismonster['evasivo']){
									$dmgVelocidad=$mymonster['velocidad'] - $hismonster['evasivo'];
									$dmgTotal+=$dmgVelocidad;
								}else{
									$dmgVelocidad=$hismonster['evasivo'] - $mymonster['velocidad'];
									$dmgTotal-=$dmgVelocidad;
								}
								if($dmgTotal>0){									
									$oldEnergy=$mylab['energia'];
									$newEnergy=$mylab['energia']-$distancia;
									$moneyCambiado=0;
									$moneyConseguido=0;
									$puntosgeneticosConseguidos=0;
									$puntosgeneticosCambiados=0;
									$destroyed="false";
									mysqli_query($coneccion, "UPDATE labs SET energia = '$newEnergy' WHERE id = '$mylabid';");
									if($dmgTotal<=10){
										$moneyCambiado = round($dmgTotal*10*$hislab['oro']/100);
										$moneyConseguido = $mylab['oro']+$moneyCambiado;
										$moneyPerdido = $hislab['oro']-$moneyCambiado;
										mysqli_query($coneccion, "UPDATE labs SET oro = '$moneyPerdido' WHERE id = '$getlabid';");
										mysqli_query($coneccion, "UPDATE labs SET oro = '$moneyConseguido' WHERE id = '$mylabid';");
									}else if($dmgTotal<=20){
										$moneyConseguido = $mylab['oro']+$hislab['oro'];
										$moneyCambiado=$hislab['oro'];								
										mysqli_query($coneccion, "UPDATE labs SET oro = '0' WHERE id = '$getlabid';");
										mysqli_query($coneccion, "UPDATE labs SET oro = '$moneyConseguido' WHERE id = '$mylabid';");
										$puntosgeneticosCambiados = round($hislab['puntosgeneticos']*($dmgTotal-10)*10/100);
										$puntosgeneticosConseguidos = $mylab['puntosgeneticos']+$puntosgeneticosCambiados;
										$puntosgeneticosPerdidos = $hislab['puntosgeneticos']-$puntosgeneticosCambiados;
										mysqli_query($coneccion, "UPDATE labs SET puntosgeneticos = '$puntosgeneticosPerdidos' WHERE id = '$getlabid';");
										mysqli_query($coneccion, "UPDATE labs SET puntosgeneticos = '$puntosgeneticosConseguidos' WHERE id = '$mylabid';");
									}else{
										$moneyConseguido = $mylab['oro']+$hislab['oro'];
										$moneyCambiado=$hislab['oro'];
										$puntosgeneticosConseguidos = $mylab['puntosgeneticos']+$hislab['puntosgeneticos'];
										$puntosgeneticosCambiados= $hislab['puntosgeneticos'];
										mysqli_query($coneccion, "UPDATE labs SET oro = '$moneyConseguido', puntosgeneticos = '$puntosgeneticosConseguidos' WHERE id = '$mylabid';");
 										mysqli_query($coneccion, "DELETE FROM labs WHERE id = '$getlabid' LIMIT 1;");
									}
									$attackResult.='"dmgtotal":"'.$dmgTotal.'", "oldEnergy":"'.$oldEnergy.'", "newEnergy":"'.$newEnergy.'", "moneyConseguido":"'.$moneyCambiado.'", "newMoney":"'.$moneyConseguido.'", "puntosgeneticosConseguidos":"'.$puntosgeneticosConseguidos.'", "puntosgeneticosCambiados":"'.$puntosgeneticosCambiados.'", "destroyed":"'.$destroyed.'", "distancia":"'.$distancia.'", "ganaste":"true"';		
								}else{
									$energyLose=0;
									if($dmgTotal>=-10){
										$energyLose = round($mylab['energia']*(-$dmgTotal)*10/100)+$distancia;
										$newEnergy = $mylab['energia'] - $energyLose;
										mysqli_query($coneccion, "UPDATE labs SET energia = '$newEnergy' WHERE id = '$mylabid';");
									}else{
										$energyLose = $mylab['energia'];
										$newEnergy=0;
										mysqli_query($coneccion, "UPDATE labs SET energia = '0' WHERE id = '$mylabid';");
									}
									$attackResult.='"dmgtotal":"'.$dmgTotal.'", "energyLose":"'.$energyLose.'", "newenergy":"'.$newEnergy.'", "ganaste":"false"';
								}
							}
							else
								$attackResult.='"msgtype":"Error", "msgtext":"No puedes atacar a este laboratorio"';
						}
					}
				}
			}
			$options.='</optgroup>';			
		}
	}
	if($confirm==0)
		echo $options;	
	else
		echo $attackResult."}";
}
?>
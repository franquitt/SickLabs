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
}
$consulta=mysqli_query($coneccion, "SELECT * FROM notificaciones WHERE receptor='$idUser' AND visto=0;");
$countnotificaciones=mysqli_num_rows($consulta);
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>World</title>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<link rel="stylesheet" href="css/bootstrap.min.css">
			<link rel="stylesheet" href="css/core.css">
			<link rel="icon" href="images/logo.ico" type="image/x-icon">
			<link rel="shortcut icon" href="images/logo.ico" type="image/x-icon"> 
			<script src="js/jquery.min.js"></script>
			<script src="js/bootstrap.min.js"></script>
<!--EMPIEZA LA NAVBAR-->    
    <nav class="navbar navbar-core" role="navigation">
  	<div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->

    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
      <a class="navbar-brand brand" href="home.php">SickLabs</a> </div>
    
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse navheadings" id="bs-example-navbar-collapse-1">
      
       <ul class="nav navbar-nav">
		
		<li ><a href="alianza.php">Alianza</a></li>
		
		<li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown">Acerca De<span class="caret"></span></a>
		  <ul class="dropdown-menu" role="menu">
			<li><a href="informe.pdf">Informe Base de datos</a></li>
			<li><a href="ayuda.html">Ayuda</a></li>
		  </ul>
		</li>  
	  </ul>
        <div class="navbar-center">
        	<p class="navbar-text">WORLD</p>
        </div>
      
      <ul class="nav navbar-nav navbar-right">
		  	<li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="badge glyphicon glyphicon-globe"> <?php echo $countnotificaciones ?></span><span class="caret"></span></a>
			   	<ul class="dropdown-menu" role="menu">
			   		<li class="dropdown-header">Mensaje de Pedro</li>
                	<li><a href="#">Action</a></li>
                	<li class="dropdown-header">Suceso</li>
                	<li><a href="#">Another action</a></li>
                	<li role="separator" class="divider"></li>
                	<li class="dropdown-header">Suceso</li>
                	<li><a href="#">Something else here</a></li>
                	<li role="separator" class="divider"></li>
                	<li class="dropdown-header">Mensaje de Maria</li>
                	<li><a href="#">Something else here</a></li>
                	<li role="separator" class="divider"></li>
                	<li class="dropdown-header">Mensaje de Juan</li>
                	<li><a href="#">Soy un cacheton feo</a></li>

                	<li role="separator" class="divider"></li>
                	<li><a href="#">CENTRO DE MENSAJERIA</a></li>
                	<li><a href="#">CENTRO DE SUCESOS</a></li>
			    </ul>
			</li>
        
        <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-cog"></span><span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="editPerfil.php">Editar Perfil</a></li>
            <li><a href="logout.php">Cerrar sesion</a></li>
          </ul>
        </li>
      </ul>
      
    </div>
    <!-- /.navbar-collapse --> 
  </div>
  <!-- /.container-fluid --> 
</nav>
<!--TERMINA LA NAVBAR-->

	</head>
	<body>
		<script type="text/javascript">
			window.mapmode = <?php echo $mode;?>;
			window.cx = <?php echo $cx;?>;
			window.cy = <?php echo $cy;?>;
			window.lx = <?php echo $lx;?>;
			window.ly = <?php echo $ly;?>;


			function getSelectedValue(elementId) {
			    var elt = document.getElementById(elementId);

			    if (elt.selectedIndex == -1)
			        return null;

			    return elt.options[elt.selectedIndex].value;
			}


			function irDerecha(){
				if(window.mapmode==1)
					window.cx = window.cx+1;
				else
					window.lx = window.lx+1;
				cargarTabla();
			}
			function irIzquierda(){
				if(window.mapmode==1)
					window.cx = window.cx-1;
				else
					window.lx = window.lx-1;
				cargarTabla();
			}
			function irArriba(){
				if(window.mapmode==1)
					window.cy = window.cy+1;
				else
					window.ly = window.ly+1;
				cargarTabla();
			}
			function irAbajo(){
				if(window.mapmode==1)
					window.cy = window.cy-1;
				else
					window.ly = window.ly-1;
				cargarTabla();
			}
		</script>
		<script>
			window.idUser=<?php echo $idUser; ?>;
			cargarTabla();
			function goCity(x, y){
				window.cy=y;
				window.cx=x;
				window.mapmode=2;
				cargarTabla();
			}
			function goBackCity(){
				window.ly=0;
				window.lx=0;
				window.mapmode=1;
				cargarTabla();
			}
			function confirmAttack(){
				var xmlhttp;
				if (window.XMLHttpRequest)
				  {// code for IE7+, Firefox, Chrome, Opera, Safari
				  	xmlhttp=new XMLHttpRequest();
				  }
				else
				  {// code for IE6, IE5
				  	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				  }
				xmlhttp.onreadystatechange=function()
				  {
				  if (xmlhttp.readyState==4 && xmlhttp.status==200)
					{
						var data = JSON.parse(xmlhttp.responseText);
						if(data.ganaste=="true"){
							if (data.distancia!=0) {
								var color='red';
								var icon='menos';
								if(data.distancia<0){
									color='green';
									icon='mas';
								}
								var out= '<center><tr><td><img src="images/icon_'+ icon +'Energia.png" width="50" height="35" style="margin-right:5px;margin-top:5px;"></td><td><span style="color:'+ color +';font-weight:bold">' + data.distancia + '</span></td></tr></center>'
								document.getElementById("energy").innerHTML=out;
							};
							if (data.puntosgeneticosConseguidos!=0) {
								var out= '<center><tr><td><img src="images/icon_masAdn.png" width="50" height="35" style="margin-right:5px;margin-top:5px;"></td><td><span style="color:green;font-weight:bold">' + data.puntosgeneticosConseguidos + '</span></td></tr></center>'
								document.getElementById("pgenwin").innerHTML=out;
							};
							if (data.moneyConseguido!=0) {
								var out= '<center><tr><td><img src="images/icon_masOro.png" width="50" height="35" style="margin-right:5px;"></td><td><span style="color:green;font-weight:bold">' + data.moneyConseguido + '</span></td></tr></center>'
								document.getElementById("orowin").innerHTML=out;
							};
        
							$('#modalTriunfo').modal('show')
						}else{
							document.getElementById("energylost2").innerHTML=data.energyLose;
							$('#modalDerrota').modal('show')
						}	    				
					}
				  }				  
				xmlhttp.open("GET","attack.php?id="+window.attackId+"&confirm="+getSelectedValue("labattackid"),true);
				xmlhttp.send();
			}
			function attack(id,alPueblo){
				if(alPueblo==false){
					if(id==idUser){
						var out ='No podes atacar a un laboratorio propio';
						var out2 = '<button type="button" class="btn btn-info" onClick="cerrarModal()" >Aceptar</button>';
						document.getElementById("selectattack").innerHTML='No podes atacar a un laboratorio propio';
						document.getElementById("accionsiguiente").innerHTML='<button type="button" class="btn btn-info" onClick="cerrarModal()" >Aceptar</button>';
						document.getElementById("titulomodal").innerHTML='<h4 class="modal-title">Error</h4>';
					} else {
						window.attackId=id;
						var xmlhttp;
						if (window.XMLHttpRequest)
						  {// code for IE7+, Firefox, Chrome, Opera, Safari
						  	xmlhttp=new XMLHttpRequest();
						  }
						else
						  {// code for IE6, IE5
						  	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
						  }
						xmlhttp.onreadystatechange=function()
						  {
						  if (xmlhttp.readyState==4 && xmlhttp.status==200)
							{
								var out ='<select class="form-control" id="labattackid">';
			              		out+=xmlhttp.responseText;
			            		out+='</select>';
								document.getElementById("selectattack").innerHTML=out;
								document.getElementById("accionsiguiente").innerHTML='<button type="button" class="btn btn-info" onClick="confirmAttack()">Atacar <span class="glyphicon glyphicon-flash"></span></button>';
								document.getElementById("titulomodal").innerHTML='<h4 class="modal-title">Selecciona desde que laboratorio atacarás</h4>';
							}
						  }
						xmlhttp.open("GET","attack.php?id="+id,true);
						xmlhttp.send();
					}
					$('#modalInicio').modal('show')
				} else {
					if (id==idUser) {
						var out ='Ten en cuenta que luego de atacar a tu pueblo este se volvera mas fuerte'
						out+='<select class="form-control" id="labattackid">'
			            out+='<option value="grecia">Grecia</option><option value="italia">Italia</option>'
			            out+='</select>';
			            var out2 = '<button type="button" class="btn btn-info" onClick="confirmAttack()">Atacar <span class="glyphicon glyphicon-flash"></span></button>';
			            var out3 = '<h4 class="modal-title">Selecciona desde que laboratorio atacarás</h4>';
					} else {
						var out ='No podes atacar la población de otro usuario';
						var out2 = '<button type="button" class="btn btn-info" onClick="cerrarModal()" >Aceptar</button>';
						var out3 = '<h4 class="modal-title">Error</h4>';
					}
					document.getElementById("selectattack").innerHTML=out;
					document.getElementById("accionsiguiente").innerHTML=out2;	 
					document.getElementById("titulomodal").innerHTML=out3;
					$('#modalInicio').modal('show');
				}	
			}
			function comprarTerreno(modo,x,y) {
				if (modo) {
					var out ='Seguro que quieres comprar un terreno en esta ciudad('+x+','+y+') por 50000?';
					var out2 = '<a href="addcity.php?x='+x+'&y='+y+'"><button type="button" class="btn btn-info" onClick="confirmarCompra('+x+','+y+')" >Aceptar</button></a>';
					var out3 = '<h4 class="modal-title">Confirmar Compra</h4>';
				} else if (cityowner==idUser) {
					var out ='Ya posees este terreno';
					var out2 = '<button type="button" class="btn btn-info" onClick="cerrarModal()">Aceptar</button>';
					var out3 = '<h4 class="modal-title">Error</h4>';
				} else {
					var out ='No podes comprar un terreno en una ciudad de otro usuario';
					var out2 = '<button type="button" class="btn btn-info" onClick="cerrarModal()" >Aceptar</button>';
					var out3 = '<h4 class="modal-title">Error</h4>';
				}
				document.getElementById("selectattack").innerHTML=out;
				document.getElementById("accionsiguiente").innerHTML=out2;	 
				document.getElementById("titulomodal").innerHTML=out3;
				$('#modalInicio').modal('show');
			}
			function confirmarCompra(x,y){
				console.log("X: " + x);
				console.log("Y: " + y);
			}
			function cerrarModal () {
				$("#modalInicio").modal("hide");
			}
			function showData(id){
				var out = "";
				if(window.mapmode==1){
					var ciudad=window.ciudades[id];
					out+="<br>x="+ciudad.x;	
					out+="<br>y="+ciudad.y;
					out+="<br>cityid="+ciudad.cityid;
					out+="<br>free="+ciudad.free;
					out+="<br>arbol="+ciudad.arbol;
					out+="<br>hostil="+ciudad.hostil;
					out+="<br>owner Nick="+ciudad.ownernick;
					out+="<br>nombre="+ciudad.cityname;
					out+="<br>ownerlevel="+ciudad.ownerlevel;
					out+="<br>cantlabs="+ciudad.cantlabs;
				}else{
					var lab=window.labs[id];
					out+="<br>id="+lab.id;
					out+="<br>hostil="+lab.hostil;
					out+="<br>x="+lab.x;
					out+="<br>y="+lab.y;
					out+="<br>lab="+lab.lab;
					out+="<br>free="+lab.free;
					out+="<br>name="+lab.name;
					out+="<br>monstertype="+lab.monstertype;
					out+="<br>oro="+lab.oro;
					out+="<br>puntosgeneticos="+lab.puntosgeneticos;
					out+="<br>internos="+lab.internos;
					out+="<br>energia="+lab.energia;
					out+="<br>puntaje="+lab.puntaje;
				}
				document.getElementById("divdata").innerHTML=out;
			}
			function parsear(data){
				if(data.mode==1){
					console.log("ES MODO 1");
					window.ciudades=data.ciudades;
					document.getElementById("divbtnback").innerHTML='';
					document.getElementById("titulaso").innerHTML="<h3>Mundo</h3>";
					for(i = 0; i < window.ciudades.length; i++) {
						console.log("Ciudad"+i);
						var out="";
						var ciudad=window.ciudades[i];
						if(ciudad.free=="false"){
							if (ciudad.arbol=="true"){
								out='<img id="image" src="images/bosque.png" width="160" height="160"/>';
							}else{
								var cityname = ciudad.cityname;
								var cityowner = ciudad.ownernick;
								if (ciudad.cityname.length > 17) {
									console.log("pedazo de nombre: " + ciudad.cityname.substring(0,16));
									cityname = ciudad.cityname.substring(0,13) + "...";
								}
								if (cityowner.length > 11) {
									console.log("pedazo de nombre: " + ciudad.cityname.substring(0,16));
									cityowner = cityowner.substring(0,8) + "...";
								}
								out='<div id="mainwrapper" class="cubito" onClick="goCity('+ciudad.x+', '+ciudad.y+')" cosa="ciudad"><div id="box" class="box"><img id="image" src="images/ciudadentera.png" width="160" height="160"/><span class="caption fade-caption"><div id="textbox"><h1 class="alignleft">' + cityname + '</h1><h1 class="alignright">' + ciudad.cityid + '</h1><div style="clear: both;"></div><h4>' + ciudad.cantlabs + ' Laboratorios</h4><img src="images/'+ciudad.hostil+'-hostil.png" width="100%" height="100" style="padding-left:20%;padding-right:20%;"><h5 style="font-size:10pt;margin-top:65%;" class="alignleft" width="100%">' + cityowner + '(' + ciudad.ownerlevel + ')</h5><h5 style="font-size:8pt;text-align:right;margin-top:65%;" width="100%">X= ' + ciudad.x + '</h5><h5 style="font-size:8pt;text-align:right" width="100%">Y= ' + ciudad.y + '</h5></div></span></div></div>';
							}
						}else{
							out='<div id="mainwrapper" onclick="comprarTerreno(true,' + ciudad.x + ',' + ciudad.y + ');"><div id="box" class="box"><img id="image" src="images/grass.png" width="160" height="160"/><span class="caption fade-caption"><div id="textbox"><h1 class="alignleft">Terreno libre</h1><div style="clear: both;"></div><h5 style="font-size:8pt;text-align:right;margin-top:70%;" width="100%">X= ' + ciudad.x + '</h5><h5 style="font-size:8pt;text-align:right" width="100%">Y= ' + ciudad.y + '</h5></div></span></div></div>';
						}
						document.getElementById("celda"+i).innerHTML=out;
					}
				}
				else{
					console.log("ES MODO 2");
					window.labs=data.labs;
					console.log(labs);
					document.getElementById("divbtnback").innerHTML='<div class="arrowBig hvr-bubble-float-left" onclick="goBackCity()" style="position:absolute;left:5%;"><p style="margin-top:12px;margin-left:5px;margin-right:5px;color:black">Volver al mapa<span class="glyphicon glyphicon-globe" style="margin-left:3px"></span></p></div>';
					window.cityid=data.cityid;

					window.cityowner=data.cityowner;
					document.getElementById("titulaso").innerHTML="<h3>Ciudad de "+data.ownernickciudad+"</h3>";
					for(i = 0; i < window.labs.length; i++) {
						var out="";
						var laboratorio=window.labs[i];
						if(laboratorio.lab=="false"){
							out='<div id="mainwrapper" onClick="attack('+ cityowner + ',' + true +')"><div id="box" class="box"><img id="image" src="images/town_map.png" width="160" height="160"/><span class="caption fade-caption"><div id="textbox"><h1 class="alignleft">Pueblo</h1><div style="clear: both;"></div><br><br><br><br><h5 style="font-size:8pt;text-align:right" width="100%">X= ' + laboratorio.x + '</h5><h5 style="font-size:8pt;text-align:right" width="100%">Y= ' + laboratorio.y + '</h5></div></span></div></div>';
						}
						else{
							console.log(laboratorio.id);
							if(laboratorio.free=="false"){
								marginTabla = "10%";
								if(laboratorio.hostil=="si"){
									marginTabla= "20%";
								}

								out = '<div id="mainwrapper"><div id="box" class="box"><img id="image" src="images/lab_map.png" width="160" height="160"/><span ';

								out += 'onClick="attack('+laboratorio.id+','+false+')" class="caption fade-caption"><div id="textbox"><h2 class="alignleft">' + laboratorio.name + '</h2><img src="images/icon_' + laboratorio.monstertype + '.png" width="95%" height="50" style="padding-left:80%;"/><br><table style="margin-top:' + marginTabla + '"><tbody>';

								if(laboratorio.hostil=="si")
									out+='<tr><td><img src="images/icon_stat.png" width="20" height="20" style="position:static" /></td><td style="padding-left:5px;vertical-align:middle;">' + laboratorio.puntaje + '</td></tr>';

								out+= '<tr><td><img src="images/icon_oro.png" width="20" height="20" style="position:static" /></td><td style="padding-left:5px;vertical-align:middle;">' + laboratorio.oro + '</td></tr><tr><td><img src="images/icon_adn.png" width="20" height="20" style="position:static" /></td><td style="padding-left:5px;vertical-align:middle;">' + laboratorio.puntosgeneticos + '</td></tr>';

								if(laboratorio.hostil=="no")
									out+= '<tr><td><img src="images/icon_interno.png" width="20" height="20" style="position:static" /></td><td style="padding-left:5px;vertical-align:middle;">' + laboratorio.internos + '</td></tr><tr><td><img src="images/icon_energia.png" width="20" height="20" style="position:static" /></td><td style="padding-left:5px;vertical-align:middle;">' + laboratorio.energia + '</td></tr>';

								out+= '</tbody></table><br></div></span></div></div>';

							}
							else{
								out='<div id="mainwrapper" onclick="comprarTerreno(false);"><div id="box" class="box"><img id="image" src="images/grass.png" width="160" height="160"/><span class="caption fade-caption"><div id="textbox"><h1 class="alignleft">Terreno libre</h1><div style="clear: both;"></div><br><br><br><br><h5 style="font-size:8pt;text-align:right" width="100%">X= ' + laboratorio.x + '</h5><h5 style="font-size:8pt;text-align:right" width="100%">Y= ' + laboratorio.y + '</h5></div></span></div></div>';
							}
						}
						document.getElementById("celda"+i).innerHTML=out;
					}
				}
			}
			function cargarTabla(){
				var xmlhttp;
				if (window.XMLHttpRequest)
				  {// code for IE7+, Firefox, Chrome, Opera, Safari
				  	xmlhttp=new XMLHttpRequest();
				  }
				else
				  {// code for IE6, IE5
				  	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				  }
				xmlhttp.onreadystatechange=function()
				  {
				  if (xmlhttp.readyState==4 && xmlhttp.status==200)
					{
						var myArr = JSON.parse(xmlhttp.responseText); 
						window.mapmode = myArr.mode;      
        				parsear(myArr);
					}
				  }

				xmlhttp.open("GET","worldget.php?cx="+window.cx+"&cy="+window.cy+"&lx="+window.lx+"&ly="+window.ly+"&mode="+window.mapmode,true);
				xmlhttp.send();
			}
		</script>
        
		<center><div id="divbtnback"></div></center>
		<div id="tabladelamuerte">
			<center>
				<div id="titulaso" class="gris"></div>
				<div class="arrow arrow-up hvr hvr-float" onclick="irArriba()" style="margin-bottom:5px"></div>
				<div id="divdata">
				</div>

				<table>
					<tr>
						<td></td>
						<td id="celda0"><img src="images/grass.png" /></td>
						<td id="celda1"><img src="images/grass.png" /></td>
						<td id="celda2"><img src="images/grass.png" /></td>
						<td></td>
					</tr>
					<tr>
						<td style="vertical-align:middle">
							<div class="arrow arrow-left hvr hvr-shiftLeft" onclick="irIzquierda()" style="margin-right:5px"></div>
						</td>
						<td id="celda3"><img src="images/grass.png" /></td>
						<td id="celda4"><img src="images/grass.png" /></td>
						<td id="celda5"><img src="images/grass.png" /></td>
						<td style="vertical-align:middle">
							<div class="arrow arrow-right hvr hvr-shiftRight" onclick="irDerecha()" style="margin-left:5px"></div>
						</td>
					</tr>
					<tr>
						<td></td>
						<td id="celda6"><img src="images/grass.png" /></td>
						<td id="celda7"><img src="images/grass.png" /></td>
						<td id="celda8"><img src="images/grass.png" /></td>
						<td></td>
					</tr>
				</table>
				<div class="arrow arrow-down hvr hvr-sink" onclick="irAbajo()" style="margin-top:5px"></div>
			</center>
		</div>
            

<!-- Modal Inicio-->
<div id="modalInicio" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
      	<span id="titulomodal"></span>
      </div>

      <div class="modal-body">
      	<div id="selectattack">
            		
        </div>

      <div class="modal-footer">
        <div id="accionsiguiente"></div>
      </div>
    </div>

  </div>
</div


<!-- Modal TRIUNFO-->
<div id="modalTriunfo" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" align="center" >¡VICTORIA!</h4>
      </div>
      <div class="modal-body">
		<table align="center">
			<tbody>
				<span id="orowin"></span>
				<span id="pgenwin"></span>
				<span id="energy"></span>
			</tbody>
		</table>
        
      </div>
      <div class="modal-footer">
      	<center>
        	<button type="button" class="btn btn-success" data-dismiss="modal" style="text-align:center">Aceptar</button>
        </center>
      </div>
    </div>

  </div>
</div>

<!-- Modal DERROTA-->
<div id="modalDerrota" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" align="center">¡DERROTA!</h4>
      </div>
      <div class="modal-body">
        <img src="images/icon_menosEnergia.png" width="50" height="35" style="position:absolute;left:45%;top:5px;">
        <span id="energylost2" style="color:red;position:absolute;left:54%;top:12px;font-weight:bold"></span>
        <h4 style="margin-top:30px"> </h4>
        
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

  </div>
</div>
</body>
</html>
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
	$consulta=mysqli_query($coneccion, "SELECT fuerza FROM (SELECT id AS idlabs FROM (SELECT id AS idciu FROM ciudades WHERE owner='$idUser') AS CIUDAD, labs WHERE ciudad=CIUDAD.idciu) AS LAB, monstruos WHERE monstruos.lab= LAB.idlabs;");

	
	$countmostros=mysqli_num_rows($consulta);
	$consulta=mysqli_query($coneccion, "SELECT id AS idlabs FROM (SELECT id AS idciu FROM ciudades WHERE owner='$idUser') AS CIUDAD, labs WHERE ciudad=CIUDAD.idciu");
	
	$countlabs=mysqli_num_rows($consulta);
	if($countlabs==0 || $countmostros!=0){
		header("Location: startcity.php");
	}
}
?>
<!DOCTYPE html>
<html lang="es">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon"> 
<title>SickLabs</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/core.css" rel="stylesheet">
<script type="text/javascript">
		function monster(tipo){
			var r = confirm("Seguro de usar el tipo "+ tipo +"?" );
			if (r == true) {
    

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
						if(xmlhttp.responseText=="home"){
							location.href="home.php";
						}else{
							alert(xmlhttp.responseText);
						}
					}
				  }

				xmlhttp.open("GET","monstercreator.php?tipo="+tipo,true);
				xmlhttp.send();
			}
		}
</script>
	<head>
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
          <p class="navbar-text">YOUR FIRST MONSTER</p>
        </div>
      
      <ul class="nav navbar-nav navbar-right disabled">
        <li class="dropdown"> <a href="#" class="dropdown-toggle disabled" data-toggle="dropdown"><span class="badge glyphicon glyphicon-globe">15</span><span class="caret"></span></a>
          <ul class="dropdown-menu disabled" role="menu">  
            <li><a href="#">Mensajes</a></li>
            <li class="divider"></li>
            <li><a href="#">Sucesos</a></li>
          </ul>
        </li>
        
        <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-cog"></span><span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">Editar Perfil</a></li>
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
    <div class="panel-heading">
    	<h3 class="text-center h3panel"><?php echo $user['nickname']?>, es  tiempo de crear a tu Monstruo!</h3>
    </div>
    <div class="row" style="padding-left: 10%;">
    	<div class="col-md-2">
    		<div onClick="monster('bug')">
	    		<ul class="nav align" role="tablist">
					  <li role="presentation" class="active">
					  	<span class="badge">
					  	<h4>Bug</h4>
					  	<img class="img-monster" src="images/icon_1.png"></img>
					  	<p>+2 Evasion</p>
					  	<p>+2 Veneno</p>
					  	<p>+1 Velocidad</p>
					  </span>
					  </li>
					</ul>
				</div>
			</div>
			<div class="col-md-2">
				<div onClick="monster('demon')">
	    		<ul class="nav align" role="tablist" >
						  <li role="presentation" class="active">
						  	<span class="badge">
						  	<h4>Demon</h4>
						  	<img class="img-monster" src="images/icon_2.png"></img>
							  	<p>+2 Garra</p>
							  	<p>+2 Resist</p>
							  	<p>+1 Velocidad</p>
							 </span>
						  </li> 
					</ul>
				</div>
			</div>
			<div class="col-md-2">
				<div onClick="monster('wild')">
	    		<ul class="nav align" role="tablist">
					  <li role="presentation" class="active">
					  	<span class="badge">
					  	<h4>Wild</h4>
					  	<img class="img-monster" src="images/icon_3.png"></img>
					  	<p>+2 Garras</p>
					  	<p>+2 Velocidad</p>
					  	<p>+1 Escudo</p>
					  </span>
					  </li>
					</ul>
				</div>
			</div>
			<div class="col-md-2">
				<div onClick="monster('element')">
	    		<ul class="nav align" role="tablist">
					  <li role="presentation" class="active" >
					  	<span class="badge">
					  	<h4>Element</h4>
					  	<img class="img-monster" src="images/icon_4.png"></img>
					  	<p>+2 Fuerza</p>
					  	<p>+2 Resist</p>
					  	<p>+1 Evasion</p>
					  </span>
					  </li>
					</ul>
				</div>
			</div>
			<div class="col-md-2">
				<div  onClick="monster('beast')">
	    		<ul class="nav align" role="tablist">
					  <li role="presentation" class="active">
					  	<span class="badge">
					  	<h4>Beast</h4>
					  	<img class="img-monster" src="images/icon_5.png"></img>
					  	<p>+2 Fuerza</p>
					  	<p>+2 Escudo</p>
					  	<p>+1 Garras</p>
					  </span>	
					  </li>
					</ul>
				</div>
			</div>
		</div>
	</body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</html>
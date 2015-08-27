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
	$consulta=mysqli_query($coneccion, "SELECT * FROM ciudades WHERE owner = '$idUser';");
	
	$countciudades=mysqli_num_rows($consulta);
	if($countciudades!=0){
		header("Location: home.php");
	}
	$error="";
	if(isset($_POST['crear'])) {
		$city= $_POST['cityname'];
		$lab= $_POST['labname'];
		if($city!="" &&  $lab!=""){
			$params=explode(" ",getXY($coneccion));
			$x=$params[0];
			$y=$params[1];
			mysqli_query($coneccion, "INSERT INTO ciudades(nombre, owner, x, y) VALUES('$city', '$idUser', '$x', '$y');");
			$consulta=mysqli_query($coneccion, "SELECT * FROM ciudades WHERE owner = '$idUser' AND x = '$x' AND y = '$y';");
			$ciudad=mysqli_fetch_array($consulta);
			$cityid=$ciudad['id'];
			mysqli_query($coneccion, "INSERT INTO labs(nombre, ciudad) VALUES('$lab', '$cityid');");

			header("Location: addmonster.php");
		}
		else{
			$error ="Rellene todos los campos";
		}
	}
	//5000 0pG 200 E 1 Chabon
}
function getXY($coneccion){
	while(true){
		$x = rand(0, 10000);
		$y = rand(0, 10000);
		$consulta=mysqli_query($coneccion, "SELECT * FROM ciudades WHERE x = '$x' AND y = '$y';");
		$countciudades=mysqli_num_rows($consulta);
		if($countciudades==0){
			return $x." ".$y;
		}
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
          <p class="navbar-text">YOUR FIRST CITY</p>
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
		<div class="container">
    	<div class="panel-heading">
    		<h3 class="text-center h3panel">Bienvenido <?php echo $user['nickname']?>, es hora de empezar!</h3>
    	</div>
 		<form class="form-horizontal" method="POST" action="#">
		  <?php echo "<h1>".$error."</h1>"; ?>          
      <ul class="list-group">
	  		<li class="list-group-item ul">
	        <input type="text" name="cityname" class="form-control"  placeholder="Nombre de tu tu ciudad">
	      </li>
	      <li class="list-group-item ul">
	      	<input type="text" name="labname" class="form-control" placeholder="Nombre de tu laboratorio">
	      </li>
	      <li class="list-group-item ul">
	      	<button type="submit" name="crear" class="btn botona btn-success">Crear</button>
        </li>         	  
    </form>
 	</div>
</body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</html>

<?php
session_start();
if(isset($_SESSION['usuario']))
{
	header("Location: home.php");
}
else{
	$error="";
	if(isset($_POST['registrarse'])) {
		
		$nick= $_POST['nickname'];
		$password= $_POST['password'];
		$repassword= $_POST['repassword'];
		$email= $_POST['email'];
		if($nick==""||$password==""||$repassword==""||$email==""){
			$error.="<div class='alert alert-danger' role='alert'>Complete todos los campos</div>";
		}else{
			if($password==$repassword){
				include('seguridad.php');
				if(validacionemail($email)){
					$coneccion = conectardb();
					$result=mysqli_query($coneccion, "SELECT * FROM users WHERE nickname = '$nick';");
					$countuser=mysqli_num_rows($result);
					if($countuser!=0){
						$error.="<div class='alert alert-danger' role='alert'>Nickname ya usado</div>";
					}else{
						$result=mysqli_query($coneccion, "SELECT * FROM users WHERE email = '$email';");
						$countemail=mysqli_num_rows($result);
						if($countemail!=0){
							$error.="<div class='alert alert-danger' role='alert'>Email ya usado</div>";
						}else{
							$user = encriptar($nick);
							$password = encriptar($password);
							
							$consulta=mysqli_query($coneccion, "INSERT INTO users(nickname, password, email, user) VALUES('$nick', '$password', '$email', '$user');");
							$consulta=mysqli_query($coneccion, "SELECT *FROM users WHERE email = '$email';");
							$row=mysqli_fetch_array($consulta);
							session_start();
							$_SESSION['usuario'] = $row['id'];
							header("location: startcity.php");
						}
					}
				}else{
					$error.="<div class='alert alert-danger' role='alert'>Email no valido</div>";
				}
			}else{
				$error.="<div class='alert alert-danger' role='alert'>Las contraseñas no coinciden</div>";
			}
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
    <script type="text/javascript">	
		function checkUsuario(){
			usuario = document.f1.nickname.value;
			if (usuario == "")
				document.getElementById("error").innerHTML = "<div class='alert alert-danger' role='alert' style='margin:2%'><button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button><h4>Ingresa nombre de usuario</h4></div>";
			else {
				document.getElementById("error").innerHTML = "<div class='alert alert-success' role='alert' style='margin:2%'><button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button><h4>Estás completando correctamente el formulario</h4></div>";
			}
		}
		function checkContra(){
			contra = document.f1.password.value;
			recontra = document.f1.repassword.value;
			if (contra == recontra)
				document.getElementById("error").innerHTML = "<div class='alert alert-success' role='alert' style='margin:2%'><button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button><h4>Estás completando correctamente el formulario</h4></div>";
			else {
				document.getElementById("error").innerHTML = "<div class='alert alert-danger' role='alert' style='margin:2%'><button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button><h4>Las contraseñas no son iguales</h4></div>";
			}
		}
	</script>
  <head>
  	<nav class="navbar navbar-core">
    <div class="container-fluid">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">SickLabs</a>
      </div>
      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
        </ul>
      </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
  </head>
  <body>
    <div class="container">
    	<div class="panel-heading">
    		<h3 class="text-center h3panel">Ingrese los datos al formulario</h3>
    	</div>
 		<form class="form-horizontal" name="f1" action="#" method="POST">
          
		  <?php echo "<h1>".$error."</h1>"; ?>
          
          <div id ="error"></div>
          
          <ul class="list-group">
	         	<li class="list-group-item ul">
	        		<input type="text" name="nickname" id="nickname" class="form-control"  placeholder="Ingresa tu usuario" onKeyUp="checkUsuario();">
	      		</li>
	      		<li class="list-group-item ul">
	        		<input type="password" name="password" class="form-control" id="password" placeholder="Ingresa tu contraseña">
	      		</li>
	      		<li class="list-group-item ul">
	        		<input type="password" onKeyUp="checkContra()" name="repassword" class="form-control" id="repassword" placeholder="Repetir contraseña">
	      		</li>
          		<li class="list-group-item ul">
	        		<input type="email" onKeyUp="checkEmail" name="email" class="form-control" id="email" placeholder="Ingresa tu email">
	      		</li>
	      		<li class="list-group-item ul">
	      			<button type="submit" name="registrarse" class="btn botona btn-success">Registrarse</button>
        		</li>
        </form>
 	</div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
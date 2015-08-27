<?php
session_start();
if(isset($_SESSION['usuario']))
{
	header("Location: home.php");
}
else{
	$error="";
	if(isset($_POST['login'])) {
		
		$nick= $_POST['nickname'];
		$password= $_POST['password'];
		if($nick==""||$password==""){
			$error="<div class='alert alert-danger' role='alert'>No ha ingresado datos</div>";
		}else{
			include('seguridad.php');
			$coneccion = conectardb();
			$user=encriptar($nick);
			$password = encriptar($password);
			$result=mysqli_query($coneccion, "SELECT * FROM users WHERE user = '$user' AND password='$password';");
			$countuser=mysqli_num_rows($result);
			if($countuser==0){
				$error="<div class='alert alert-danger' role='alert'>Los datos ingresados son incorrectos</div>";
			}else{						
				$row=mysqli_fetch_array($result);
				$_SESSION['usuario'] = $row['id'];
				header("location: home.php");
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

    <title>SickLabs</title>
    <!-- Bootstrap -->
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon"> 
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/core.css" rel="stylesheet">
    <script type="text/javascript">
      function chb(){
        if(document.getElementById("myChb").checked==true)
          document.getElementById("myChb").checked = false;
        else
          document.getElementById("myChb").checked = true;
      }
    </script>
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
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
       		<form class="form-horizontal " action="#" role="form" name="f1" autocomplete="on" method="post">
            <div class="logueo">
              <img class="profile-img" src="images/icono.png" alt="">

              <div class="log">
                <input type="text" name="nickname" id="nickname" class="form-control"  placeholder="Usuario">
              </div>

              <div class="log boton">
                <input type="password" name="password" id="password" class="form-control"  placeholder="Contraseña">
              </div>
              <?php echo "<div><h3>".$error."</h3></div>"; ?>
              <div class="checkbox"> 
                <div class="log">
                  <div class="btn-warning btn form-control" id="myBtn" href="#" onclick="chb()">
                    <input type="checkbox" name="recordarme" id="myChb"  checked="false">
                    <label  for="recordarme">Recordarme</label>  
                  </div>
                </div>
              </div>

                <div class="log">
              <button type="submit" name="login" class="form-control btn btn-info">Ingresar</button> 
                </div>

                <div class="log">
                  <a href="register.php"><button type="button" name="register" class="form-control boton btn btn-success">Registrate</button></a> 
                </div>                 
          </form>
    </div>
 	 </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
	<title><?php echo COMPANY; ?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<link rel="stylesheet" href="<?php echo SERVERURL; ?>views/css/main.css">
    <!--====== Scripts -->
	<?php include "views/modulos/script.php";?>
</head>
<body>
    <?php 
        $peticionAjax=false;
        require_once "./controllers/vistasControlador.php";
        $vt = new vistasControlador();
        $vistasR=$vt->obtener_vistas_controlador();
        
        if(($vistasR=="login")||($vistasR=="404")):
            if($vistasR=="login"){
                require_once "./views/contenidos/login-view.php";
            }else{
                require_once "./views/contenidos/404-view.php";
            }
        else:
            session_start(['name'=>'SBP']);

            require_once "./controllers/loginControlador.php";

            $lc = new loginControlador();

            if(!isset($_SESSION['token_sbp']) || !isset($_SESSION['usuario_sbp'])){
                $lc->forzar_cierre_sesion_controlador();

            }
    ?>
	<!-- SideBar -->
	<?php include "views/modulos/navlateral.php";?>

	<!-- Content page-->
	<section class="full-box dashboard-contentPage">

		<!-- NavBar -->
		<?php include "views/modulos/navbar.php";?>
		
		<!-- Content page -->
		<?php require_once $vistasR; ?>
	</section>
    <?php 
        include "views/modulos/logoutScript.php";
    endif; 
    ?>
    <script>
        $.material.init();
    </script>
</body>
</html>
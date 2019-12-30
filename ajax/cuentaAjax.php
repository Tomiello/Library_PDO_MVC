<?php 
    $peticionAjax=true;
    require_once "../core/configGeneral.php";

    if( isset($_POST['cuentacodigoup']) ){

       require_once "../controllers/cuentaControlador.php";
       $cuenta = new cuentaControlador();
        if ( isset($_POST['cuentacodigoup']) && isset($_POST['cuentatipoup']) && isset($_POST['userlog'])) {
            echo $cuenta->actualizar_cuenta_controlador();
        }
    
    }else{
        session_start(['name'=>'SBP']);
        session_destroy();
        echo '<script>window.location.href="'.SERVERURL.'login/";</script>';
    }
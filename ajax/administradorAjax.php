<?php 
    $peticionAjax=true;
    require_once "../core/configGeneral.php";

    if( (isset($_POST['dni'])) || (isset($_POST['codigoDel'])) || (isset($_POST['cuentaup'])) ){

        require_once "../controllers/administradorControlador.php";
        $insAdmin= new administradorControlador();

        //aca se agrega un administrador
        if( (isset($_POST['dni'])) && (isset($_POST['nombre'])) && (isset($_POST['apellido'])) && (isset($_POST['user'])) ){
            $datos=[
                "dni-reg"=>$_POST['dni'],
                "nombre-reg"=>$_POST['nombre'],
                "apellido-reg"=>$_POST['apellido'],
                "telefono-reg"=>$_POST['telefono'],
                "direccion-reg"=>$_POST['direccion'],
                "usuario-reg"=>$_POST['user'],
                "password1-reg"=>$_POST['pass1'],
                "password2-reg"=>$_POST['pass2'],
                "email-reg"=>$_POST['email'],
                "optionsGenero"=>$_POST['genero'],
                "optionsPrivilegio"=>$_POST['privilegio']
            ];
            echo $insAdmin->agregar_administrador_controlador($datos);
        }

        //aca se elimina un administrador
        if( (isset($_POST['codigoDel'])) && (isset($_POST['privilegioAdmin'])) ){
            echo $insAdmin->eliminar_administrador_controlador();
        }
      
        if ( (isset($_POST['cuentaup'])) && (isset($_POST['dniup'])) ) {
            echo $insAdmin->actualizar_administrador_controlador();
        }

    }else{
        session_start(['name'=>'SBP']);
        session_destroy();
        echo '<script>window.location.href="'.SERVERURL.'login/";</script>';
    }
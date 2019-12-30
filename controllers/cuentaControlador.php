<?php
    if($peticionAjax){
        require_once "../core/MainModel.php";
    }else{
        require_once "./core/MainModel.php";
    }

    class cuentaControlador extends MainModel{

        public function datos_cuenta_controlador($codigo,$tipo){
            $codigo=MainModel::decryption($codigo);
            $tipo=MainModel::limpiar_cadena($tipo);
            if ($tipo=="admin") {
                $tipo="Administrador";
            } else {
                $tipo="Cliente";
            }
            
            return MainModel::datos_cuenta($codigo,$tipo);
        }

        public function actualizar_cuenta_controlador(){
            $CuentaCodigo=MainModel::decryption($_POST['cuentacodigoup']);
            $CuentaTipo=MainModel::decryption($_POST['cuentatipoup']);

            $query1=MainModel::ejecutar_consulta_simple("SELECT * FROM cuenta WHERE CuentaCodigo='$CuentaCodigo'");
            $DatosCuenta=$query1->fetch();

            $user=MainModel::limpiar_cadena($_POST['userlog']);
            $password=MainModel::limpiar_cadena($_POST['passwordlog']);
            $password=MainModel::encryption($password);

            if ($user!="" && $password!="") {

                //si no es el propietario de la cuenta, verifico los privilegios
                if (isset($_POST['privilegio'])) {
                    $login=MainModel::ejecutar_consulta_simple("SELECT id FROM cuenta WHERE CuentaUsuario='$user' AND CuentaClave='$password' ");
                } else {
                    $login=MainModel::ejecutar_consulta_simple("SELECT id FROM cuenta WHERE CuentaUsuario='$user' AND CuentaClave='$password' AND CuentaCodigo='$CuentaCodigo'");
                }

                if ($login->rowCount()==0) {
                    //los datos que ingreso no coinciden con ningun registro de la db
                    $alerta=[
                        "Alerta"=>"simple",
                        "Titulo"=>"Ocurrió un error inesperado",
                        "Texto"=>"El nombre de usuario y clave que acaba de ingresar no coinciden con los datos de su cuenta.",
                        "Tipo"=>"error"
                    ];
                    return MainModel::sweet_alert($alerta);
                    exit();
                }
                
            }else{
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"Para actualizar los datos de la cuenta debe de ingresar el nombre de usuario y clave, por favor ingrese los datos e intente nuevamente.",
                    "Tipo"=>"error"
                ];
                return MainModel::sweet_alert($alerta);
                exit();
            }

            //VERIFICAR USUARIO
            $CuentaUsuario=MainModel::limpiar_cadena($_POST['cuentausuarioup']);
             //si el nombre usuario que se ingreso es distinto del que esta en la DB
            if ($CuentaUsuario!=$DatosCuenta['CuentaUsuario']) {
                $query2=MainModel::ejecutar_consulta_simple("SELECT CuentaUsuario FROM cuenta WHERE CuentaUsuario='$CuentaUsuario'");
                if ($query2->rowCount()>=1) {
                    //el nombre de usuario ya existe
                    $alerta=[
                        "Alerta"=>"simple",
                        "Titulo"=>"Ocurrió un error inesperado",
                        "Texto"=>"El nombre de usuario que acaba de ingresar ya se encuentra registrado en el sistema.",
                        "Tipo"=>"error"
                    ];
                    return MainModel::sweet_alert($alerta);
                    exit();
                }
            }

            //VERIFICAR EMAIL
            $CuentaEmail=MainModel::limpiar_cadena($_POST['cuentaemailup']);

             //si el email que se ingreso es distinto del que esta en la DB
            if ($CuentaEmail!=$DatosCuenta['CuentaEmail']) {
                $query3=MainModel::ejecutar_consulta_simple("SELECT CuentaEmail FROM cuenta WHERE CuentaEmail='$CuentaEmail'");
                if ($query3->rowCount()>=1) {
                    //el nombre de usuario ya existe
                    $alerta=[
                        "Alerta"=>"simple",
                        "Titulo"=>"Ocurrió un error inesperado",
                        "Texto"=>"El email que ingreso ya se encuentra registrado en el sistema.",
                        "Tipo"=>"error"
                    ];
                    return MainModel::sweet_alert($alerta);
                    exit();
                }
            }

            $CuentaGenero=MainModel::limpiar_cadena($_POST['cuentageneroup']);
            if (isset($_POST['cuentaestadoup'])) {
                $CuentaEstado=MainModel::limpiar_cadena($_POST['cuentaestadoup']);
            }else{
                $CuentaEstado=$DatosCuenta['CuentaEstado'];
            }

            if ($CuentaTipo=="Admin") {
                if (isset($_POST['cuentaprivilegioup'])) {
                    $CuentaPrivilegio=MainModel::decryption($_POST['cuentaprivilegioup']);
                }else{
                    $CuentaPrivilegio=$DatosCuenta['CuentaPrivilegio'];
                }

                if ($CuentaGenero=="Masculino") {
                    $CuentaFoto="Male3Avatar.png";
                }else{
                    $CuentaFoto="Famale3Avatar.png";
                }

            }else{//es un cliente
                $CuentaPrivilegio=$DatosCuenta['CuentaPrivilegio'];
                if ($CuentaGenero=="Masculino") {
                    $CuentaFoto="Male2Avatar.png";
                }else{
                    $CuentaFoto="Famale2Avatar.png";
                }
            }

            //VERIFICAR EL CAMBIO DE CLAVE
            $passwordN1=MainModel::limpiar_cadena($_POST['cuentaclave1up']);
            $passwordN2=MainModel::limpiar_cadena($_POST['cuentaclave2up']);

            //si se cambio la contrasenia
            if ($passwordN1!="" || $passwordN2!="") {
                if ($passwordN1==$passwordN2) {
                    $CuentaClave=MainModel::encryption($passwordN1);
                } else {
                    $alerta=[
                        "Alerta"=>"simple",
                        "Titulo"=>"Ocurrió un error inesperado",
                        "Texto"=>"Los nuevas contraseñas no coinciden, por favor verifique los datos e intente nuevamente.",
                        "Tipo"=>"error"
                    ];
                    return MainModel::sweet_alert($alerta);
                    exit();
                }
                

            }else{//no se cambio la contrasenia
                $CuentaClave=$DatosCuenta['CuentaClave'];
            }

            //ENVIANDO DATOS AL MODELO
            $datosUpdate=[
                "CuentaPrivilegio"=>$CuentaPrivilegio,
                "CuentaCodigo"=>$CuentaCodigo,
                "CuentaUsuario"=>$CuentaUsuario,
                "CuentaClave"=>$CuentaClave,
                "CuentaEmail"=>$CuentaEmail,
                "CuentaEstado"=>$CuentaEstado,
                "CuentaGenero"=>$CuentaGenero,
                "CuentaFoto"=>$CuentaFoto
            ];

            if (MainModel::actualizar_cuenta($datosUpdate)) {
                //si la cuenta es la del que inicio sesion
                if (!isset($_POST['privilegio'])) {
                    session_start(['name'=>'SBP']);
                    $_SESSION['usuario_sbp']=$CuentaUsuario;
                    $_SESSION['foto_sbp']=$CuentaFoto;
                }
                $alerta=[
                    "Alerta"=>"recargar",
                    "Titulo"=>"Cuenta actualizada",
                    "Texto"=>"Los datos de la cuenta se actualizaron con exito.",
                    "Tipo"=>"success"
                ];
            }else{
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"Lo sentimos no hemos podido actualizar los datos de la cuenta.",
                    "Tipo"=>"error"
                ];
            }
            return MainModel::sweet_alert($alerta);
        }

    }
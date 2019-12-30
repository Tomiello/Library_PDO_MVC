<?php
    if($peticionAjax){
        require_once "../models/loginModelo.php";
    }else{
        require_once "./models/loginModelo.php";
    }

    class loginControlador extends loginModelo{
        
        public function iniciar_sesion_controlador(){
            $usuario=MainModel::limpiar_cadena($_POST['usuario']);
            $clave=MainModel::limpiar_cadena($_POST['clave']);
            $clave=MainModel::encryption($clave);

            $datosLogin=[
                "Usuario"=>$usuario,
                "Clave"=>$clave
            ];

            $datosCuenta=loginModelo::iniciar_sesion_modelo($datosLogin);

            if(($datosCuenta->rowCount())==1){//si el usuario esta registrado
                
                $row=$datosCuenta->fetch();
    
                $fechaActual=date("Y-m-d");
                $yearActual=date("Y");
                $horaActual=date("h:i:s a");

                $consulta1=MainModel::ejecutar_consulta_simple("SELECT id FROM bitacora");

                $numero=($consulta1->rowCount())+1;

                $codigoB=MainModel::generar_codigo_aleatorio("CB",7,$numero);

                $datosBitacora=[
                    "Codigo"=>$codigoB,
                    "Fecha"=>$fechaActual,
                    "HoraInicio"=>$horaActual,
                    "HoraFinal"=>"Sin registro",
                    "Tipo"=>$row['CuentaTipo'],
                    "Anio"=>$yearActual,
                    "Cuenta"=>$row['CuentaCodigo']
                ];
                
                $insertarBitacora=MainModel::guardar_bitacora($datosBitacora);
                if(($insertarBitacora->rowCount())>=1){//si se inserto la bitacora
                    if ($row['CuentaTipo']=="Administrador") {
                        $query1=MainModel::ejecutar_consulta_simple("SELECT * FROM admin WHERE CuentaCodigo='".$row['CuentaCodigo']."'");
                    }else{//el que inicio sesion es un cliente
                        $query1=MainModel::ejecutar_consulta_simple("SELECT * FROM cliente WHERE CuentaCodigo='".$row['CuentaCodigo']."'");
                    }

                    if ($query1->rowCount()==1) {
                        session_start(['name'=>'SBP']);
                        $UserData=$query1->fetch();

                        if ($row['CuentaTipo']=="Administrador") {
                           $_SESSION['nombre_sbp']=$UserData['AdminNombre'];
                           $_SESSION['apellido_sbp']=$UserData['AdminApellido'];
                        }else{//el que inicio sesion es un cliente
                            $_SESSION['nombre_sbp']=$UserData['ClienteNombre'];
                            $_SESSION['apellido_sbp']=$UserData['ClienteApellido'];
                        }


                        $_SESSION['usuario_sbp']=$row['CuentaUsuario'];
                        $_SESSION['tipo_sbp']=$row['CuentaTipo'];
                        $_SESSION['privilegio_sbp']=$row['CuentaPrivilegio'];
                        $_SESSION['foto_sbp']=$row['CuentaFoto'];
                        $_SESSION['token_sbp']=md5(uniqid(mt_rand(),true));
                        $_SESSION['codigo_cuenta_sbp']=$row['CuentaCodigo'];
                        $_SESSION['codigo_bitacora_sbp']=$codigoB;

                        if($row['CuentaTipo']=="Administrador"){
                            $url=SERVERURL."home/";
                        }else{
                            $url=SERVERURL."catalog/";
                        }

                        return $urlLocation='<script> window.location="'.$url.'"; </script>';
                    }else{
                        $alerta=[
                            "Alerta"=>"simple",
                            "Titulo"=>"Ocurrio un error inesperado",
                            "Texto"=>"No hemos podido iniciar la sesión por problemas técnicos, por favor intente nuevamente.",
                            "Tipo"=>"error"
                        ];
                        return MainModel::sweet_alert($alerta);
                    }

                }else{
                    $alerta=[
                        "Alerta"=>"simple",
                        "Titulo"=>"Ocurrio un error inesperado",
                        "Texto"=>"No hemos podido iniciar la sesión por problemas técnicos, por favor intente nuevamente.",
                        "Tipo"=>"error"
                    ];
                    return MainModel::sweet_alert($alerta);
                }

            }else{//el usuario no esta registrado
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrio un error inesperado",
                    "Texto"=>"El nombre del usuario y contraseña no son correctos o su cuenta puede estar deshabilitada",
                    "Tipo"=>"error"
                ];
                return MainModel::sweet_alert($alerta);
            }
        }

        public function cerrar_sesion_controlador(){
            session_start(['name'=>'SBP']);
            $token=MainModel::decryption($_GET['Token']);
            $hora=date("h:i:s a");
            $datos=[
                "Usuario"=>$_SESSION['usuario_sbp'],
                "Token_S"=>$_SESSION['token_sbp'],
                "Token"=>$token,
                "Codigo"=>$_SESSION['codigo_bitacora_sbp'],
                "Hora"=>$hora
            ];
            return loginModelo::cerrar_sesion_modelo($datos);
        }

        public function forzar_cierre_sesion_controlador(){
            session_start(['name'=>'SBP']);
            session_destroy();
            return header("Location: ".SERVERURL."login/");
        }
    }
<?php
    if($peticionAjax){
        require_once "../core/MainModel.php";
    }else{
        require_once "./core/MainModel.php";
    }

    class loginModelo extends MainModel{

        protected function iniciar_sesion_modelo($datos){
            $sql=MainModel::conectar()->prepare("SELECT * FROM cuenta WHERE ((CuentaUsuario=:Usuario) AND (CuentaClave=:Clave) AND (CuentaEstado='Activo'))");
            $sql->bindParam(':Usuario',$datos['Usuario']);
            $sql->bindParam(':Clave',$datos['Clave']);
            $sql->execute();
            return $sql;
        }

        protected function cerrar_sesion_modelo($datos){
            if($datos['Usuario']!="" && $datos['Token_S']==$datos['Token']){
            //si el usuario esta definido y el Token de la sesion es igual al Token del boton de cerrar sesion
                $Abitacora=MainModel::actualizar_bitacora($datos['Codigo'],$datos['Hora']);
                if(($Abitacora->rowCount())==1){
                    session_unset();
                    session_destroy();
                    $respuesta="true";
                }else{
                    $respuesta="false";
                }
            }else{
                $respuesta="false";
            }
            return $respuesta;
        }
    }
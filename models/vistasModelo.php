<?php
class vistasModelo{
    protected function obtener_vistas_modelo($vistas){
        //palabras permitidas en la url
        $lsitaBlanca=["adminlist","adminsearch","admin","book","bookconfig","bookinfo","catalog","category","categorylist","client","clientlist","clientsearch","company","companylist","home","myaccount","mydata","provider","providerlist","search"];
        //si lo que tiene $vistas esta en el array
        if(in_array($vistas,$lsitaBlanca)){
            //si el archivo existe el la carpeta views
            if(is_file("./views/contenidos/".$vistas."-view.php")){
                $contenido="./views/contenidos/".$vistas."-view.php";
            }else{
                //si el archivo no existe o fue eliminado
                $contenido="login";
            }
        }elseif($vistas=="login"){
            $contenido="login";
        }elseif($vistas=="index"){
            $contenido="login";
        }else{
            $contenido="404";
        }
        return $contenido;
    }
}
<?php
    if($peticionAjax){
        require_once "../models/administradorModelo.php";
    }else{
        require_once "./models/administradorModelo.php";
    }

    class administradorControlador extends administradorModelo{

        public function agregar_administrador_controlador($datos){

            $dni=MainModel::limpiar_cadena($datos['dni-reg']);
            $nombre=MainModel::limpiar_cadena($datos['nombre-reg']);
            $apellido=MainModel::limpiar_cadena($datos['apellido-reg']);
            $telefono=MainModel::limpiar_cadena($datos['telefono-reg']);
            $direccion=MainModel::limpiar_cadena($datos['direccion-reg']);

            $usuario=MainModel::limpiar_cadena($datos['usuario-reg']);
            $password1=MainModel::limpiar_cadena($datos['password1-reg']);
            $password2=MainModel::limpiar_cadena($datos['password2-reg']);
            $email=MainModel::limpiar_cadena($datos['email-reg']);
            $genero=MainModel::limpiar_cadena($datos['optionsGenero']);

            $privilegio=MainModel::decryption($datos['optionsPrivilegio']);
            $privilegio=MainModel::limpiar_cadena($privilegio);
            

            if($genero=="Masculino"){
                $foto="Male3Avatar.png";
            }else{
                $foto="Female3Avatar.png";
            }
           
            if ($privilegio<1 || $privilegio>3) {
                //el nivel de privilegio no existe
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrio un error inesperado",
                    "Texto"=>"El nivel de privilegio que intenta asignar es incorrecto.",
                    "Tipo"=>"error"
                ];
            }else{
                if($password1!=$password2){
                    $alerta=[
                        "Alerta"=>"simple",
                        "Titulo"=>"Ocurrio un error inesperado",
                        "Texto"=>"Las contraseñas no coinciden, por favor intente nuevamente",
                        "Tipo"=>"error"
                    ];
                }else{
                    //BUSCO SI EL DNI YA EXISTE EN LA DB
                    $consulta1=MainModel::ejecutar_consulta_simple("SELECT AdminDNI FROM admin WHERE AdminDNI='$dni'");
                    if($consulta1->rowCount()>=1){
                        $alerta=[
                            "Alerta"=>"simple",
                            "Titulo"=>"Ocurrio un error inesperado",
                            "Texto"=>"El DNI que acaba de ingresar ya se encuentra registrado en el sistema.",
                            "Tipo"=>"error"
                        ];
                    }else{
                        if($email!=""){
                            $consulta2=MainModel::ejecutar_consulta_simple("SELECT CuentaEmail FROM cuenta WHERE CuentaEmail='$email'");
                            $ec=$consulta2->rowCount();
                        }else{
                            $ec=0;
                        }
    
                        if($ec>=1){//el email ya existe
                            $alerta=[
                                "Alerta"=>"simple",
                                "Titulo"=>"Ocurrio un error inesperado",
                                "Texto"=>"El email que acaba de ingresar ya se encuentra registrado en el sistema.",
                                "Tipo"=>"error"
                            ];
                        }else{
                            //verifico que el usuario sea unico
                            $consulta3=MainModel::ejecutar_consulta_simple("SELECT CuentaUsuario FROM cuenta WHERE CuentaUsuario='$usuario'");
                            if(($consulta3->rowCount())>=1){
                                $alerta=[
                                    "Alerta"=>"simple",
                                    "Titulo"=>"Ocurrio un error inesperado",
                                    "Texto"=>"El usuario que acaba de ingresar ya se encuentra registrado en el sistema.",
                                    "Tipo"=>"error"
                                ];
                            }else{
                                $consulta4=MainModel::ejecutar_consulta_simple("SELECT id FROM cuenta");
                                $numero=($consulta4->rowCount())+1;
                                
                                $codigo=MainModel::generar_codigo_aleatorio("AC",7,$numero);
                                
                                $clave=MainModel::encryption($password1);
                                
                                $dataAC=[
                                    "Codigo"=>$codigo,
                                    "Privilegio"=>$privilegio,
                                    "Usuario"=>$usuario,
                                    "Clave"=>$clave,
                                    "Email"=>$email,
                                    "Estado"=>"Activo",
                                    "Tipo"=>"Administrador",
                                    "Genero"=>$genero,
                                    "Foto"=>$foto
                                ];
                                
                                $guardarCuenta=MainModel::agregar_cuenta($dataAC);
    
                                if(($guardarCuenta->rowCount())>=1){//si se agrego la cuento
                                    $dataAD=[
                                        "DNI"=>$dni,
                                        "Nombre"=>$nombre,
                                        "Apellido"=>$apellido,
                                        "Telefono"=>$telefono,
                                        "Direccion"=>$direccion,
                                        "Codigo"=>$codigo
                                    ];
                                    
                                    $guardarAdmin=administradorModelo::agregar_administrador_modelo($dataAD);
                                    
                                    if(($guardarAdmin->rowCount())>=1){//si se agrego el admin
                                        /*si se pudo agregar el administrador */
                                        //salio todo bien
                                        $alerta=[
                                            "Alerta"=>"limpiar",
                                            "Titulo"=>'Nuevo administrador registrado',
                                            "Texto"=>'El administrador se registro con exito en el sistema.',
                                            "Tipo"=>'success'
                                        ];
                                    }else{
                                        /*no se pudo agregar el administrador entonces se elimina la cuenta */
                                        MainModel::eliminar_cuenta($codigo);
                                        $alerta=[
                                            "Alerta"=>"simple",
                                            "Titulo"=>"Ocurrio un error inesperado",
                                            "Texto"=>"El administrador no pudo ser registrado en el sistema.",
                                            "Tipo"=>"error"
                                        ];
                                    }
                                }else{
                                    $alerta=[
                                        "Alerta"=>"simple",
                                        "Titulo"=>"Ocurrio un error inesperado",
                                        "Texto"=>"No hemos podido registrar el administrador.",
                                        "Tipo"=>"error"
                                    ];
                                }
    
                            }
                        }
                    }
                }
            }
            return MainModel::sweet_alert($alerta);
        }

        public function paginador_administrador_controlador($pagina,$registros,$privilegio,$codigo,$busqueda){
            $pagina=MainModel::limpiar_cadena($pagina);
            $registros=MainModel::limpiar_cadena($registros);
            $privilegio=MainModel::limpiar_cadena($privilegio);
            $codigo=MainModel::limpiar_cadena($codigo);
            $busqueda=MainModel::limpiar_cadena($busqueda);
            $tabla="";

            //si alguien pone un numero decimal en la url, pagina toma el entero
            //si agregan otras cosas que no sean numeros a la url, pagina vale 1
            $pagina=((isset($pagina)) && ($pagina>0)) ? (int)$pagina :1;

            $inicio=($pagina>0)? (($pagina*$registros)-$registros) : 0;

            if ((isset($busqueda)) && ($busqueda!="")) {
                $consulta="SELECT SQL_CALC_FOUND_ROWS * FROM admin WHERE ( (CuentaCodigo != '$codigo' AND id!='1') AND (AdminNombre LIKE '%$busqueda%' OR AdminApellido LIKE '%$busqueda%' OR AdminDNI LIKE '%$busqueda%' OR AdminTelefono LIKE '%$busqueda%') )   ORDER BY AdminNombre ASC LIMIT $inicio,$registros";
                $paginaurl="adminsearch";
            } else {
                $consulta="SELECT SQL_CALC_FOUND_ROWS * FROM admin WHERE CuentaCodigo != '$codigo' AND id!='1' ORDER BY AdminNombre ASC LIMIT $inicio,$registros";
                $paginaurl="adminlist";
            }
            
            $conexion=MainModel::conectar();

            $datos = $conexion->query($consulta);
            $datos=$datos->fetchAll();

            $total=$conexion->query("SELECT FOUND_ROWS()");
            $total= (int) $total->fetchColumn();
            //$total tiene la cantidad de registros que hay

            //ceil redondea para arriba, de la division toma la parte entera
            $Npaginas=ceil($total/$registros);

            $tabla.='<div class="table-responsive">
            <table class="table table-hover text-center">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">DNI</th>
                        <th class="text-center">NOMBRES</th>
                        <th class="text-center">APELLIDOS</th>
                        <th class="text-center">TELÉFONO</th>';
                    if($privilegio<=2){
                        //si tiene privilegio de nivel 2 o 1 se le muestran las siguientes opciones
                        $tabla.='<th class="text-center">A. CUENTA</th>
                        <th class="text-center">A. DATOS</th>';
                    }
                    if ($privilegio==1) {
                        $tabla.='
                        <th class="text-center">ELIMINAR</th>
                        ';
                    }
                   
            $tabla.='</tr>
                </thead>
                <tbody>';
            
            if(($total>=1) && ($pagina<=$Npaginas)){
                $contador=$inicio+1;
                foreach($datos as $rows){
                    $tabla.='
                    <tr>
                        <td>'.$contador.'</td>
                        <td>'.$rows['AdminDNI'].'</td>
                        <td>'.$rows['AdminNombre'].'</td>
                        <td>'.$rows['AdminApellido'].'</td>
                        <td>'.$rows['AdminTelefono'].'</td>';
                        if($privilegio<=2){
                            $tabla.='
                            <td>
                                <a href="'.SERVERURL.'myaccount/admin/'.MainModel::encryption($rows['CuentaCodigo']).'/" class="btn btn-success btn-raised btn-xs">
                                    <i class="zmdi zmdi-refresh"></i>
                                </a>
                            </td>
                            <td>
                                <a href="'.SERVERURL.'mydata/admin/'.MainModel::encryption($rows['CuentaCodigo']).'/" class="btn btn-success btn-raised btn-xs">
                                    <i class="zmdi zmdi-refresh"></i>
                                </a>
                            </td>
                            ';
                        }
                        if($privilegio==1){
                            $tabla.=
                            '<td>
                                <form action="'.SERVERURL.'ajax/administradorAjax.php" method="POST" class="FormularioAjaxEliminar" data-form="delete" entype="multipart/form-data" autocomplete="off">
                                <input type="hidden" name="codigo-del" value="'.MainModel::encryption($rows['CuentaCodigo']).'">
                                <input type="hidden" name="privilegio-admin" value="'.MainModel::encryption($privilegio).'">
                                    <button type="submit" class="btn btn-danger btn-raised btn-xs">
                                        <i class="zmdi zmdi-delete"></i>
                                    </button>
                                    <div class="RespuestaAjaxEliminar"></div>
                                </form>
                            </td>';
                        }
                    $tabla.='</tr>';
                    $contador++;
                }
            }else{
                if($total>=1){
                    $tabla.='
                        <tr>
                            <td colspan="5">
                                <a href="'.SERVERURL.$paginaurl.'/" class="btn btn-sm btn-info btn-raised">
                                    Haga clic aquí para recargar el listado
                                </a>
                            </td>
                        </tr>
                    ';
                }else{
                    $tabla.='
                        <tr>
                            <td colspan="5"> No hay registros en el sistema</td>
                        </tr>
                    ';
                }
            }
            $tabla.="</tbody></table></div>";

            if(($total>=1) && ($pagina<=$Npaginas)){
                $tabla.=
                    '<nav class="text-center">
                    <ul class="pagination pagination-sm">';
                //boton anterior (primer bton)
                if ($pagina==1) {
                    //el boton esta deshabilitado
                    $tabla.=
                    ' <li class="disabled"><a><i class="zmdi zmdi-arrow-left"></i></a></li>';
                }else{
                     //el boton esta habilitado
                     //$pagina-1 porque tiene la ruta de la pagina anterior
                    $tabla.=
                    ' <li><a href="'.SERVERURL.$paginaurl.'/'.($pagina-1).'"><i class="zmdi zmdi-arrow-left"></i></a></li>';
                }


                for ($i=1; $i <= $Npaginas; $i++) { 
                    if ($pagina==$i) {
                        //esta es la pagina actual y se muestra activa
                        $tabla.=
                        ' <li class="active"><a href="'.SERVERURL.$paginaurl.'/'.$i.'/">'.$i.'</a></li>';
                    }else{
                        //estas son los demas enlaces que no estan activos
                        $tabla.=' <li><a href="'.SERVERURL.$paginaurl.'/'.$i.'/">'.$i.'</a></li>';
                    }
                }

                //boton de siguiente (ultimo bton)
                if ($pagina==$Npaginas) {
                    //el boton esta deshabilitado
                    $tabla.=
                    ' <li class="disabled"><a><i class="zmdi zmdi-arrow-right"></i></a></li>';
                }else{
                     //el boton esta habilitado
                     //$pagina-1 porque tiene la ruta de la pagina anterior
                    $tabla.=
                    ' <li><a href="'.SERVERURL.$paginaurl.'/'.($pagina+1).'"><i class="zmdi zmdi-arrow-right"></i></a></li>';
                }
                
                $tabla.='</ul></nav>';
            }

            return $tabla;
        }


        public function eliminar_administrador_controlador(){
            $codigo=MainModel::decryption($_POST['codigoDel']);
            $adminPrivilegio=MainModel::decryption($_POST['privilegioAdmin']);

            $codigo=MainModel::limpiar_cadena($codigo);
            $adminPrivilegio=MainModel::limpiar_cadena($adminPrivilegio);

            if ($adminPrivilegio==1) {
                //tiene los permisos para borrar
                $query1=MainModel::ejecutar_consulta_simple("SELECT id FROM admin WHERE CuentaCodigo='$codigo'");
                $datosAdmin=$query1->fetch();
                if ($datosAdmin['id']!=1) {
                    //si se puede eliminar este admin
                    $DelAdmin = administradorModelo::eliminar_administrador_modelo($codigo);
                    MainModel::eliminar_bitacora($codigo);
                    if ( ($DelAdmin->rowCount()) >= 1 ) {
                        $DelCuenta=MainModel::eliminar_cuenta($codigo);
                        if ( ($DelCuenta->rowCount()) == 1 ) {//si se elimino la cuenta
                            $alerta=[
                                "Alerta"=>"recargar",
                                "Titulo"=>"Administrador eliminado",
                                "Texto"=>"El administrador fue eliminado con exito del sistema.",
                                "Tipo"=>"success"
                            ];
                        } else {
                            $alerta=[
                                "Alerta"=>"simple",
                                "Titulo"=>"Ocurrio un error inesperado",
                                "Texto"=>"No se pudo eliminar esta cuenta del sistema en este momento.",
                                "Tipo"=>"error"
                            ];
                        }
                        
                    } else {
                        $alerta=[
                            "Alerta"=>"simple",
                            "Titulo"=>"Ocurrio un error inesperado",
                            "Texto"=>"No se pudo eliminar el administrador del sistema en este momento.",
                            "Tipo"=>"error"
                        ];
                    }
                } else {
                    //es el admin principa y no se puede eliminar
                    $alerta=[
                        "Alerta"=>"simple",
                        "Titulo"=>"Ocurrio un error inesperado",
                        "Texto"=>"No se puede eliminar el administrador principal del sistema.",
                        "Tipo"=>"error"
                    ];
                }
                
            } else {
                //no tiene los permisos para borrar
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrio un error inesperado",
                    "Texto"=>"Usted no tiene los permisos necesarios para realizar esta operación.",
                    "Tipo"=>"error"
                ];
            }
            return MainModel::sweet_alert($alerta);
        }

        public function datos_administrador_controlador($tipo,$codigo){
            $codigo=MainModel::decryption($codigo);
            $tipo=MainModel::limpiar_cadena($tipo);

            return administradorModelo::datos_administrador_modelo($tipo,$codigo);
        }

        public function actualizar_administrador_controlador(){
            $cuenta=MainModel::decryption($_POST['cuentaup']);

            $dni=MainModel::limpiar_cadena($_POST['dniup']);
            $nombre=MainModel::limpiar_cadena($_POST['nombreup']);
            $apellido=MainModel::limpiar_cadena($_POST['apellidoup']);
            $telefono=MainModel::limpiar_cadena($_POST['telefonoup']);
            $direccion=MainModel::limpiar_cadena($_POST['direccionup']);

            $query1=MainModel::ejecutar_consulta_simple("SELECT * FROM admin WHERE CuentaCodigo='$cuenta'");
            $DatosAdmin=$query1->fetch();

            if ($dni != $DatosAdmin['AdminDNI']) {
                $consulta1=MainModel::ejecutar_consulta_simple("SELECT AdminDNI FROM admin WHERE AdminDNI='$dni'");
                if (($consulta1->rowCount()) >= 1) {
                    //si existe ya un DNI igual que el que se ingreso en el formulario
                    $alerta=[
                        "Alerta"=>"simple",
                        "Titulo"=>"Ocurrió un error inesperado",
                        "Texto"=>"El DNI que acaba de ingresar ya se encuentra registrado en el sistema.",
                        "Tipo"=>"error"
                    ];
                    return MainModel::sweet_alert($alerta);
                    exit();
                }

                $dataAd=[
                    "DNI"=>$dni,
                    "Nombre"=>$nombre,
                    "Apellido"=>$apellido,
                    "Telefono"=>$telefono,
                    "Direccion"=>$direccion,
                    "Codigo"=>$cuenta
                ];

                if (administradorModelo::actualizar_administrador_modelo($dataAd)) {
                    $alerta=[
                        "Alerta"=>"recargar",
                        "Titulo"=>"Datos actualizados!",
                        "Texto"=>"Tus datos han sido actualizados con exito.",
                        "Tipo"=>"success"
                    ];
                } else {
                    $alerta=[
                        "Alerta"=>"simple",
                        "Titulo"=>"Ocurrió un error inesperado",
                        "Texto"=>"No hemos podido actualizar tus datos, por favor intente nuevamente.",
                        "Tipo"=>"error"
                    ];
                }
                return MainModel::sweet_alert($alerta);
                

            }
        }

    }

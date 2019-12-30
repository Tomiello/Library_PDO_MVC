$(document).ready(function(){
	$('.btn-sideBar-SubMenu').on('click', function(e){
		e.preventDefault();
		var SubMenu=$(this).next('ul');
		var iconBtn=$(this).children('.zmdi-caret-down');
		if(SubMenu.hasClass('show-sideBar-SubMenu')){
			iconBtn.removeClass('zmdi-hc-rotate-180');
			SubMenu.removeClass('show-sideBar-SubMenu');
		}else{
			iconBtn.addClass('zmdi-hc-rotate-180');
			SubMenu.addClass('show-sideBar-SubMenu');
		}
	});
	
	$('.btn-menu-dashboard').on('click', function(e){
		e.preventDefault();
		var body=$('.dashboard-contentPage');
		var sidebar=$('.dashboard-sideBar');
		if(sidebar.css('pointer-events')=='none'){
			body.removeClass('no-paddin-left');
			sidebar.removeClass('hide-sidebar').addClass('show-sidebar');
		}else{
			body.addClass('no-paddin-left');
			sidebar.addClass('hide-sidebar').removeClass('show-sidebar');
		}
    });

    $('.FormularioAjaxDatosCuenta').submit(function(e){
        e.preventDefault();

        var form=$(this);

        var tipo=form.attr('data-form');
        var accion=form.attr('action');
        var metodo=form.attr('method');
        var respuesta=form.children('.RespuestaAjaxDatosCuenta');

        var msjError="<script>swal('Ocurrió un error inesperado','Por favor recargue la página','error');</script>";
        var formdata = new FormData(this);
 
        var textoAlerta;
        if(tipo==="save"){
            textoAlerta="Los datos que enviaras quedaran almacenados en el sistema";
        }else if(tipo==="delete"){
            textoAlerta="Los datos serán eliminados completamente del sistema";
        }else if(tipo==="update"){
        	textoAlerta="Los datos del sistema serán actualizados";
        }else{
            textoAlerta="Quieres realizar la operación solicitada";
        }

        swal({
            title: "¿Estás seguro?",   
            text: textoAlerta,   
            type: "question",   
            showCancelButton: true,     
            confirmButtonText: "Aceptar",
            cancelButtonText: "Cancelar"
        }).then(function () {

            $.post(accion,
                {
                    privilegio: $("[name|='privilegio-up']").val(),
                    cuentacodigoup: $("[name|='CodigoCuenta-up']").val(),
                    cuentatipoup: $("[name|='tipoCuenta-up']").val(),
                    cuentausuarioup: $("[name|='usuario-up']").val(),
                    cuentaemailup: $("[name|='email-up']").val(),
                    cuentageneroup: $('input:radio[name=optionsGenero]:checked').val(),
                    cuentaestadoup: $("[name|='optionsEstado-up']").val(),
                    cuentaclave1up: $("[name|='newPassword1-up']").val(),
                    cuentaclave2up: $("[name|='newPassword2-up']").val(),
                    cuentaprivilegioup: $('input:radio[name=optionsPrivilegio]:checked').val(),
                    userlog: $("[name|='user-log']").val(),
                    passwordlog: $("[name|='password-log']").val()
                },function(respuestaActualizarCuenta){
                    respuesta.html(respuestaActualizarCuenta);
                }
              );
        });
    });

    $('.FormularioAjaxActualizar').submit(function(e){
        e.preventDefault();

        var form=$(this);

        var tipo=form.attr('data-form');
        var accion=form.attr('action');
        var metodo=form.attr('method');
        var respuesta=form.children('.RespuestaAjaxActualizar');

        var msjError="<script>swal('Ocurrió un error inesperado','Por favor recargue la página','error');</script>";
        var formdata = new FormData(this);
 
        var textoAlerta;
        if(tipo==="save"){
            textoAlerta="Los datos que enviaras quedaran almacenados en el sistema";
        }else if(tipo==="delete"){
            textoAlerta="Los datos serán eliminados completamente del sistema";
        }else if(tipo==="update"){
        	textoAlerta="Los datos del sistema serán actualizados";
        }else{
            textoAlerta="Quieres realizar la operación solicitada";
        }

        swal({
            title: "¿Estás seguro?",   
            text: textoAlerta,   
            type: "question",   
            showCancelButton: true,     
            confirmButtonText: "Aceptar",
            cancelButtonText: "Cancelar"
        }).then(function () {

            $.post(accion,
                {
                    cuentaup: $("[name|='cuenta-up']").val(),
                    dniup: $("[name|='dni-up']").val(),
                    nombreup: $("[name|='nombre-up']").val(),
                    apellidoup: $("[name|='apellido-up']").val(),
                    telefonoup: $("[name|='telefono-up']").val(),
                    direccionup: $("[name|='direccion-up']").val()
                },function(respuestaActualizar){
                    respuesta.html(respuestaActualizar);
                }
              );
        });
    });
    
    $('.FormularioAjaxEliminar').submit(function(e){
        e.preventDefault();

        var form=$(this);

        var tipo=form.attr('data-form');
        var accion=form.attr('action');
        var metodo=form.attr('method');
        var respuesta=form.children('.RespuestaAjaxEliminar');

        var msjError="<script>swal('Ocurrió un error inesperado','Por favor recargue la página','error');</script>";
        var formdata = new FormData(this);
 
        var textoAlerta;
        if(tipo==="save"){
            textoAlerta="Los datos que enviaras quedaran almacenados en el sistema";
        }else if(tipo==="delete"){
            textoAlerta="Los datos serán eliminados completamente del sistema";
        }else if(tipo==="update"){
        	textoAlerta="Los datos del sistema serán actualizados";
        }else{
            textoAlerta="Quieres realizar la operación solicitada";
        }

        swal({
            title: "¿Estás seguro?",   
            text: textoAlerta,   
            type: "question",   
            showCancelButton: true,     
            confirmButtonText: "Aceptar",
            cancelButtonText: "Cancelar"
        }).then(function () {

            $.post(accion,
                {
                    codigoDel: $("[name|='codigo-del']").val(),
                    privilegioAdmin: $("[name|='privilegio-admin']").val()
                },function(respuestaDelete){
                    respuesta.html(respuestaDelete);
                }
              );
        });
    });

	$('.FormularioAjax').submit(function(e){
        e.preventDefault();

        var form=$(this);

        var tipo=form.attr('data-form');
        var accion=form.attr('action');
        var metodo=form.attr('method');
        var respuesta=form.children('.RespuestaAjax');

        var msjError="<script>swal('Ocurrió un error inesperado','Por favor recargue la página','error');</script>";
        var formdata = new FormData(this);
 
        var textoAlerta;
        if(tipo==="save"){
            textoAlerta="Los datos que enviaras quedaran almacenados en el sistema";
        }else if(tipo==="delete"){
            textoAlerta="Los datos serán eliminados completamente del sistema";
        }else if(tipo==="update"){
        	textoAlerta="Los datos del sistema serán actualizados";
        }else{
            textoAlerta="Quieres realizar la operación solicitada";
        }

        swal({
            title: "¿Estás seguro?",   
            text: textoAlerta,   
            type: "question",   
            showCancelButton: true,     
            confirmButtonText: "Aceptar",
            cancelButtonText: "Cancelar"
        }).then(function () {

            $.post(accion,
                {
                    dni: $("[name|='dni-reg']").val(),
                    nombre: $("[name|='nombre-reg']").val(),
                    apellido: $("[name|='apellido-reg']").val(),
                    telefono: $("[name|='telefono-reg']").val(),
                    direccion: $("[name|='direccion-reg']").val(),
                    user: $("[name|='usuario-reg']").val(),
                    pass1: $("[name|='password1-reg']").val(),
                    pass2: $("[name|='password2-reg']").val(),
                    email: $("[name|='email-reg']").val(),
                    genero: $('input:radio[name=optionsGenero]:checked').val(),
                    privilegio: $('input:radio[name=optionsPrivilegio]:checked').val(),
                },function(respuestaS){
                    respuesta.html(respuestaS);
                }
              );
        });
    });

});
(function($){
    $(window).on("load",function(){
        $(".dashboard-sideBar-ct").mCustomScrollbar({
        	theme:"light-thin",
        	scrollbarPosition: "inside",
        	autoHideScrollbar: true,
        	scrollButtons: {enable: true}
        });
        $(".dashboard-contentPage, .Notifications-body").mCustomScrollbar({
        	theme:"dark-thin",
        	scrollbarPosition: "inside",
        	autoHideScrollbar: true,
        	scrollButtons: {enable: true}
        });
    });
})(jQuery);
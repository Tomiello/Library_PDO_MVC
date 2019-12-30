<script>
    $(document).ready(function(){
        $('.btn-exit-system').on('click', function(e){
            e.preventDefault();
            var Token=$(this).attr('href');
            swal({
                title: '¿Estás seguro?',
                text: "La sesion actual se cerrara y deberas iniciar sesion nuevamente.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#03A9F4',
                cancelButtonColor: '#F44336',
                confirmButtonText: '<i class="zmdi zmdi-run"></i> Si, Cerrar!',
                cancelButtonText: '<i class="zmdi zmdi-close-circle"></i> No, Cancelar!'
            }).then(function () {
                $.ajax({
                    url:'<?php echo SERVERURL; ?>ajax/loginAjax.php?Token='+Token,
                    success: function(data){
                        if(data=="true"){
                            //si se quiere cerrar sesion
                            window.location.href="<?php echo SERVERURL; ?>login/";
                        }else{
                            swal(
                                "Ocurrio un error",
                                "No se pudo cerrar la sesion",
                                "error"
                            );
                        }
                    }
                });
            });
        });
    });
</script>
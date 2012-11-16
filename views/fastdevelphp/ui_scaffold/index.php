<?php if(!is_writable("../views/")): $band = false; ?>
    <div class="albox errorbox">
    	<b>Error :</b> Folder views/ debe tener permisos de escritura
    </div>
<?php endif ?>

<?php if(!is_writable("../controllers/")): $band = false; ?>
    <div class="albox errorbox">
    	<b>Error :</b> Folder controllers/ debe tener permisos de escritura
    </div>
<?php endif ?>



<div class="simplebox" style="z-index: 720;">
<div class="titleh" style="z-index: 710;">
	<h3>Bienvenido al modulo de scaffold de Fastdevelphp.</h3>
</div>
<div class="body" style="z-index: 690;">
    <div class="st-form-line">
        <strong>Objetivo:</strong>
        <ul>
            <li>- auto generar los controladores</li>
            <li>- auto generar las vistas</li>
            <li>- funcionalidades de Crear, Editar, Eliminar, Listar</li>
        </ul>
        <br />
        <br />
        
        <strong>Ventajas:</strong>
        <ul>
            <li>- ahorra tiempo</li>
            <li>- codigo ordenado</li>
        </ul>
        <br />
        <br />
        
        <a href="<?php echo ROOT_PATH ?>fastdevelphp/ui_scaffold/create" title="Genrear nuevo">Generar nuevo</a>
    
    </div>
</div>
</div>



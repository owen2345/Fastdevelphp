<div class="albox informationbox">
	<h2>Bienvenidos a "FASTDEVELPHP".</h2>
</div><br />

<?php if(!is_writable(MODELS_PATH)): ?>
    <div class="albox errorbox">
    	<b>Error :</b> Folder Models/ debe tener permisos de escritura para poder generar los modelos automaticamente
    </div>
<?php endif ?>

<?php if(!is_writable(LOGS_PATH)): ?>
    <div class="albox errorbox">
    	<b>Error :</b> Folder logs/ debe tener permisos de escritura para guardar errores y acciones en el sistema
    </div>
<?php endif ?>


<?php if(!is_writable(UPLOADS_PATH)): ?>
    <div class="albox errorbox">
    	<b>Error :</b> Folder uploads/ debe tener permisos de escritura para poder subir archivos
    </div>
<?php endif ?>

<div class="simplebox">
    <div class="titleh"><h3>Pasos para empezar con su proyecto:</h3></div>
    <div class="body padding-left10">
        <br /><br />
        <ul>
            <li>- configurar los datos de la base de datos en su archivo confi/FD_config.php<br /><br /></li>
            <li>- crear controladores<br /><br /></li>
            <li>- crear vistas<br /><br /></li>
        </ul>
        <br />
        <br />Mas informacion en <a target="_blank" href="http://www.fastdevelphp.sysdecom.com">www.fastdevelphp.sysdecom.com</a>
        <br /><br />
        Tambien puedes usar nuestro <a target="_blank" href="<?php echo ROOT_PATH ?>fastdevelphp/ui_scaffold">Scaffold module</a> para generar controladores y vistas - CRUD
        <br /><br />
    </div>
</div>


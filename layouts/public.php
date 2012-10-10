<!DOCTYPE html>
<html lang="es">
    <head>
        <title><?php echo SITE_NAME ?></title>
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    	<meta name="author" content="Owen Peredo" />    	
        <link href="<?php echo CSS_PATH ?>reset.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo CSS_PATH ?>style.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="<?php echo JS_PATH ?>jquery.js"></script>
    </head>
    <body>
        <div id="main">
            <h1>FastDevelPHP</h1>
            <p><i>Framework de codigo libre para agilizar y estructurar el Desarrollo de Aplicaciones Web</i></p>
            <br /><br />
            <?php echo $contentView ?>
        </div>
    </body>
</html>
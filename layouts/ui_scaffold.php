<!DOCTYPE html>
<html lang="es">
    <head>
        <title><?php echo SITE_NAME ?></title>
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    	<meta name="author" content="Owen Peredo" />
        <link href="<?php echo CSS_PATH ?>scaffold/style.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="<?php echo JS_PATH ?>scaffold/jquery.js"></script>
        <script type="text/javascript" src="<?php echo JS_PATH ?>scaffold/validate.js"></script>
        <script>ROOT_PATH = "<?php echo ROOT_PATH ?>"</script>
        
    </head>
    <body>
        <div id="main">
            <div class="header">
                <h1>Scaffold module</h1>
                <p class="aright">Fastdevelphp - Framework de desarrollo agil.</p>
            </div>
            <div class="main_content">
                <?php echo $contentView ?>
            </div>
        </div>
    </body>
</html>
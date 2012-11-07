<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo SITE_NAME ?></title>
	
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH ?>fastdevelphp/reset.css" /> 
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH ?>fastdevelphp/root.css" /> 
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH ?>fastdevelphp/grid.css" /> 
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH ?>fastdevelphp/typography.css" /> 
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH ?>fastdevelphp/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH ?>fastdevelphp/jquery-plugin-base.css" />
    
    <!--[if IE 7]>	  <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH ?>fastdevelphp/ie7-style.css" />	<![endif]-->
    <script> var ROOT_PATH = "<?php echo ROOT_PATH ?>"; </script>
	<script type="text/javascript" src="<?php echo JS_PATH ?>fastdevelphp/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo JS_PATH ?>fastdevelphp/validate.js"></script>
</head>
<body class="scaffold">

    <div class="wrapper">

	<!-- START HEADER -->
        <div id="header">    
        
        	<!-- logo -->
        	<div class="logo">	<a href="index.html"><img src="<?php echo IMAGES_PATH ?>logo.png" width="112" height="35" alt="logo"/></a>	</div>
            
            <div class="clear"></div>
        </div>
        <!-- END HEADER -->
            
        <!-- START MAIN -->
        <div id="main">
            
            <!-- START PAGE -->
            <div id="page">
                <?php echo $contentView ?>
            </div>
            <!-- END PAGE -->
            
            <div class="clear"></div>
            </div>
            <!-- END MAIN -->
            
            <!-- START FOOTER -->
            <div id="footer">
            	<div class="left-column">&copy; Copyright 2012 - All rights reserved.</div>
                <div class="right-column">Backend template <a href="http://www.fastdevelphp.sysdecom.com" target="_blank">Fastdevelphp</a></div>
            </div>
            <!-- END FOOTER -->
    </div>
</body>
</html>
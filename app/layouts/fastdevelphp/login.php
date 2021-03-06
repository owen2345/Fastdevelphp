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
    
    <!--[if IE 7]>	<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH ?>fastdevelphp/ie7-style.css" />	<![endif]-->
    
	<script type="text/javascript" src="<?php echo JS_PATH ?>fastdevelphp/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo JS_PATH ?>fastdevelphp/toogle.js"></script>
</head>
<body>
    <div class="loginform">
    	<div class="title"> <a href="<?php echo ROOT_PATH ?>" title="go to home"><img src="<?php echo IMAGES_PATH ?>logo.png" width="112" height="35" /></a></div>
        <div class="body">
            <div class="albox informationbox">
            	<b>Information :</b> Please adapt to your needs. 
            	<a href="#" class="close tips" title="close">close</a>
            </div>
            <?php $this->loadPartialView("fastdevelphp/session_messages") ?>
            <form id="form1" name="form1" method="post" action="<?php echo ROOT_PATH ?>">
                <label class="log-lab">Username</label>
                <input name="textfield" type="text" class="login-input-user" value="Admin"/>
                <label class="log-lab">Password</label>
                <input name="textfield" type="password" class="login-input-pass" value="Password"/>
                <input type="submit" name="button" value="Login" class="button"/>
            </form>
        </div>
    </div>
    
    </div>
</body>
</html>